<?php

declare(strict_types=1);

namespace RohaM\LBCreator\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use RohaM\LBCreator\LeaderboardManager;

class PlayerJoinListener implements Listener {

    private LeaderboardManager $manager;

    public function __construct(LeaderboardManager $manager) {
        $this->manager = $manager;
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $this->manager->updateAll();
    }
}