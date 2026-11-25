<?php

declare(strict_types=1);

namespace RohaM\LBCreator\listener;

use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use RohaM\LBCreator\LeaderboardManager;

class WorldChangeListener implements Listener {

    private LeaderboardManager $manager;

    public function __construct(LeaderboardManager $manager) {
        $this->manager = $manager;
    }

    /**
     * PocketMine-MP 5 has no PlayerWorldChangeEvent,
     * so world changes are detected through EntityTeleportEvent.
     *
     * @priority MONITOR
     * @ignoreCancelled
     */
    public function onTeleport(EntityTeleportEvent $event): void {
        if (!$event->getEntity() instanceof Player) {
            return;
        }
        if ($event->getFrom()->getWorld() === $event->getTo()->getWorld()) {
            return;
        }
        $this->manager->updateAll();
    }
}
