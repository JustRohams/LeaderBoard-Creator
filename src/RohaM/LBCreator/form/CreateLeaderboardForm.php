<?php

declare(strict_types=1);

namespace RohaM\LBCreator\form;

use pocketmine\player\Player;
use RohaM\LBCreator\Loader;

class CreateLeaderboardForm extends BaseForm {

    private string $pluginName;
    private string $stat;
    private int $limit;

    public function __construct(Loader $plugin, Player $player, string $pluginName, string $stat, int $limit = 10) {
        parent::__construct($plugin, $player);
        $this->pluginName = $pluginName;
        $this->stat = $stat;
        $this->limit = max(1, min(50, $limit));
    }

    public function handleResponse(Player $player, $data): void {
        if (!is_array($data)) {
            return;
        }

        $default = (string) $this->plugin->getConfigManager()->get("default_title", "RohaM");
        $title = trim((string) ($data[0] ?? ""));
        if ($title === "") {
            $title = $default;
        }
        $end = (string) ($data[1] ?? "");
        $limit = (int) ($data[2] ?? $this->limit);

        if ($limit < 1 || $limit > 50) {
            $player->sendMessage($this->plugin->getMessages()->get("error_limit_range"));
            return;
        }

        try {
            $id = $this->plugin->getLeaderboardManager()->createLeaderboard(
                $player, $this->pluginName, $this->stat, $limit, $title, $end
            );
            $player->sendMessage($this->plugin->getMessages()->get("created", [
                "{id}" => $id,
                "{plugin}" => $this->pluginName
            ]));
        } catch (\Exception $e) {
            $player->sendMessage("§c" . $e->getMessage());
        }
    }

    public function jsonSerialize(): array {
        return [
            "type" => "custom_form",
            "title" => $this->plugin->getMessages()->get("form_title_create"),
            "content" => [
                ["type" => "input", "text" => "Title", "placeholder" => "Enter title", "default" => (string) $this->plugin->getConfigManager()->get("default_title", "RohaM")],
                ["type" => "input", "text" => "End Text", "placeholder" => "Enter end text (optional)", "default" => ""],
                ["type" => "slider", "text" => "Limit", "min" => 1, "max" => 50, "step" => 1, "default" => $this->limit]
            ]
        ];
    }
}
