<?php

declare(strict_types=1);

namespace RohaM\LBCreator\event;

class LeaderboardEditEvent extends LeaderboardEvent {

    private array $oldData;

    public function __construct(array $newData, array $oldData) {
        parent::__construct($newData);
        $this->oldData = $oldData;
    }

    public function getOldData(): array {
        return $this->oldData;
    }

    public function getChanges(): array {
        $changes = [];
        foreach ($this->oldData as $key => $value) {
            if (isset($this->leaderboard[$key]) && $this->leaderboard[$key] !== $value) {
                $changes[$key] = [
                    'old' => $value,
                    'new' => $this->leaderboard[$key]
                ];
            }
        }
        return $changes;
    }
}