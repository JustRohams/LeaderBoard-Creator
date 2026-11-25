<?php

declare(strict_types=1);

namespace RohaM\LBCreator\event;

use pocketmine\player\Player;

class LeaderboardDeleteEvent extends LeaderboardEvent {

    private ?Player $deleter;

    public function __construct(array $leaderboard, ?Player $deleter = null) {
        parent::__construct($leaderboard);
        $this->deleter = $deleter;
    }

    public function getDeleter(): ?Player {
        return $this->deleter;
    }
}