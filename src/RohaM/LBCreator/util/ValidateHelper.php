<?php

declare(strict_types=1);

namespace RohaM\LBCreator\util;

class ValidateHelper {

    public static function isInteger($value): bool {
        return is_int($value) || (is_string($value) && ctype_digit($value));
    }

    public static function isPositiveInteger($value): bool {
        return self::isInteger($value) && (int) $value > 0;
    }

    public static function isInRange(int $value, int $min, int $max): bool {
        return $value >= $min && $value <= $max;
    }

    public static function isStringLengthValid(string $value, int $min = 1, int $max = PHP_INT_MAX): bool {
        $length = strlen($value);
        return $length >= $min && $length <= $max;
    }

    public static function isPluginNameValid(string $name): bool {
        return preg_match('/^[a-zA-Z0-9_-]{3,32}$/', $name) === 1;
    }

    public static function isStatNameValid(string $stat): bool {
        return preg_match('/^[a-zA-Z0-9_]{2,32}$/', $stat) === 1;
    }

    public static function isWorldNameValid(string $world): bool {
        return preg_match('/^[a-zA-Z0-9_\-]{1,64}$/', $world) === 1;
    }

    public static function sanitizeString(string $input): string {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    public static function sanitizeFileName(string $fileName): string {
        return preg_replace('/[^a-zA-Z0-9_\-.]/', '_', $fileName);
    }
}