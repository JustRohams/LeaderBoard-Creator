<?php

declare(strict_types=1);

namespace RohaM\LBCreator\database;

use pocketmine\utils\Config;
use RohaM\LBCreator\Loader;

class YamlStorage implements DatabaseInterface {

    private Loader $plugin;
    private Config $data;

    public function __construct(Loader $plugin) {
        $this->plugin = $plugin;
        $this->data = new Config($plugin->getDataFolder() . "leaderboards.yml", Config::YAML, [
            "leaderboards" => [],
            "next_id" => 1
        ]);
    }

    public function loadAll(): array {
        return $this->data->get("leaderboards", []);
    }

    public function saveAll(array $data): void {
        $this->data->set("leaderboards", $data);
        $this->data->save();
    }

    public function getNextId(): int {
        $id = (int) $this->data->get("next_id", 1);
        $this->data->set("next_id", $id + 1);
        $this->data->save();
        return $id;
    }

    public function saveLeaderboardFile(array $lb, array $data): void {
        $fileName = $lb['plugin'] . "_" . $lb['stat'] . "_" . $lb['id'] . ".yml";
        $filePath = $this->plugin->getDataFolder() . "leaderboards_data/" . $fileName;
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0777, true);
        }
        $config = new Config($filePath, Config::YAML, $data);
        $config->save();
    }

    public function loadLeaderboardFile(int $id): ?array {
        foreach ($this->loadAll() as $lb) {
            if ((int) $lb['id'] === $id) {
                $fileName = $lb['plugin'] . "_" . $lb['stat'] . "_" . $id . ".yml";
                $filePath = $this->plugin->getDataFolder() . "leaderboards_data/" . $fileName;
                if (file_exists($filePath)) {
                    $config = new Config($filePath, Config::YAML);
                    return $config->getAll();
                }
            }
        }
        return null;
    }

    public function deleteLeaderboardFile(int $id): bool {
        foreach ($this->loadAll() as $lb) {
            if ((int) $lb['id'] === $id) {
                $fileName = $lb['plugin'] . "_" . $lb['stat'] . "_" . $id . ".yml";
                $filePath = $this->plugin->getDataFolder() . "leaderboards_data/" . $fileName;
                if (file_exists($filePath)) {
                    return unlink($filePath);
                }
                return true;
            }
        }
        return false;
    }
}