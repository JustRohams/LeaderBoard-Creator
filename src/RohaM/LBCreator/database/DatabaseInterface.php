<?php

declare(strict_types=1);

namespace RohaM\LBCreator\database;

interface DatabaseInterface {

    public function loadAll(): array;
    public function saveAll(array $data): void;
    public function getNextId(): int;
    public function saveLeaderboardFile(array $lb, array $data): void;
    public function loadLeaderboardFile(int $id): ?array;
    public function deleteLeaderboardFile(int $id): bool;
}