<?php

declare(strict_types=1);

namespace RohaM\LBCreator\util;

class MathHelper {

    public static function clamp(int $value, int $min, int $max): int {
        return max($min, min($max, $value));
    }

    public static function round(float $value, int $precision = 2): float {
        return round($value, $precision);
    }

    public static function average(array $numbers): float {
        if (empty($numbers)) {
            return 0;
        }
        return array_sum($numbers) / count($numbers);
    }

    public static function median(array $numbers): float {
        sort($numbers);
        $count = count($numbers);
        if ($count === 0) {
            return 0;
        }
        if ($count % 2 === 1) {
            return $numbers[$count / 2];
        }
        return ($numbers[$count / 2 - 1] + $numbers[$count / 2]) / 2;
    }
}