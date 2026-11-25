<?php

declare(strict_types=1);

namespace RohaM\LBCreator\util;

class FileHelper {

    public static function createDirectory(string $path): bool {
        if (!is_dir($path)) {
            return mkdir($path, 0777, true);
        }
        return true;
    }

    public static function deleteDirectory(string $path): bool {
        if (!is_dir($path)) {
            return false;
        }
        $files = array_diff(scandir($path), ['.', '..']);
        foreach ($files as $file) {
            $filePath = $path . DIRECTORY_SEPARATOR . $file;
            if (is_dir($filePath)) {
                self::deleteDirectory($filePath);
            } else {
                unlink($filePath);
            }
        }
        return rmdir($path);
    }

    public static function getFileSize(string $path): string {
        if (!file_exists($path)) {
            return "0 B";
        }
        $size = filesize($path);
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }
        return round($size, 2) . " " . $units[$i];
    }

    public static function getFileExtension(string $path): string {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    public static function getFileName(string $path): string {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    public static function isReadable(string $path): bool {
        return file_exists($path) && is_readable($path);
    }

    public static function isWritable(string $path): bool {
        if (file_exists($path)) {
            return is_writable($path);
        }
        return is_writable(dirname($path));
    }
}