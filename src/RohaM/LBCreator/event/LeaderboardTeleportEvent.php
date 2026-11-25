<?php

declare(strict_types=1);

namespace RohaM\LBCreator\event;

use pocketmine\player\Player;

class LeaderboardTeleportEvent extends LeaderboardEvent {

    private Player $teleporter;

    public function __construct(array $leaderboard, Player $teleporter) {
        parent::__construct($leaderboard);
        $this->teleporter = $teleporter;
    }

    public function getTeleporter(): Player {
        return $this->teleporter;
    }

    public function getNewPosition(): array {
        return $this->leaderboard['position'] ?? [];
    }
}