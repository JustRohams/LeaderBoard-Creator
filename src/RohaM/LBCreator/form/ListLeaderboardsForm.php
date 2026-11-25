<?php

declare(strict_types=1);

namespace RohaM\LBCreator\form;

use pocketmine\player\Player;
use RohaM\LBCreator\Loader;

class ListLeaderboardsForm extends BaseForm {

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            return;
        }

        $leaderboards = $this->plugin->getLeaderboardManager()->getLeaderboards();
        if (!isset($leaderboards[$data])) {
            return;
        }

        $lb = $leaderboards[$data];
        $player->sendMessage("§6=== Leaderboard #{$lb['id']} ===");
        $player->sendMessage("§7Plugin: §f{$lb['plugin']}");
        $player->sendMessage("§7Stat: §f{$lb['stat']}");
        $player->sendMessage("§7Limit: §f{$lb['limit']}");
        $player->sendMessage("§7Title: §f{$lb['title']}");
        $player->sendMessage("§7End: §f{$lb['end']}");
        $player->sendMessage("§7Position: §f{$lb['position']['world']} ({$lb['position']['x']}, {$lb['position']['y']}, {$lb['position']['z']})");
        $player->sendMessage("§7Created: §f" . date("Y-m-d H:i:s", $lb['created_at']));
        $player->sendMessage("§7Last Update: §f" . date("Y-m-d H:i:s", $lb['last_update']));
    }

    public function jsonSerialize(): array {
        $leaderboards = $this->plugin->getLeaderboardManager()->getLeaderboards();
        $buttons = [];
        foreach ($leaderboards as $lb) {
            $buttons[] = ["text" => "§e#{$lb['id']} §f- {$lb['plugin']} §7({$lb['stat']})"];
        }

        return [
            "type" => "form",
            "title" => $this->plugin->getMessages()->get("form_title_list"),
            "content" => "§7Select a leaderboard to view details:",
            "buttons" => $buttons
        ];
    }
}