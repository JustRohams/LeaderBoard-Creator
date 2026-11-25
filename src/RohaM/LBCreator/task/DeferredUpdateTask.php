<?php

declare(strict_types=1);

namespace RohaM\LBCreator\task;

use pocketmine\scheduler\Task;
use RohaM\LBCreator\LeaderboardManager;

class DeferredUpdateTask extends Task {

    private LeaderboardManager $manager;

    public function __construct(LeaderboardManager $manager) {
        $this->manager = $manager;
    }

    public function onRun(): void {
        $this->manager->updateAll();
    }
}