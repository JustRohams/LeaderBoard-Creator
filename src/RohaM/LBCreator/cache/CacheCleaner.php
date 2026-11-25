<?php

declare(strict_types=1);

namespace RohaM\LBCreator\cache;

use pocketmine\scheduler\Task;
use RohaM\LBCreator\Loader;

class CacheCleaner extends Task {

    private CacheManager $manager;

    public function __construct(CacheManager $manager) {
        $this->manager = $manager;
    }

    public function onRun(): void {
        $this->manager->cleanAll();
    }
}