<?php

declare(strict_types=1);

namespace RohaM\LBCreator\command\subcommand;

use pocketmine\player\Player;
use RohaM\LBCreator\Loader;

class ReloadSubCommand extends SubCommand {

    public function __construct(Loader $plugin) {
        parent::__construct($plugin, "reload", "Reload the plugin configuration", "/lbc reload", "lbcreator.reload");
    }

    public function execute(Player $player, array $args): void {
        $this->plugin->reload();
        $player->sendMessage($this->plugin->getMessages()->get("reloaded"));
    }
}