<?php

declare(strict_types=1);

namespace RohaM\LBCreator\form;

use pocketmine\player\Player;
use RohaM\LBCreator\Loader;

class SelectStatForm extends BaseForm {

    private string $pluginName;
    private int $limit;

    public function __construct(Loader $plugin, Player $player, string $pluginName, int $limit = 10) {
        parent::__construct($plugin, $player);
        $this->pluginName = $pluginName;
        $this->limit = $limit;
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            return;
        }

        $stats = $this->plugin->getLeaderboardManager()->getAvailableStats($this->pluginName);
        if (!isset($stats[$data])) {
            return;
        }

        $stat = $stats[$data];
        $title = $this->plugin->getConfigManager()->get("default_title", "RohaM");
        $end = $this->plugin->getConfigManager()->get("default_end_text", "");

        try {
            $id = $this->plugin->getLeaderboardManager()->createLeaderboard(
                $player, $this->pluginName, $stat, $this->limit, $title, $end
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
        $stats = $this->plugin->getLeaderboardManager()->getAvailableStats($this->pluginName);
        $buttons = [];
        foreach ($stats as $stat) {
            $buttons[] = ["text" => "§a" . ucfirst($stat)];
        }

        return [
            "type" => "form",
            "title" => $this->plugin->getMessages()->get("form_title_select_stat"),
            "content" => "§7Select statistic for §e{$this->pluginName}§7:",
            "buttons" => $buttons
        ];
    }
}