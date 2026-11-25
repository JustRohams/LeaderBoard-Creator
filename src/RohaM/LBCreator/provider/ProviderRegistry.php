<?php

declare(strict_types=1);

namespace RohaM\LBCreator\provider;

class ProviderRegistry {

    private static array $providers = [];

    public static function register(string $pluginName, PluginProviderInterface $provider): void {
        self::$providers[$pluginName] = $provider;
    }

    public static function getProvider(string $pluginName): ?PluginProviderInterface {
        return self::$providers[$pluginName] ?? null;
    }

    public static function hasProvider(string $pluginName): bool {
        return isset(self::$providers[$pluginName]);
    }

    public static function unregister(string $pluginName): void {
        unset(self::$providers[$pluginName]);
    }

    public static function getAllProviders(): array {
        return self::$providers;
    }
}