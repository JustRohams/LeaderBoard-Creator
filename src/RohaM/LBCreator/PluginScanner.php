<?php

declare(strict_types=1);

namespace RohaM\LBCreator;

use pocketmine\plugin\Plugin;
use ReflectionClass;

class PluginScanner {

    private Loader $plugin;
    private array $pluginInfo = [];

    public function __construct(Loader $plugin) {
        $this->plugin = $plugin;
    }

    public function scanPlugin(string $pluginName): array {
        if (isset($this->pluginInfo[$pluginName])) {
            return $this->pluginInfo[$pluginName];
        }

        $targetPlugin = $this->plugin->getServer()->getPluginManager()->getPlugin($pluginName);
        if ($targetPlugin === null || !$targetPlugin->isEnabled()) {
            return [];
        }

        $info = [
            "name" => $targetPlugin->getName(),
            "version" => $targetPlugin->getDescription()->getVersion(),
            "author" => $targetPlugin->getDescription()->getAuthors(),
            "main" => $targetPlugin->getDescription()->getMain(),
            "api" => $targetPlugin->getDescription()->getCompatibleApis(),
            "dependencies" => $targetPlugin->getDescription()->getDepend(),
            "stats" => [],
            "classes" => [],
            "methods" => []
        ];

        $reflection = new ReflectionClass($targetPlugin);
        $info["classes"][] = $reflection->getName();

        foreach ($reflection->getMethods() as $method) {
            $info["methods"][$method->getName()] = [
                "parameters" => $method->getNumberOfParameters(),
                "static" => $method->isStatic(),
                "public" => $method->isPublic()
            ];
        }

        foreach ($reflection->getProperties() as $prop) {
            $type = $prop->getType();
            if ($type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
                try {
                    $prop->setAccessible(true);
                    $value = $prop->getValue($targetPlugin);
                    if (is_object($value)) {
                        $classRef = new ReflectionClass($value);
                        $info["classes"][] = $classRef->getName();
                        foreach ($classRef->getMethods() as $method) {
                            if (str_starts_with($method->getName(), "get")) {
                                $statName = lcfirst(substr($method->getName(), 3));
                                $info["stats"][] = $statName;
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    continue;
                }
            }
        }

        $info["stats"] = array_unique($info["stats"]);
        $this->pluginInfo[$pluginName] = $info;
        return $info;
    }

    public function getActivePlugins(): array {
        $plugins = [];
        foreach ($this->plugin->getServer()->getPluginManager()->getPlugins() as $plugin) {
            if ($plugin->isEnabled()) {
                $plugins[] = $plugin->getName();
            }
        }
        return $plugins;
    }

    public function hasStat(string $pluginName, string $stat): bool {
        $info = $this->scanPlugin($pluginName);
        return in_array($stat, $info["stats"], true);
    }

    public function getStats(string $pluginName): array {
        $info = $this->scanPlugin($pluginName);
        return $info["stats"] ?? [];
    }
}