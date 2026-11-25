<?php

declare(strict_types=1);

namespace RohaM\LBCreator\task;

use pocketmine\scheduler\Task;
use RohaM\LBCreator\database\BackupManager;

class BackupTask extends Task {

    private BackupManager $backupManager;

    public function __construct(BackupManager $backupManager) {
        $this->backupManager = $backupManager;
    }

    public function onRun(): void {
        $this->backupManager->createBackup();
    }
}