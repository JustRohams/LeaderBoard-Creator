<?php

declare(strict_types=1);

namespace RohaM\LBCreator\command\subcommand;

use pocketmine\player\Player;
use RohaM\LBCreator\Loader;

class ListSubCommand extends SubCommand {

    public function __construct(Loader $plugin) {
        parent::__construct($plugin, "list", "List all leaderboards", "/lbc list", "lbcreator.list");
    }

    public function execute(Player $player, array $args): void {
        $leaderboards = $this->plugin->getLeaderboardManager()->getLeaderboards();
        $count = count($leaderboards);

        if ($count === 0) {
            $player->sendMessage($this->plugin->getMessages()->get("list_empty"));
            return;
        }

        $player->sendMessage($this->plugin->getMessages()->get("list_header", ["{count}" => $count]));

        foreach ($leaderboards as $lb) {
            $pos = $lb['position'];
            $player->sendMessage($this->plugin->getMessages()->get("list_entry", [
                "{id}" => $lb['id'],
                "{plugin}" => $lb['plugin'],
                "{stat}" => $lb['stat'],
                "{limit}" => $lb['limit']
            ]) . " " . $this->plugin->getMessages()->get("list_position", [
                "{world}" => $pos['world'],
                "{x}" => round($pos['x'], 1),
                "{y}" => round($pos['y'], 1),
                "{z}" => round($pos['z'], 1)
            ]));
        }
    }
}