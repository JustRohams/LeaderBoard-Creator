<?php

declare(strict_types=1);

namespace RohaM\LBCreator\task;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use RohaM\LBCreator\Loader;
use RohaM\LBCreator\util\TextFormatter;

class AsyncSaveTask extends AsyncTask {

    private array $leaderboardData;
    private array $topPlayersData;
    private string $savePath;

    public function __construct(array $leaderboardData, array $topPlayersData, string $savePath) {
        $this->leaderboardData = $leaderboardData;
        $this->topPlayersData = $topPlayersData;
        $this->savePath = $savePath;
        
        $this->storeLocal($this->leaderboardData);
        $this->storeLocal($this->topPlayersData);
        $this->storeLocal($this->savePath);
    }

    public function onRun(): void {
        $data = $this->fetchLocal("leaderboardData");
        $topData = $this->fetchLocal("topPlayersData");
        $path = $this->fetchLocal("savePath");

        $formatted = [
            "title" => $data['title'] ?? "RohaM",
            "end" => $data['end'] ?? "",
            "plugin" => $data['plugin'] ?? "",
            "stat" => $data['stat'] ?? "",
            "limit" => $data['limit'] ?? 10,
            "created_at" => $data['created_at'] ?? time(),
            "last_update" => time(),
            "lb" => []
        ];

        $rank = 1;
        foreach ($topData as $name => $value) {
            if ($rank > $data['limit']) {
                break;
            }
            $formatted['lb'][] = "#{$rank} - {$name} - " . number_format($value);
            $rank++;
        }

        $yaml = "---\n";
        foreach ($formatted as $key => $value) {
            if (is_array($value)) {
                $yaml .= $key . ":\n";
                foreach ($value as $item) {
                    $yaml .= "  - \"" . addslashes($item) . "\"\n";
                }
            } else {
                $yaml .= $key . ": \"" . addslashes((string)$value) . "\"\n";
            }
        }

        file_put_contents($path, $yaml);
        $this->setResult(["success" => true, "path" => $path]);
    }

    public function onCompletion(): void {
        $result = $this->getResult();
        if ($result["success"]) {
            $plugin = Server::getInstance()->getPluginManager()->getPlugin("LBCreator");
            if ($plugin instanceof Loader) {
                $plugin->getLogger()->debug("Leaderboard data saved to: " . $result["path"]);
            }
        }
    }
}