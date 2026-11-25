<?php

declare(strict_types=1);

namespace RohaM\LBCreator\language;

use pocketmine\utils\Config;
use RohaM\LBCreator\Loader;

class LanguageManager {

    private Loader $plugin;
    private array $languages = [];
    private string $defaultLanguage = "en";

    public function __construct(Loader $plugin) {
        $this->plugin = $plugin;
        $this->loadLanguages();
    }

    private function loadLanguages(): void {
        $langDir = $this->plugin->getDataFolder() . "languages/";
        if (!is_dir($langDir)) {
            mkdir($langDir, 0777, true);
        }
        
        $files = glob($langDir . "*.yml");
        foreach ($files as $file) {
            $name = basename($file, ".yml");
            $this->languages[$name] = new Config($file, Config::YAML);
        }
        
        if (empty($this->languages)) {
            $this->createDefaultLanguage();
        }
    }

    private function createDefaultLanguage(): void {
        $langFile = $this->plugin->getDataFolder() . "languages/en.yml";
        $config = new Config($langFile, Config::YAML, $this->getDefaultMessages());
        $config->save();
        $this->languages["en"] = $config;
    }

    private function getDefaultMessages(): array {
        return [
            "startup" => "§a LBCreator §7by §eRohaM §aenabled!",
            "shutdown" => "§cLBCreator disabled!",
            "created" => "§a Leaderboard #§e{id}§a created for §e{plugin}§a!",
            "deleted" => "§a Leaderboard #§e{id}§a deleted!",
            "error_plugin_not_found" => "§c Plugin '{plugin}' not found!",
            "error_invalid_stat" => "§c Statistic '{stat}' not found!",
            "error_limit_range" => "§c Limit must be between 1 and 50!"
        ];
    }

    public function getLanguage(string $lang = "en"): ?Config {
        return $this->languages[$lang] ?? $this->languages[$this->defaultLanguage] ?? null;
    }
}