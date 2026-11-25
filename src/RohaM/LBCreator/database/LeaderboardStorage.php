<?php

declare(strict_types=1);

namespace RohaM\LBCreator\database;

use pocketmine\utils\Config;
use RohaM\LBCreator\Loader;
use RohaM\LBCreator\util\TextFormatter;

class LeaderboardStorage {

    private Loader $plugin;
    private Config $data;
    private string $dataPath;
    private string $type;

    public function __construct(Loader $plugin) {
        $this->plugin = $plugin;
        $this->dataPath = $plugin->getDataFolder();
        $this->type = $plugin->getConfigManager()->get("database.type", "yaml");
        
        if ($this->type === "json") {
            $this->data = new Config($this->dataPath . "leaderboards.json", Config::JSON);
        } else {
            $this->data = new Config($this->dataPath . "leaderboards.yml", Config::YAML);
        }
        
        if (!$this->data->exists("leaderboards")) {
            $this->data->set("leaderboards", []);
        }
        if (!$this->data->exists("next_id")) {
            $this->data->set("next_id", 1);
        }
        $this->data->save();
    }

    public function loadAll(): array {
        return $this->data->get("leaderboards", []);
    }

    public function saveAll(array $leaderboards): void {
        $this->data->set("leaderboards", $leaderboards);
        $this->data->save();
    }

    public function getNextId(): int {
        $id = (int) $this->data->get("next_id", 1);
        $this->data->set("next_id", $id + 1);
        $this->data->save();
        return $id;
    }

    public function saveLeaderboardFile(array $lb, array $data): void {
        $fileName = $lb['plugin'] . "_" . $lb['stat'] . "_" . $lb['id'] . "." . ($this->type === "json" ? "json" : "yml");
        $filePath = $this->dataPath . "leaderboards_data/" . $fileName;
        
        if (!is_dir($this->dataPath . "leaderboards_data/")) {
            mkdir($this->dataPath . "leaderboards_data/", 0777, true);
        }

        $formatted = TextFormatter::formatForFile($lb, $data);
        
        if ($this->type === "json") {
            file_put_contents($filePath, json_encode($formatted, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $config = new Config($filePath, Config::YAML, $formatted);
            $config->save();
        }
    }

    public function loadLeaderboardFile(int $id): ?array {
        foreach ($this->loadAll() as $lb) {
            if ((int) $lb['id'] === $id) {
                $fileName = $lb['plugin'] . "_" . $lb['stat'] . "_" . $id . "." . ($this->type === "json" ? "json" : "yml");
                $filePath = $this->dataPath . "leaderboards_data/" . $fileName;
                
                if (file_exists($filePath)) {
                    if ($this->type === "json") {
                        $content = file_get_contents($filePath);
                        return json_decode($content, true);
                    } else {
                        $config = new Config($filePath, Config::YAML);
                        return $config->getAll();
                    }
                }
                return null;
            }
        }
        return null;
    }

    public function getLeaderboardFiles(): array {
        $dir = $this->dataPath . "leaderboards_data/";
        if (!is_dir($dir)) {
            return [];
        }
        $files = scandir($dir);
        return array_filter($files, function($file) {
            return $file !== "." && $file !== "..";
        });
    }

    public function deleteLeaderboardFile(int $id): bool {
        foreach ($this->loadAll() as $lb) {
            if ((int) $lb['id'] === $id) {
                $fileName = $lb['plugin'] . "_" . $lb['stat'] . "_" . $id . "." . ($this->type === "json" ? "json" : "yml");
                $filePath = $this->dataPath . "leaderboards_data/" . $fileName;
                if (file_exists($filePath)) {
                    return unlink($filePath);
                }
                return true;
            }
        }
        return false;
    }
}