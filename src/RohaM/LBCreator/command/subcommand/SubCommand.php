<?php

declare(strict_types=1);

namespace RohaM\LBCreator\command\subcommand;

use pocketmine\player\Player;
use RohaM\LBCreator\Loader;

abstract class SubCommand {

    protected Loader $plugin;
    protected string $name;
    protected string $description;
    protected string $usage;
    protected string $permission;

    public function __construct(Loader $plugin, string $name, string $description, string $usage, string $permission) {
        $this->plugin = $plugin;
        $this->name = $name;
        $this->description = $description;
        $this->usage = $usage;
        $this->permission = $permission;
    }

    abstract public function execute(Player $player, array $args): void;

    public function getName(): string {
        return $this->name;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getUsage(): string {
        return $this->usage;
    }

    public function getPermission(): string {
        return $this->permission;
    }
}