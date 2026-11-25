<?php

declare(strict_types=1);

namespace RohaM\LBCreator\command\subcommand;

use pocketmine\player\Player;
use RohaM\LBCreator\Loader;
use RohaM\LBCreator\form\EditLeaderboardForm;

class EditSubCommand extends SubCommand {

    public function __construct(Loader $plugin) {
        parent::__construct($plugin, "edit", "Edit a leaderboard", "/lbc edit <id> [key] [value]", "lbcreator.edit");
    }

    public function execute(Player $player, array $args): void {
        if (empty($args)) {
            $player->sendMessage("§cUsage: /lbc edit <id> [limit|title|end|stat] [value]");
            return;
        }

        $id = (int) $args[0];
        $lb = $this->plugin->getLeaderboardManager()->getLeaderboard($id);
        if ($lb === null) {
            $player->sendMessage($this->plugin->getMessages()->get("error_not_found"));
            return;
        }

        if (count($args) === 1) {
            $form = new EditLeaderboardForm($this->plugin, $player, $id);
            $form->send();
            return;
        }

        $key = strtolower($args[1]);
        $value = isset($args[2]) ? trim(implode(" ", array_slice($args, 2))) : null;

        if ($value === null || ($value === "" && $key !== "end")) {
            $player->sendMessage("§cPlease provide a value for {$key}");
            return;
        }

        $changes = [];
        switch ($key) {
            case "limit":
                $limit = (int) $value;
                if ($limit < 1 || $limit > 50) {
                    $player->sendMessage($this->plugin->getMessages()->get("error_limit_range"));
                    return;
                }
                $changes["limit"] = $limit;
                break;
            case "title":
                $changes["title"] = $value;
                break;
            case "end":
                $changes["end"] = $value;
                break;
            case "stat":
                $stats = $this->plugin->getLeaderboardManager()->getAvailableStats($lb['plugin']);
                if (!in_array($value, $stats, true)) {
                    $player->sendMessage($this->plugin->getMessages()->get("error_invalid_stat", ["{stat}" => $value, "{plugin}" => $lb['plugin']]));
                    return;
                }
                $changes["stat"] = $value;
                break;
            default:
                $player->sendMessage("§cInvalid key! Use: limit, title, end, stat");
                return;
        }

        if ($this->plugin->getLeaderboardManager()->editLeaderboard($id, $changes)) {
            $player->sendMessage($this->plugin->getMessages()->get("edited", ["{id}" => $id]));
        } else {
            $player->sendMessage($this->plugin->getMessages()->get("error_not_found"));
        }
    }
}