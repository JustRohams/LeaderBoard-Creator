<?php

declare(strict_types=1);

namespace RohaM\LBCreator\provider;

use pocketmine\Server;

abstract class BaseProvider implements PluginProviderInterface {

    protected string $pluginName;

    public function __construct(string $pluginName) {
        $this->pluginName = $pluginName;
    }

    public function getPluginName(): string {
        return $this->pluginName;
    }

    protected function getPlugin() {
        return Server::getInstance()->getPluginManager()->getPlugin($this->pluginName);
    }

    protected function isPluginEnabled(): bool {
        $plugin = $this->getPlugin();
        return $plugin !== null && $plugin->isEnabled();
    }

    public function getPlayerStat(string $playerName, string $stat): ?int {
        $data = $this->getTopPlayers($stat, 1);
        return $data[$playerName] ?? null;
    }
}