<?php

declare(strict_types=1);

namespace RohaM\LBCreator\form;

use pocketmine\player\Player;
use RohaM\LBCreator\Loader;

class EditTitleForm extends BaseForm {

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

        $title = trim((string) ($data[0] ?? ""));
        if ($title === "") {
            $player->sendMessage("§cTitle cannot be empty!");
            return;
        }

        if ($this->plugin->getLeaderboardManager()->editLeaderboard($this->id, ["title" => $title])) {
            $player->sendMessage($this->plugin->getMessages()->get("edited", ["{id}" => $this->id]));
        }
    }

    public function jsonSerialize(): array {
        return [
            "type" => "custom_form",
            "title" => "§l§6Edit Title",
            "content" => [
                ["type" => "input", "text" => "New Title", "placeholder" => "Enter new title", "default" => (string) ($this->lb['title'] ?? "RohaM")]
            ]
        ];
    }
}
