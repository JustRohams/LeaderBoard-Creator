<?php

declare(strict_types=1);

namespace RohaM\LBCreator\event;

use pocketmine\event\Event;

abstract class LeaderboardEvent extends Event {

    protected array $leaderboard;

    public function __construct(array $leaderboard) {
        $this->leaderboard = $leaderboard;
    }

    public function getLeaderboard(): array {
        return $this->leaderboard;
    }

    public function getLeaderboardId(): int {
        return $this->leaderboard['id'] ?? 0;
    }

    public function getPluginName(): string {
        return $this->leaderboard['plugin'] ?? '';
    }

    public function getStat(): string {
        return $this->leaderboard['stat'] ?? '';
    }

    public function getLimit(): int {
        return $this->leaderboard['limit'] ?? 10;
    }
}