<?php

declare(strict_types=1);

namespace RohaM\LBCreator\util;

class TimeHelper {

    public static function getTimestamp(): int {
        return time();
    }

    public static function getDateTime(string $format = "Y-m-d H:i:s"): string {
        return date($format);
    }

    public static function getTimeAgo(int $timestamp): string {
        $diff = time() - $timestamp;
        if ($diff < 60) {
            return $diff . " seconds ago";
        } elseif ($diff < 3600) {
            return floor($diff / 60) . " minutes ago";
        } elseif ($diff < 86400) {
            return floor($diff / 3600) . " hours ago";
        } elseif ($diff < 604800) {
            return floor($diff / 86400) . " days ago";
        } else {
            return floor($diff / 604800) . " weeks ago";
        }
    }

    public static function isValidTimestamp(int $timestamp): bool {
        return $timestamp > 0 && $timestamp <= time() + 86400;
    }

    public static function formatDuration(int $seconds): string {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        return sprintf("%02d:%02d:%02d", $hours, $minutes, $secs);
    }
}