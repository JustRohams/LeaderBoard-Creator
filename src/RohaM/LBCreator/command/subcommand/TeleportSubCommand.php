<?php

declare(strict_types=1);

namespace RohaM\LBCreator\command\subcommand;

use pocketmine\player\Player;
use RohaM\LBCreator\Loader;

class TeleportSubCommand extends SubCommand {

    public function __construct(Loader $plugin) {
        parent::__construct($plugin, "teleport", "Teleport a leaderboard to your location", "/lbc teleport <id>", "lbcreator.teleport");
    }

    public function execute(Player $player, array $args): void {
        if (empty($args)) {
            $player->sendMessage("§cUsage: /lbc teleport <id>");
            return;
        }

        $id = (int) $args[0];
        if ($this->plugin->getLeaderboardManager()->teleportLeaderboard($id, $player)) {
            $player->sendMessage($this->plugin->getMessages()->get("teleported", ["{id}" => $id]));
        } else {
            $player->sendMessage($this->plugin->getMessages()->get("error_not_found"));
        }
    }
}