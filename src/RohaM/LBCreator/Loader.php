<?php

declare(strict_types=1);

namespace RohaM\LBCreator;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use RohaM\LBCreator\command\LBCCommand;
use RohaM\LBCreator\listener\PlayerJoinListener;
use RohaM\LBCreator\listener\PlayerQuitListener;
use RohaM\LBCreator\listener\WorldChangeListener;
use RohaM\LBCreator\config\ConfigManager;
use RohaM\LBCreator\language\Messages;
use RohaM\LBCreator\language\LanguageManager;
use RohaM\LBCreator\database\LeaderboardStorage;
use RohaM\LBCreator\database\BackupManager;
use RohaM\LBCreator\task\UpdateTask;
use RohaM\LBCreator\task\DeferredUpdateTask;
use RohaM\LBCreator\task\BackupTask;
use RohaM\LBCreator\task\InitTask;
use RohaM\LBCreator\exception\LeaderboardException;

class Loader extends PluginBase {

    private static self $instance;
    private LeaderboardManager $leaderboardManager;
    private ConfigManager $configManager;
    private Messages $messages;
    private LanguageManager $languageManager;
    private LeaderboardStorage $storage;
    private BackupManager $backupManager;
    private PluginScanner $pluginScanner;

    public function onEnable(): void {
        self::$instance = $this;
        
        $this->createDirectories();
        
        $this->saveDefaultConfig();
        $this->saveResource("messages.yml", false);
        
        $this->configManager = new ConfigManager($this);
        $this->languageManager = new LanguageManager($this);
        $this->messages = new Messages($this);
        $this->pluginScanner = new PluginScanner($this);
        $this->storage = new LeaderboardStorage($this);
        $this->backupManager = new BackupManager($this);
        
        $this->leaderboardManager = new LeaderboardManager($this);
        $this->leaderboardManager->init();
        
        $this->getServer()->getCommandMap()->register("lbc", new LBCCommand($this));
        
        $this->getServer()->getPluginManager()->registerEvents(new PlayerJoinListener($this->leaderboardManager), $this);
        $this->getServer()->getPluginManager()->registerEvents(new PlayerQuitListener($this->leaderboardManager), $this);
        $this->getServer()->getPluginManager()->registerEvents(new WorldChangeListener($this->leaderboardManager), $this);
        
        $this->scheduleTasks();
        
        $this->getLogger()->info($this->messages->get("startup"));
    }

    private function createDirectories(): void {
        $dirs = [
            $this->getDataFolder(),
            $this->getDataFolder() . "leaderboards_data/",
            $this->getDataFolder() . "backups/",
            $this->getDataFolder() . "cache/"
        ];
        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
        }
    }

    private function scheduleTasks(): void {
        $interval = $this->configManager->get("update_interval", 5);
        $async = $this->configManager->get("async_enabled", true);
        
        if ($async) {
            $this->getScheduler()->scheduleRepeatingTask(
                new DeferredUpdateTask($this->leaderboardManager),
                20 * $interval
            );
        } else {
            $this->getScheduler()->scheduleRepeatingTask(
                new UpdateTask($this->leaderboardManager),
                20 * $interval
            );
        }
        
        if ($this->configManager->get("database.backup", true)) {
            $backupInterval = $this->configManager->get("database.backup_interval", 3600);
            $this->getScheduler()->scheduleRepeatingTask(
                new BackupTask($this->backupManager),
                20 * $backupInterval
            );
        }
        
        $this->getScheduler()->scheduleTask(new InitTask($this->leaderboardManager));
    }

    public function onDisable(): void {
        if (isset($this->leaderboardManager)) {
            $this->leaderboardManager->shutdown();
        }
        if (isset($this->messages)) {
            $this->getLogger()->info($this->messages->get("shutdown"));
        }
    }

    public static function getInstance(): self {
        return self::$instance;
    }

    public function getLeaderboardManager(): LeaderboardManager {
        return $this->leaderboardManager;
    }

    public function getConfigManager(): ConfigManager {
        return $this->configManager;
    }

    public function getMessages(): Messages {
        return $this->messages;
    }

    public function getLanguageManager(): LanguageManager {
        return $this->languageManager;
    }

    public function getStorage(): LeaderboardStorage {
        return $this->storage;
    }

    public function getBackupManager(): BackupManager {
        return $this->backupManager;
    }

    public function getPluginScanner(): PluginScanner {
        return $this->pluginScanner;
    }

    public function reload(): void {
        $this->configManager->reload();
        $this->messages->reload();
        $this->getLogger()->info($this->messages->get("reloaded"));
    }
}