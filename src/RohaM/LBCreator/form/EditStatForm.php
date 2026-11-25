<?php

declare(strict_types=1);

namespace RohaM\LBCreator\form;

use pocketmine\player\Player;
use RohaM\LBCreator\Loader;

class EditStatForm extends BaseForm {

    private int $id;
    private ?array $lb;

    public function __construct(Loader $plugin, Player $player, int $id) {
        parent::__construct($plugin, $player);
        $this->id = $id;
        $this->lb = $plugin->getLeaderboardManager()->getLeaderboard($id);
    }

    public function send(): void {
        if ($this->lb === null) {
            $this->player->sendMessage($this->plugin->getMessages()->get("error_not_found"));
            return;
        }
        parent::send();
    }

    public function handleResponse(Player $player, $data): void {
        if (!is_array($data) || $this->lb === null) {
            return;
        }

        $stats = $this->plugin->getLeaderboardManager()->getAvailableStats($this->lb['plugin']);
        if (!isset($data[0], $stats[$data[0]])) {
            $player->sendMessage("§cInvalid stat selected!");
            return;
        }

        if ($this->plugin->getLeaderboardManager()->editLeaderboard($this->id, ["stat" => $stats[$data[0]]])) {
            $player->sendMessage($this->plugin->getMessages()->get("edited", ["{id}" => $this->id]));
        }
    }

    public function jsonSerialize(): array {
        $stats = $this->plugin->getLeaderboardManager()->getAvailableStats($this->lb['plugin']);
        $defaultStat = 0;
        foreach ($stats as $i => $stat) {
            if ($stat === $this->lb['stat']) {
                $defaultStat = $i;
            }
        }

        return [
            "type" => "custom_form",
            "title" => "§l§6Edit Statistic",
            "content" => [
                ["type" => "dropdown", "text" => "Select Statistic", "options" => array_values($stats), "default" => $defaultStat]
            ]
        ];
    }
}
