<?php

declare(strict_types=1);

namespace RohaM\LBCreator\form;

use pocketmine\player\Player;

class MainMenuForm extends BaseForm {

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            return;
        }

        switch ($data) {
            case 0:
                (new SelectPluginForm($this->plugin, $player))->send();
                break;
            case 1:
                (new ListLeaderboardsForm($this->plugin, $player))->send();
                break;
            case 2:
                $player->sendMessage($this->plugin->getMessages()->get("command_usage"));
                $player->sendMessage("§e/lbc delete <id>");
                break;
            case 3:
                $player->sendMessage($this->plugin->getMessages()->get("command_usage"));
                $player->sendMessage("§e/lbc teleport <id>");
                break;
            case 4:
                $player->sendMessage($this->plugin->getMessages()->get("command_usage"));
                $player->sendMessage("§e/lbc edit <id> <limit|title|end|stat> <value>");
                break;
            case 5:
                $this->plugin->reload();
                $player->sendMessage($this->plugin->getMessages()->get("reloaded"));
                break;
        }
    }

    public function jsonSerialize(): array {
        return [
            "type" => "form",
            "title" => $this->plugin->getMessages()->get("form_title_main"),
            "content" => "§7Select an option below:",
            "buttons" => [
                ["text" => "§aCreate New Leaderboard"],
                ["text" => "§bList All Leaderboards"],
                ["text" => "§cDelete Leaderboard"],
                ["text" => "§eTeleport Leaderboard"],
                ["text" => "§6Edit Leaderboard"],
                ["text" => "§dReload Plugin"]
            ]
        ];
    }
}
