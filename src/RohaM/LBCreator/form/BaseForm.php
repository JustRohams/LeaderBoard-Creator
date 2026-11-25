<?php

declare(strict_types=1);

namespace RohaM\LBCreator\form;

use pocketmine\form\Form;
use pocketmine\player\Player;
use RohaM\LBCreator\Loader;

abstract class BaseForm implements Form {

    protected Loader $plugin;
    protected Player $player;

    public function __construct(Loader $plugin, Player $player) {
        $this->plugin = $plugin;
        $this->player = $player;
    }

    public function send(): void {
        $this->player->sendForm($this);
    }

    abstract public function handleResponse(Player $player, $data): void;

    abstract public function jsonSerialize(): array;
}
