<?php

declare(strict_types=1);

namespace RohaM\LBCreator\task;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use RohaM\LBCreator\Loader;
use RohaM\LBCreator\DynamicDataFetcher;

class AsyncFetchTask extends AsyncTask {

    private string $pluginName;
    private string $stat;
    private int $limit;
    private string $serializedFetcher;

    public function __construct(string $pluginName, string $stat, int $limit) {
        $this->pluginName = $pluginName;
        $this->stat = $stat;
        $this->limit = $limit;
        $this->storeLocal($this->pluginName);
        $this->storeLocal($this->stat);
        $this->storeLocal($this->limit);
    }

    public function onRun(): void {
        $result = [
            "success" => false,
            "data" => [],
            "error" => "AsyncTask cannot use Reflection. Use DeferredUpdateTask instead."
        ];
        $this->setResult($result);
    }

    public function onCompletion(): void {
        $result = $this->getResult();
        $plugin = Server::getInstance()->getPluginManager()->getPlugin("LBCreator");
        if ($plugin instanceof Loader && $result["success"]) {
            $cache = $plugin->getLeaderboardManager()->getCache();
            $cacheKey = md5($this->pluginName . "_" . $this->stat . "_" . $this->limit);
            $cache->set($cacheKey, $result["data"], 60);
        }
    }
}