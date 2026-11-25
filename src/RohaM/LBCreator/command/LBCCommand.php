<?php

declare(strict_types=1);

namespace RohaM\LBCreator\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use RohaM\LBCreator\Loader;
use RohaM\LBCreator\command\subcommand\CreateSubCommand;
use RohaM\LBCreator\command\subcommand\DeleteSubCommand;
use RohaM\LBCreator\command\subcommand\TeleportSubCommand;
use RohaM\LBCreator\command\subcommand\EditSubCommand;
use RohaM\LBCreator\command\subcommand\ListSubCommand;
use RohaM\LBCreator\command\subcommand\ReloadSubCommand;
use Roham\LBCreator\command\subcommand\HelpSubCommand;
use RohaM\LBCreator\form\MainMenuForm;

class LBCCommand extends Command {

    private Loader $plugin;
    private array $subCommands = [];

    public function __construct(Loader $plugin) {
        parent::__construct("lbc", "Manage dynamic leaderboards", "/lbc <subcommand> [args]");
        $this->setPermission("lbcreator.command");
        $this->plugin = $plugin;
        $this->registerSubCommands();
    }

    private function registerSubCommands(): void {
        $this->subCommands = [
            "create" => new CreateSubCommand($this->plugin),
            "delete" => new DeleteSubCommand($this->plugin),
            "teleport" => new TeleportSubCommand($this->plugin),
            "edit" => new EditSubCommand($this->plugin),
            "list" => new ListSubCommand($this->plugin),
            "reload" => new ReloadSubCommand($this->plugin),
            "help" => new HelpSubCommand($this->plugin),
        ];
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if (!$sender instanceof Player) {
            $sender->sendMessage("§cThis command can only be used in-game!");
            return;
        }

        if (!$this->testPermission($sender)) {
            return;
        }

        if (empty($args)) {
            $form = new MainMenuForm($this->plugin, $sender);
            $form->send();
            return;
        }

        $subCommandName = strtolower($args[0]);
        if (!isset($this->subCommands[$subCommandName])) {
            $sender->sendMessage($this->plugin->getMessages()->get("command_usage"));
            $this->sendHelp($sender);
            return;
        }

        $subCommand = $this->subCommands[$subCommandName];
        if (!$sender->hasPermission($subCommand->getPermission())) {
            $sender->sendMessage($this->plugin->getMessages()->get("error_permission"));
            return;
        }

        $subCommand->execute($sender, array_slice($args, 1));
    }

    private function sendHelp(Player $player): void {
        $player->sendMessage($this->plugin->getMessages()->get("command_help_header"));
        foreach ($this->subCommands as $name => $cmd) {
            $player->sendMessage($this->plugin->getMessages()->get("command_help_entry", [
                "{cmd}" => $name,
                "{desc}" => $cmd->getDescription()
            ]));
        }
    }

    public function getSubCommands(): array {
        return $this->subCommands;
    }
}