<?php

declare(strict_types=1);

namespace RohaM\LBCreator\command\subcommand;

use pocketmine\player\Player;
use RohaM\LBCreator\Loader;

class DeleteSubCommand extends SubCommand {

    public function __construct(Loader $plugin) {
        parent::__construct($plugin, "delete", "Delete a leaderboard", "/lbc delete <id>", "lbcreator.delete");
    }

    public function execute(Player $player, array $args): void {
        if (empty($args)) {
            $player->sendMessage("§cUsage: /lbc delete <id>");
            return;
        }

        $id = (int) $args[0];
        if ($this->plugin->getLeaderboardManager()->deleteLeaderboard($id, $player)) {
            $player->sendMessage($this->plugin->getMessages()->get("deleted", ["{id}" => $id]));
        } else {
            $player->sendMessage($this->plugin->getMessages()->get("error_not_found"));
        }
    }
}