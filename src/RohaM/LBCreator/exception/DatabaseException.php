<?php

declare(strict_types=1);

namespace RohaM\LBCreator\exception;

class DatabaseException extends LeaderboardException {

    public function __construct(string $message) {
        parent::__construct($message);
    }
}