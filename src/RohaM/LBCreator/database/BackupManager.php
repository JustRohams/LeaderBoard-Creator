<?php

declare(strict_types=1);

namespace RohaM\LBCreator\database;

use pocketmine\utils\Config;
use RohaM\LBCreator\Loader;
use RohaM\LBCreator\util\TimeHelper;

class BackupManager {

    private Loader $plugin;
    private string $backupPath;

    public function __construct(Loader $plugin) {
        $this->plugin = $plugin;
        $this->backupPath = $plugin->getDataFolder() . "backups/";
        
        if (!is_dir($this->backupPath)) {
            mkdir($this->backupPath, 0777, true);
        }
    }

    public function createBackup(): string {
        $timestamp = TimeHelper::getTimestamp();
        $fileName = "leaderboards_backup_" . $timestamp . ".yml";
        $filePath = $this->backupPath . $fileName;

        $data = $this->plugin->getStorage()->loadAll();
        $config = new Config($filePath, Config::YAML, [
            "backup_date" => date("Y-m-d H:i:s"),
            "backup_timestamp" => time(),
            "count" => count($data),
            "leaderboards" => $data
        ]);
        $config->save();

        $this->cleanOldBackups();
        return $fileName;
    }

    public function cleanOldBackups(int $keep = 5): void {
        $files = glob($this->backupPath . "leaderboards_backup_*.yml");
        if (count($files) <= $keep) {
            return;
        }

        usort($files, function($a, $b) {
            return filemtime($a) - filemtime($b);
        });

        $toDelete = array_slice($files, 0, count($files) - $keep);
        foreach ($toDelete as $file) {
            unlink($file);
        }
    }

    public function restoreBackup(string $fileName): bool {
        $filePath = $this->backupPath . $fileName;
        if (!file_exists($filePath)) {
            return false;
        }

        $config = new Config($filePath, Config::YAML);
        $data = $config->get("leaderboards", []);
        if (empty($data)) {
            return false;
        }

        $this->plugin->getStorage()->saveAll($data);
        return true;
    }

    public function getBackups(): array {
        $files = glob($this->backupPath . "leaderboards_backup_*.yml");
        $backups = [];
        foreach ($files as $file) {
            $config = new Config($file, Config::YAML);
            $backups[] = [
                "name" => basename($file),
                "date" => $config->get("backup_date", "Unknown"),
                "count" => $config->get("count", 0),
                "size" => filesize($file)
            ];
        }
        usort($backups, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        return $backups;
    }
}