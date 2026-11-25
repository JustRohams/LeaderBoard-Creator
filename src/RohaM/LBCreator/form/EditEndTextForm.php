<?php

declare(strict_types=1);

namespace RohaM\LBCreator\form;

use pocketmine\player\Player;
use RohaM\LBCreator\Loader;

class EditEndTextForm extends BaseForm {

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

        $end = (string) ($data[0] ?? "");

        if ($this->plugin->getLeaderboardManager()->editLeaderboard($this->id, ["end" => $end])) {
            $player->sendMessage($this->plugin->getMessages()->get("edited", ["{id}" => $this->id]));
        }
    }

    public function jsonSerialize(): array {
        return [
            "type" => "custom_form",
            "title" => "§l§6Edit End Text",
            "content" => [
                ["type" => "input", "text" => "New End Text", "placeholder" => "Enter new end text", "default" => (string) ($this->lb['end'] ?? "")]
            ]
        ];
    }
}
