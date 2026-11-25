<?php

declare(strict_types=1);

namespace RohaM\LBCreator\cache;

class DataCache {

    private array $cache = [];
    private array $expiry = [];

    public function set(string $key, $data, int $ttl = 60): void {
        $this->cache[$key] = $data;
        $this->expiry[$key] = time() + $ttl;
    }

    public function get(string $key) {
        if ($this->has($key)) {
            return $this->cache[$key];
        }
        return null;
    }

    public function has(string $key): bool {
        if (!isset($this->cache[$key])) {
            return false;
        }
        if ($this->expiry[$key] < time()) {
            unset($this->cache[$key], $this->expiry[$key]);
            return false;
        }
        return true;
    }

    public function remove(string $key): void {
        unset($this->cache[$key], $this->expiry[$key]);
    }

    public function clear(): void {
        $this->cache = [];
        $this->expiry = [];
    }

    public function clean(): void {
        foreach ($this->expiry as $key => $time) {
            if ($time < time()) {
                unset($this->cache[$key], $this->expiry[$key]);
            }
        }
    }

    public function size(): int {
        return count($this->cache);
    }

    public function keys(): array {
        return array_keys($this->cache);
    }
}