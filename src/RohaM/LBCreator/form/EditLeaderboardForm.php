<?php

declare(strict_types=1);

namespace RohaM\LBCreator\form;

use pocketmine\player\Player;
use RohaM\LBCreator\Loader;

class EditLeaderboardForm extends BaseForm {

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

        $changes = [];

        $limit = (int) ($data[0] ?? $this->lb['limit']);
        if ($limit !== (int) $this->lb['limit']) {
            if ($limit < 1 || $limit > 50) {
                $player->sendMessage($this->plugin->getMessages()->get("error_limit_range"));
                return;
            }
            $changes["limit"] = $limit;
        }

        $title = trim((string) ($data[1] ?? ""));
        if ($title !== "" && $title !== ($this->lb['title'] ?? "")) {
            $changes["title"] = $title;
        }

        $end = (string) ($data[2] ?? "");
        if ($end !== ($this->lb['end'] ?? "")) {
            $changes["end"] = $end;
        }

        // Dropdown returns the selected INDEX, not the text
        $stats = $this->plugin->getLeaderboardManager()->getAvailableStats($this->lb['plugin']);
        if (isset($data[3], $stats[$data[3]])) {
            $stat = $stats[$data[3]];
            if ($stat !== $this->lb['stat']) {
                $changes["stat"] = $stat;
            }
        }

        if (empty($changes)) {
            $player->sendMessage("§7No changes made.");
            return;
        }

        if ($this->plugin->getLeaderboardManager()->editLeaderboard($this->id, $changes)) {
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

        $content = [
            ["type" => "slider", "text" => "Limit", "min" => 1, "max" => 50, "step" => 1, "default" => max(1, min(50, (int) $this->lb['limit']))],
            ["type" => "input", "text" => "Title", "placeholder" => "Enter title", "default" => (string) ($this->lb['title'] ?? "")],
            ["type" => "input", "text" => "End Text", "placeholder" => "Enter end text", "default" => (string) ($this->lb['end'] ?? "")]
        ];
        if (!empty($stats)) {
            $content[] = ["type" => "dropdown", "text" => "Statistic", "options" => array_values($stats), "default" => $defaultStat];
        }

        return [
            "type" => "custom_form",
            "title" => $this->plugin->getMessages()->get("form_title_edit"),
            "content" => $content
        ];
    }
}
