<?php

declare(strict_types=1);

namespace RohaM\LBCreator\form;

use pocketmine\player\Player;
use RohaM\LBCreator\Loader;

class EditLimitForm extends BaseForm {

    private int $id;
    private ?array $lb;

    public function __construct(Loader $plugin, Player $player, int $id) {
        parent::__construct($plugin, $player);
        $this->id = $id;
        $this->lb = $plugin->getLeaderboardManager()->getLeaderboard($id);
    }

    public function send(): void {
        if ($this->lb === null) {
            $this->player->sendMessage($this->plugin->getMessages()->get("error_not_found"));
            return;
        }
        parent::send();
    }

    public function handleResponse(Player $player, $data): void {
        if (!is_array($data) || $this->lb === null) {
            return;
        }

        $limit = (int) ($data[0] ?? 0);
        if ($limit < 1 || $limit > 50) {
            $player->sendMessage($this->plugin->getMessages()->get("error_limit_range"));
            return;
        }

        if ($this->plugin->getLeaderboardManager()->editLeaderboard($this->id, ["limit" => $limit])) {
            $player->sendMessage($this->plugin->getMessages()->get("edited", ["{id}" => $this->id]));
        }
    }

    public function jsonSerialize(): array {
        return [
            "type" => "custom_form",
            "title" => "§l§6Edit Limit",
            "content" => [
                ["type" => "slider", "text" => "New Limit", "min" => 1, "max" => 50, "step" => 1, "default" => max(1, min(50, (int) ($this->lb['limit'] ?? 10)))]
            ]
        ];
    }
}
