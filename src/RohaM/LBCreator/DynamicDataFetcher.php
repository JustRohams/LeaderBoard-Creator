<?php

declare(strict_types=1);

namespace RohaM\LBCreator;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\plugin\Plugin;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use RohaM\LBCreator\util\ReflectionHelper;
use RohaM\LBCreator\util\ValidateHelper;

class DynamicDataFetcher {

    private Loader $plugin;
    private array $statCache = [];
    private array $methodCache = [];
    private array $classCache = [];

    public function __construct(Loader $plugin) {
        $this->plugin = $plugin;
    }

    public function fetchTopPlayers(string $pluginName, string $stat, int $limit = 10): array {
        $targetPlugin = Server::getInstance()->getPluginManager()->getPlugin($pluginName);
        if ($targetPlugin === null || !$targetPlugin->isEnabled()) {
            return [];
        }

        $players = Server::getInstance()->getOnlinePlayers();
        $scores = [];

        foreach ($players as $player) {
            $value = $this->extractStat($targetPlugin, $player, $stat);
            if ($value !== null && $value > 0) {
                $scores[$player->getName()] = $value;
            }
        }

        arsort($scores);
        return array_slice($scores, 0, $limit, true);
    }

    public function getAvailableStats(string $pluginName): array {
        if (isset($this->statCache[$pluginName])) {
            return $this->statCache[$pluginName];
        }

        $targetPlugin = Server::getInstance()->getPluginManager()->getPlugin($pluginName);
        if ($targetPlugin === null || !$targetPlugin->isEnabled()) {
            return [];
        }

        $stats = [];
        $exclude = $this->plugin->getConfigManager()->get("scanner.exclude_methods", []);
        $maxDepth = $this->plugin->getConfigManager()->get("scanner.max_depth", 3);

        $reflection = new ReflectionClass($targetPlugin);
        $stats = array_merge($stats, $this->scanMethods($reflection, $exclude));

        foreach ($reflection->getProperties() as $prop) {
            $type = $prop->getType();
            if ($type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
                $className = $type->getName();
                try {
                    $classRef = new ReflectionClass($className);
                    $stats = array_merge($stats, $this->scanMethods($classRef, $exclude, $maxDepth - 1));
                } catch (\Throwable $e) {
                    continue;
                }
            }
        }

        foreach ($reflection->getMethods() as $method) {
            if ($method->isStatic() && str_starts_with($method->getName(), "get")) {
                $statName = lcfirst(substr($method->getName(), 3));
                if (!in_array($statName, $exclude)) {
                    $stats[] = $statName;
                }
            }
        }

        $stats = array_unique($stats);
        sort($stats);
        $this->statCache[$pluginName] = $stats;
        return $stats;
    }

    private function scanMethods(ReflectionClass $reflection, array $exclude, int $depth = 3): array {
        if ($depth <= 0) {
            return [];
        }

        $stats = [];
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $name = $method->getName();
            if (str_starts_with($name, "get") && $method->getNumberOfParameters() <= 1) {
                $statName = lcfirst(substr($name, 3));
                if (!in_array($statName, $exclude) && !in_array($statName, $stats)) {
                    $stats[] = $statName;
                }
            }
        }

        foreach ($reflection->getProperties() as $prop) {
            $type = $prop->getType();
            if ($type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
                $className = $type->getName();
                try {
                    $classRef = new ReflectionClass($className);
                    $stats = array_merge($stats, $this->scanMethods($classRef, $exclude, $depth - 1));
                } catch (\Throwable $e) {
                    continue;
                }
            }
        }

        return $stats;
    }

    private function extractStat(Plugin $targetPlugin, Player $player, string $stat): ?int {
        $methods = [
            "get{$stat}",
            "get" . ucfirst($stat),
            "getPlayer{$stat}",
            "getPlayer" . ucfirst($stat),
            "get{$stat}Data",
            "get" . ucfirst($stat) . "Data",
            "getPlayerData" . ucfirst($stat),
            "get" . ucfirst($stat) . "ForPlayer",
            "getPlayer" . ucfirst($stat) . "Value",
        ];

        $reflection = new ReflectionClass($targetPlugin);
        foreach ($methods as $method) {
            if ($reflection->hasMethod($method)) {
                $methodRef = $reflection->getMethod($method);
                if ($methodRef->getNumberOfParameters() <= 1) {
                    try {
                        $methodRef->setAccessible(true);
                        if ($methodRef->getNumberOfParameters() === 0) {
                            $result = $methodRef->invoke($targetPlugin);
                        } else {
                            $result = $methodRef->invoke($targetPlugin, $player);
                        }
                        $value = $this->extractValue($result, $player);
                        if ($value !== null) {
                            return $value;
                        }
                    } catch (\Throwable $e) {
                        continue;
                    }
                }
            }
        }

        foreach ($reflection->getProperties() as $prop) {
            $type = $prop->getType();
            if ($type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
                $className = $type->getName();
                try {
                    $classRef = new ReflectionClass($className);
                    foreach ($methods as $method) {
                        if ($classRef->hasMethod($method)) {
                            $methodRef = $classRef->getMethod($method);
                            if ($methodRef->getNumberOfParameters() <= 1) {
                                try {
                                    $prop->setAccessible(true);
                                    $obj = $prop->getValue($targetPlugin);
                                    if (is_object($obj)) {
                                        $methodRef->setAccessible(true);
                                        if ($methodRef->getNumberOfParameters() === 0) {
                                            $result = $methodRef->invoke($obj);
                                        } else {
                                            $result = $methodRef->invoke($obj, $player);
                                        }
                                        $value = $this->extractValue($result, $player);
                                        if ($value !== null) {
                                            return $value;
                                        }
                                    }
                                } catch (\Throwable $e) {
                                    continue;
                                }
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    continue;
                }
            }
        }

        foreach ($methods as $method) {
            if ($reflection->hasMethod($method) && $reflection->getMethod($method)->isStatic()) {
                try {
                    $methodRef = $reflection->getMethod($method);
                    $methodRef->setAccessible(true);
                    if ($methodRef->getNumberOfParameters() === 0) {
                        $result = $methodRef->invoke(null);
                    } else {
                        $result = $methodRef->invoke(null, $player);
                    }
                    $value = $this->extractValue($result, $player);
                    if ($value !== null) {
                        return $value;
                    }
                } catch (\Throwable $e) {
                    continue;
                }
            }
        }

        return null;
    }

    private function extractValue($result, Player $player): ?int {
        if (is_numeric($result)) {
            return (int) $result;
        }

        if (is_object($result)) {
            $methods = ["get", "getValue", "getData", "getStat", "getStats", "getPlayerData", "get{$player->getName()}"];
            foreach ($methods as $method) {
                if (method_exists($result, $method)) {
                    try {
                        if ($method === "get" && method_exists($result, "has")) {
                            if ($result->has($player->getName())) {
                                $val = $result->get($player->getName());
                                if (is_numeric($val)) {
                                    return (int) $val;
                                }
                            }
                        } else {
                            $val = $result->$method();
                            if (is_numeric($val)) {
                                return (int) $val;
                            }
                        }
                    } catch (\Throwable $e) {
                        continue;
                    }
                }
            }

            foreach (get_object_vars($result) as $propName => $propValue) {
                if (is_numeric($propValue) && strpos($propName, $player->getName()) !== false) {
                    return (int) $propValue;
                }
            }
        }

        if (is_array($result)) {
            if (isset($result[$player->getName()]) && is_numeric($result[$player->getName()])) {
                return (int) $result[$player->getName()];
            }
            if (isset($result[strtolower($player->getName())]) && is_numeric($result[strtolower($player->getName())])) {
                return (int) $result[strtolower($player->getName())];
            }
        }

        return null;
    }
}