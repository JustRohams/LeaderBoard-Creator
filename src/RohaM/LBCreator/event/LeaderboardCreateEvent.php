<?php

declare(strict_types=1);

namespace RohaM\LBCreator\event;

use pocketmine\player\Player;

class LeaderboardCreateEvent extends LeaderboardEvent {

    private Player $creator;

    public function __construct(array $leaderboard, Player $creator) {
        parent::__construct($leaderboard);
        $this->creator = $creator;
    }

    public function getCreator(): Player {
        return $this->creator;
    }
}