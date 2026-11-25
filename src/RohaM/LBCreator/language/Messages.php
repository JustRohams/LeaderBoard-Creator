<?php

declare(strict_types=1);

namespace RohaM\LBCreator\language;

use pocketmine\utils\Config;
use RohaM\LBCreator\Loader;

class Messages {

    private Loader $plugin;
    private Config $config;
    private array $messages = [];

    public function __construct(Loader $plugin) {
        $this->plugin = $plugin;
        $this->config = new Config($plugin->getDataFolder() . "messages.yml", Config::YAML);
        $this->messages = $this->config->getAll();
    }

    public function get(string $key, array $replace = []): string {
        $message = $this->messages[$key] ?? "Missing message: " . $key;
        foreach ($replace as $search => $replaceWith) {
            $message = str_replace((string) $search, (string) $replaceWith, $message);
        }
        return $message;
    }

    public function reload(): void {
        $this->config->reload();
        $this->messages = $this->config->getAll();
    }
}