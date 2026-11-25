<?php

declare(strict_types=1);

namespace RohaM\LBCreator;

use pocketmine\player\Player;
use pocketmine\math\Vector3;
use pocketmine\world\World;
use RohaM\LBCreator\util\FloatingTextHelper;
use RohaM\LBCreator\util\TextFormatter;
use RohaM\LBCreator\cache\DataCache;
use RohaM\LBCreator\event\LeaderboardCreateEvent;
use RohaM\LBCreator\event\LeaderboardDeleteEvent;
use RohaM\LBCreator\event\LeaderboardUpdateEvent;
use RohaM\LBCreator\event\LeaderboardTeleportEvent;
use RohaM\LBCreator\event\LeaderboardEditEvent;
use RohaM\LBCreator\exception\LeaderboardException;
use RohaM\LBCreator\exception\PluginNotFoundException;
use RohaM\LBCreator\exception\StatNotFoundException;
use RohaM\LBCreator\exception\ValidationException;

class LeaderboardManager {

    private Loader $plugin;
    private array $leaderboards = [];
    private DataCache $cache;
    private DynamicDataFetcher $fetcher;
    private array $updatesPending = [];

    public function __construct(Loader $plugin) {
        $this->plugin = $plugin;
        $this->cache = new DataCache();
        $this->fetcher = new DynamicDataFetcher($plugin);
    }

    public function init(): void {
        $this->leaderboards = $this->plugin->getStorage()->loadAll();
        foreach ($this->leaderboards as &$lb) {
            $this->spawnLeaderboard($lb);
        }
        $this->plugin->getLogger()->info("Loaded " . count($this->leaderboards) . " leaderboards");
    }

    public function shutdown(): void {
        foreach ($this->leaderboards as $lb) {
            FloatingTextHelper::remove($lb['id']);
        }
        $this->plugin->getStorage()->saveAll($this->leaderboards);
    }

    public function getTopPlayers(string $pluginName, string $stat, int $limit = 10): array {
        $cacheKey = $this->buildCacheKey($pluginName, $stat, $limit);
        
        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        $data = $this->fetcher->fetchTopPlayers($pluginName, $stat, $limit);
        $cacheTime = $this->plugin->getConfigManager()->get("cache_time", 60);
        $this->cache->set($cacheKey, $data, $cacheTime);
        
        return $data;
    }

    private function buildCacheKey(string $plugin, string $stat, int $limit): string {
        return md5($plugin . "_" . $stat . "_" . $limit);
    }

    public function getAvailableStats(string $pluginName): array {
        return $this->fetcher->getAvailableStats($pluginName);
    }

    public function createLeaderboard(Player $player, string $pluginName, string $stat, int $limit = 10, string $title = "RohaM", string $end = ""): int {
        $targetPlugin = $this->plugin->getServer()->getPluginManager()->getPlugin($pluginName);
        if ($targetPlugin === null || !$targetPlugin->isEnabled()) {
            throw new PluginNotFoundException($this->plugin->getMessages()->get("error_plugin_not_found", ["{plugin}" => $pluginName]));
        }

        $stats = $this->getAvailableStats($pluginName);
        if (empty($stats)) {
            throw new StatNotFoundException($this->plugin->getMessages()->get("error_invalid_stat", ["{stat}" => $stat, "{plugin}" => $pluginName]));
        }
        
        if (!in_array($stat, $stats, true)) {
            throw new StatNotFoundException($this->plugin->getMessages()->get("error_invalid_stat", ["{stat}" => $stat, "{plugin}" => $pluginName]));
        }

        if ($limit < 1 || $limit > 50) {
            throw new ValidationException($this->plugin->getMessages()->get("error_limit_range"));
        }

        $id = $this->plugin->getStorage()->getNextId();
        $pos = $player->getPosition();
        $world = $pos->getWorld();

        $lb = [
            "id" => $id,
            "plugin" => $pluginName,
            "stat" => $stat,
            "limit" => $limit,
            "title" => $title,
            "end" => $end,
            "position" => [
                "world" => $world->getFolderName(),
                "x" => $pos->getX(),
                "y" => $pos->getY() + 2.5,
                "z" => $pos->getZ()
            ],
            "created_at" => time(),
            "last_update" => time()
        ];

        $this->leaderboards[] = $lb;
        $this->saveData();

        $this->spawnLeaderboard($lb);

        $event = new LeaderboardCreateEvent($lb, $player);
        $event->call();

        return $id;
    }

    public function deleteLeaderboard(int $id, ?Player $player = null): bool {
        foreach ($this->leaderboards as $i => $lb) {
            if ((int) $lb["id"] === $id) {
                FloatingTextHelper::remove($id);
                $this->plugin->getStorage()->deleteLeaderboardFile($id);
                array_splice($this->leaderboards, $i, 1);
                $this->saveData();

                $event = new LeaderboardDeleteEvent($lb, $player);
                $event->call();
                
                return true;
            }
        }
        return false;
    }

    public function teleportLeaderboard(int $id, Player $player): bool {
        foreach ($this->leaderboards as $i => $lb) {
            if ((int) $lb["id"] === $id) {
                $pos = $player->getPosition();
                $this->leaderboards[$i]["position"] = [
                    "world" => $pos->getWorld()->getFolderName(),
                    "x" => $pos->getX(),
                    "y" => $pos->getY() + 2.5,
                    "z" => $pos->getZ()
                ];
                $this->saveData();
                $this->spawnLeaderboard($this->leaderboards[$i]);

                $event = new LeaderboardTeleportEvent($this->leaderboards[$i], $player);
                $event->call();
                
                return true;
            }
        }
        return false;
    }

    public function editLeaderboard(int $id, array $changes): bool {
        foreach ($this->leaderboards as $i => $lb) {
            if ((int) $lb["id"] === $id) {
                $old = $this->leaderboards[$i];

                if (isset($changes["limit"]) && ((int) $changes["limit"] < 1 || (int) $changes["limit"] > 50)) {
                    return false;
                }
                if (isset($changes["stat"]) && !in_array($changes["stat"], $this->getAvailableStats($lb["plugin"]), true)) {
                    return false;
                }

                foreach ($changes as $key => $value) {
                    if (in_array($key, ["limit", "title", "end", "stat"], true)) {
                        $this->leaderboards[$i][$key] = $key === "limit" ? (int) $value : $value;
                    }
                }
                if (isset($changes["stat"]) && $changes["stat"] !== $old["stat"]) {
                    $this->plugin->getStorage()->deleteLeaderboardFile($id);
                }
                $this->saveData();
                $this->spawnLeaderboard($this->leaderboards[$i]);

                $event = new LeaderboardEditEvent($this->leaderboards[$i], $old);
                $event->call();
                
                return true;
            }
        }
        return false;
    }

    public function getLeaderboards(): array {
        return $this->leaderboards;
    }

    public function getLeaderboard(int $id): ?array {
        foreach ($this->leaderboards as $lb) {
            if ((int) $lb["id"] === $id) {
                return $lb;
            }
        }
        return null;
    }

    public function spawnLeaderboard(array &$lb): void {
        $worldName = $lb['position']['world'] ?? null;
        if (!$worldName) {
            return;
        }
        
        $world = $this->plugin->getServer()->getWorldManager()->getWorldByName($worldName);
        if (!$world) {
            return;
        }

        $pos = new Vector3(
            (float) $lb['position']['x'],
            (float) $lb['position']['y'],
            (float) $lb['position']['z']
        );

        try {
            $data = $this->getTopPlayers($lb['plugin'], $lb['stat'], $lb['limit']);
            $text = TextFormatter::format($lb, $data, $this->plugin->getConfigManager());
            FloatingTextHelper::spawn($lb['id'], $world, $pos, $text);
            $lb['last_update'] = time();
        } catch (\Exception $e) {
            $this->plugin->getLogger()->warning("Failed to spawn leaderboard #{$lb['id']}: " . $e->getMessage());
            $errorText = "§cError loading leaderboard\n§7" . $e->getMessage();
            FloatingTextHelper::spawn($lb['id'], $world, $pos, $errorText);
        }
    }

    public function updateAll(): void {
        foreach ($this->leaderboards as &$lb) {
            $this->updateLeaderboard($lb);
        }
    }

    public function updateLeaderboard(array &$lb): void {
        try {
            $data = $this->getTopPlayers($lb['plugin'], $lb['stat'], $lb['limit']);
            $text = TextFormatter::format($lb, $data, $this->plugin->getConfigManager());
            
            FloatingTextHelper::update($lb['id'], $text);
            $lb['last_update'] = time();
            
            $this->plugin->getStorage()->saveLeaderboardFile($lb, $data);
        } catch (\Exception $e) {
            $this->plugin->getLogger()->warning("Failed to update leaderboard #{$lb['id']}: " . $e->getMessage());
        }
    }

    private function saveData(): void {
        $this->plugin->getStorage()->saveAll($this->leaderboards);
    }

    public function clearAll(): void {
        foreach ($this->leaderboards as $lb) {
            FloatingTextHelper::remove($lb['id']);
        }
        $this->leaderboards = [];
        $this->saveData();
    }

    public function count(): int {
        return count($this->leaderboards);
    }

    public function countByPlugin(string $pluginName): int {
        $count = 0;
        foreach ($this->leaderboards as $lb) {
            if ($lb['plugin'] === $pluginName) {
                $count++;
            }
        }
        return $count;
    }
}