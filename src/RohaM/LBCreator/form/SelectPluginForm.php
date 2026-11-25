<?php

declare(strict_types=1);

namespace RohaM\LBCreator\form;

use pocketmine\player\Player;
use RohaM\LBCreator\Loader;

class SelectPluginForm extends BaseForm {

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            return;
        }

        $plugins = $this->getPlugins();
        if (!isset($plugins[$data])) {
            return;
        }

        $form = new SelectStatForm($this->plugin, $player, $plugins[$data]);
        $form->send();
    }

    public function jsonSerialize(): array {
        $plugins = $this->getPlugins();
        $buttons = [];
        foreach ($plugins as $name) {
            $buttons[] = ["text" => "§e" . $name];
        }

        return [
            "type" => "form",
            "title" => $this->plugin->getMessages()->get("form_title_select_plugin"),
            "content" => "§7Select a plugin to create a leaderboard for:",
            "buttons" => $buttons
        ];
    }

    private function getPlugins(): array {
        $plugins = [];
        foreach ($this->plugin->getServer()->getPluginManager()->getPlugins() as $plugin) {
            if ($plugin->isEnabled()) {
                $name = $plugin->getName();
                $stats = $this->plugin->getLeaderboardManager()->getAvailableStats($name);
                if (!empty($stats)) {
                    $plugins[] = $name;
                }
            }
        }
        return $plugins;
    }
}