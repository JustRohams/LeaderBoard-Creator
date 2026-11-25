<?php

declare(strict_types=1);

namespace RohaM\LBCreator\util;

use RohaM\LBCreator\config\ConfigManager;

class TextFormatter {

    public static function format(array $lb, array $data, ConfigManager $config): string {
        $lines = [];
        $maxNameLength = $config->get("display.max_name_length", 16);
       
        $title = $lb['title'] ?? $config->get("default_title", "RohaM");
        $lines[] = "§l§6" . $title . "§r";
        $lines[] = "§7" . str_repeat("═", 30);
        
        $rank = 1;
        $colors = $config->get("rank_colors", []);
        $defaultColor = $colors['default'] ?? "§f";
        $separator = $config->get("display.separator", " - ");
        $rankPrefix = $config->get("display.rank_prefix", "#");
        $valuePrefix = $config->get("display.value_prefix", "");
        $valueSuffix = $config->get("display.value_suffix", "");
        
        foreach ($data as $name => $value) {
            if ($rank > $lb['limit']) {
                break;
            }
            
            $name = (string) $name;
            $color = $colors[$rank] ?? $defaultColor;
            $valueFormatted = number_format($value);
            
            $displayName = strlen($name) > $maxNameLength ? substr($name, 0, $maxNameLength - 3) . "..." : $name;
            
            $rankStr = str_pad((string) $rank, 2, " ", STR_PAD_LEFT);
            $lines[] = "{$color}{$rankPrefix}{$rankStr} §f{$displayName}{$separator} §e{$valuePrefix}{$valueFormatted}{$valueSuffix}";
            $rank++;
        }
       
        if ($rank === 1) {
            $lines[] = "§7No players found";
        }
        
        if (!empty($lb['end'])) {
            $lines[] = "§7" . str_repeat("═", 30);
            $lines[] = "§7" . $lb['end'];
        }
        
        return implode("\n", $lines);
    }

    public static function formatForFile(array $lb, array $data): array {
        $result = [
            "title" => $lb['title'] ?? "RohaM",
            "end" => $lb['end'] ?? "",
            "plugin" => $lb['plugin'],
            "stat" => $lb['stat'],
            "limit" => $lb['limit'],
            "created_at" => $lb['created_at'] ?? time(),
            "last_update" => $lb['last_update'] ?? time(),
            "lb" => []
        ];
        
        $rank = 1;
        foreach ($data as $name => $value) {
            if ($rank > $lb['limit']) {
                break;
            }
            $result['lb'][] = "#{$rank} - {$name} - " . number_format($value);
            $rank++;
        }
        
        return $result;
    }

    public static function formatForChat(array $lb, array $data, int $limit = 10): string {
        $lines = [];
        $lines[] = "§6▸ §l" . ($lb['title'] ?? "RohaM") . " §r§6(" . $lb['plugin'] . " - " . $lb['stat'] . ")§r";
        $lines[] = "§7" . str_repeat("─", 35);
        
        $rank = 1;
        foreach ($data as $name => $value) {
            if ($rank > $limit) break;
            $color = $rank <= 3 ? ["§6", "§7", "§8"][$rank - 1] : "§f";
            $lines[] = "{$color}#{$rank} §f{$name} §7- §e" . number_format($value);
            $rank++;
        }
        
        if ($rank === 1) {
            $lines[] = "§7No players found";
        }
        
        return implode("\n", $lines);
    }
}