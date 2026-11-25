<?php

declare(strict_types=1);

namespace RohaM\LBCreator\config;

use RohaM\LBCreator\util\ValidateHelper;

class ConfigValidator {

    public static function validateConfig(array $config): array {
        $errors = [];

        if (!isset($config['update_interval']) || !ValidateHelper::isPositiveInteger($config['update_interval'])) {
            $errors[] = "update_interval must be a positive integer";
        }

        if (!isset($config['default_limit']) || !ValidateHelper::isInRange($config['default_limit'], 1, 50)) {
            $errors[] = "default_limit must be between 1 and 50";
        }

        if (!isset($config['cache_time']) || !ValidateHelper::isPositiveInteger($config['cache_time'])) {
            $errors[] = "cache_time must be a positive integer";
        }

        if (!isset($config['database']['type']) || !in_array($config['database']['type'], ['yaml', 'json'])) {
            $errors[] = "database.type must be 'yaml' or 'json'";
        }

        if (isset($config['display']['max_name_length']) && !ValidateHelper::isInRange($config['display']['max_name_length'], 1, 64)) {
            $errors[] = "display.max_name_length must be between 1 and 64";
        }

        return $errors;
    }

    public static function validateAndFix(array &$config): void {
        $defaults = DefaultConfig::get();
        foreach ($defaults as $key => $value) {
            if (!isset($config[$key])) {
                $config[$key] = $value;
            }
        }

        if (isset($config['database']) && is_array($config['database'])) {
            foreach ($defaults['database'] as $key => $value) {
                if (!isset($config['database'][$key])) {
                    $config['database'][$key] = $value;
                }
            }
        }

        if (isset($config['display']) && is_array($config['display'])) {
            foreach ($defaults['display'] as $key => $value) {
                if (!isset($config['display'][$key])) {
                    $config['display'][$key] = $value;
                }
            }
        }

        if (isset($config['scanner']) && is_array($config['scanner'])) {
            foreach ($defaults['scanner'] as $key => $value) {
                if (!isset($config['scanner'][$key])) {
                    $config['scanner'][$key] = $value;
                }
            }
        }
    }
}