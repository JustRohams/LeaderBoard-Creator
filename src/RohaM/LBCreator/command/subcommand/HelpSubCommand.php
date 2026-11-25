<?php

declare(strict_types=1);

namespace RohaM\LBCreator\command\subcommand;

use pocketmine\player\Player;
use RohaM\LBCreator\Loader;

class HelpSubCommand extends SubCommand {

    public function __construct(Loader $plugin) {
        parent::__construct($plugin, "help", "Show help information", "/lbc help", "lbcreator.help");
    }

    public function execute(Player $player, array $args): void {
        $player->sendMessage($this->plugin->getMessages()->get("command_help_header"));
        $commands = $this->plugin->getServer()->getCommandMap()->getCommand("lbc");
        if ($commands !== null) {
            foreach ($commands->getSubCommands() as $name => $cmd) {
                $player->sendMessage($this->plugin->getMessages()->get("command_help_entry", [
                    "{cmd}" => $name,
                    "{desc}" => $cmd->getDescription()
                ]));
            }
        }
    }
}