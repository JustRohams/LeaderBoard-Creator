<?php

declare(strict_types=1);

namespace RohaM\LBCreator\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use RohaM\LBCreator\LeaderboardManager;

class PlayerQuitListener implements Listener {

    private LeaderboardManager $manager;

    public function __construct(LeaderboardManager $manager) {
        $this->manager = $manager;
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void {
        $this->manager->updateAll();
    }
}