<?php

declare(strict_types=1);

namespace RohaM\LBCreator\language;

use pocketmine\utils\Config;
use RohaM\LBCreator\Loader;

class LanguageLoader {

    private Loader $plugin;

    public function __construct(Loader $plugin) {
        $this->plugin = $plugin;
    }

    public function loadLanguage(string $lang = "en"): Config {
        $langFile = $this->plugin->getDataFolder() . "languages/{$lang}.yml";
        if (file_exists($langFile)) {
            return new Config($langFile, Config::YAML);
        }
        return $this->createDefaultLanguage($lang);
    }

    private function createDefaultLanguage(string $lang): Config {
        $langFile = $this->plugin->getDataFolder() . "languages/{$lang}.yml";
        $defaults = $this->getDefaultMessages();
        $config = new Config($langFile, Config::YAML, $defaults);
        $config->save();
        return $config;
    }

    private function getDefaultMessages(): array {
        return [
            "startup" => "§aLBCreator §7by §eRohaM §aenabled!",
            "shutdown" => "§cLBCreator disabled!",
            "created" => "§a Leaderboard #§e{id}§a created for §e{plugin}§a!",
            "deleted" => "§a Leaderboard #§e{id}§a deleted!",
            "teleported" => "§a Leaderboard #§e{id}§a teleported!",
            "edited" => "§a Leaderboard #§e{id}§a edited!",
            "reloaded" => "§aConfiguration reloaded!",
            "error_plugin_not_found" => "§c Plugin '{plugin}' not found!",
            "error_invalid_stat" => "§c Statistic '{stat}' not found!",
            "error_limit_range" => "§cLimit must be between 1 and 50!",
            "error_not_found" => "§c Leaderboard not found!",
            "error_permission" => "§c You don't have permission!",
            "command_usage" => "§c▸ Usage: /lbc <subcommand> [args]",
            "command_help_header" => "§6 §lLBCreator Help §r§6",
            "command_help_entry" => "§e/lbc {cmd} §7- {desc}",
            "list_header" => "§6 §lLeaderboards §r§6({count})§r",
            "list_entry" => "§e#{id} §7- §f{plugin} §7({stat}) §a{limit} players",
            "list_empty" => "§cNo leaderboards found!",
        ];
    }
}