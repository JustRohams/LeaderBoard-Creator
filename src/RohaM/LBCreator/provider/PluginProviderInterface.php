<?php

declare(strict_types=1);

namespace RohaM\LBCreator\provider;

interface PluginProviderInterface {

    public function getPluginName(): string;
    public function getAvailableStats(): array;
    public function getTopPlayers(string $stat, int $limit = 10): array;
    public function getPlayerStat(string $playerName, string $stat): ?int;
}