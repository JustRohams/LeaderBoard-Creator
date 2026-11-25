<?php

declare(strict_types=1);

namespace RohaM\LBCreator\config;

use pocketmine\utils\Config;
use RohaM\LBCreator\Loader;

class ConfigManager {

    private Loader $plugin;
    private Config $config;
    private array $defaults;

    public function __construct(Loader $plugin) {
        $this->plugin = $plugin;
        $this->config = new Config($plugin->getDataFolder() . "config.yml", Config::YAML, DefaultConfig::get());
        $this->defaults = DefaultConfig::get();
    }

    public function get(string $key, $default = null) {
        return $this->config->get($key, $default ?? $this->defaults[$key] ?? null);
    }

    public function set(string $key, $value): void {
        $this->config->set($key, $value);
        $this->config->save();
    }

    public function getAll(): array {
        return $this->config->getAll();
    }

    public function reload(): void {
        $this->config->reload();
    }
}