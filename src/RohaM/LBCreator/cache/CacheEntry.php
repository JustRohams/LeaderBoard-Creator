<?php

declare(strict_types=1);

namespace RohaM\LBCreator\cache;

class CacheEntry {

    private $data;
    private int $created;
    private int $ttl;

    public function __construct($data, int $ttl = 60) {
        $this->data = $data;
        $this->created = time();
        $this->ttl = $ttl;
    }

    public function getData() {
        return $this->data;
    }

    public function isExpired(): bool {
        return time() > $this->created + $this->ttl;
    }

    public function getCreated(): int {
        return $this->created;
    }

    public function getTTL(): int {
        return $this->ttl;
    }

    public function getRemainingTime(): int {
        $remaining = $this->created + $this->ttl - time();
        return $remaining > 0 ? $remaining : 0;
    }
}