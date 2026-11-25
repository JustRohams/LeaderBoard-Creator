<?php

declare(strict_types=1);

namespace RohaM\LBCreator\event;

class LeaderboardUpdateEvent extends LeaderboardEvent {

    public function __construct(array $leaderboard) {
        parent::__construct($leaderboard);
    }
}