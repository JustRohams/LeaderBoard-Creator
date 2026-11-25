<?php

declare(strict_types=1);

namespace RohaM\LBCreator\command\subcommand;

use pocketmine\player\Player;
use RohaM\LBCreator\Loader;
use RohaM\LBCreator\form\SelectPluginForm;
use RohaM\LBCreator\form\SelectStatForm;
use RohaM\LBCreator\exception\LeaderboardException;
use RohaM\LBCreator\exception\PluginNotFoundException;
use RohaM\LBCreator\exception\StatNotFoundException;
use RohaM\LBCreator\exception\ValidationException;

class CreateSubCommand extends SubCommand {

    public function __construct(Loader $plugin) {
        parent::__construct($plugin, "create", "Create a new leaderboard", "/lbc create [plugin] [stat] [limit]", "lbcreator.create");
    }

    public function execute(Player $player, array $args): void {
        if (empty($args)) {
            $form = new SelectPluginForm($this->plugin, $player);
            $form->send();
            return;
        }

        $pluginName = $args[0];
        $stat = $args[1] ?? null;
        $limit = (int) ($args[2] ?? $this->plugin->getConfigManager()->get("default_limit", 10));

        if ($limit < 1 || $limit > 50) {
            $player->sendMessage($this->plugin->getMessages()->get("error_limit_range"));
            return;
        }

        try {
            if ($stat === null) {
                $stats = $this->plugin->getLeaderboardManager()->getAvailableStats($pluginName);
                if (empty($stats)) {
                    $player->sendMessage($this->plugin->getMessages()->get("error_no_online_players", ["{plugin}" => $pluginName]));
                    return;
                }
                $form = new SelectStatForm($this->plugin, $player, $pluginName, $limit);
                $form->send();
                return;
            }

            $title = $this->plugin->getConfigManager()->get("default_title", "RohaM");
            $end = $this->plugin->getConfigManager()->get("default_end_text", "");

            $player->sendMessage($this->plugin->getMessages()->get("creating", ["{plugin}" => $pluginName]));

            $id = $this->plugin->getLeaderboardManager()->createLeaderboard(
                $player, $pluginName, $stat, $limit, $title, $end
            );

            $fileName = $pluginName . "_" . $stat . "_" . $id . ".yml";
            $player->sendMessage($this->plugin->getMessages()->get("created", [
                "{id}" => $id,
                "{plugin}" => $pluginName
            ]));
            $player->sendMessage($this->plugin->getMessages()->get("created_file", ["{file}" => $fileName]));

        } catch (PluginNotFoundException | StatNotFoundException | ValidationException $e) {
            $player->sendMessage("§c" . $e->getMessage());
        } catch (LeaderboardException $e) {
            $player->sendMessage("§c" . $e->getMessage());
        }
    }
}