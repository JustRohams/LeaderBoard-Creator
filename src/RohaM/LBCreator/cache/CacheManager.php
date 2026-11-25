<?php

declare(strict_types=1);

namespace RohaM\LBCreator\cache;

class CacheManager {

    private array $caches = [];

    public function get(string $name): ?DataCache {
        return $this->caches[$name] ?? null;
    }

    public function create(string $name): DataCache {
        $this->caches[$name] = new DataCache();
        return $this->caches[$name];
    }

    public function remove(string $name): void {
        unset($this->caches[$name]);
    }

    public function clearAll(): void {
        foreach ($this->caches as $cache) {
            $cache->clear();
        }
    }

    public function cleanAll(): void {
        foreach ($this->caches as $cache) {
            $cache->clean();
        }
    }

    public function getSize(string $name): int {
        return isset($this->caches[$name]) ? $this->caches[$name]->size() : 0;
    }

    public function getAllSizes(): array {
        $sizes = [];
        foreach ($this->caches as $name => $cache) {
            $sizes[$name] = $cache->size();
        }
        return $sizes;
    }
}