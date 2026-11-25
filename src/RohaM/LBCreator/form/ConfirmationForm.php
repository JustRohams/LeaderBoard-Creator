<?php

declare(strict_types=1);

namespace RohaM\LBCreator\form;

use Closure;
use pocketmine\form\Form;
use pocketmine\player\Player;
use RohaM\LBCreator\Loader;

class ConfirmationForm implements Form {

    private Loader $plugin;
    private Player $player;
    private string $message;
    private Closure $onConfirm;
    private ?Closure $onCancel;

    public function __construct(Loader $plugin, Player $player, string $message, callable $onConfirm, ?callable $onCancel = null) {
        $this->plugin = $plugin;
        $this->player = $player;
        $this->message = $message;
        $this->onConfirm = Closure::fromCallable($onConfirm);
        $this->onCancel = $onCancel !== null ? Closure::fromCallable($onCancel) : null;
    }

    public function handleResponse(Player $player, $data): void {
        // Modal forms return bool (true = button1), null = closed
        if ($data === true) {
            ($this->onConfirm)($player);
            return;
        }

        if ($this->onCancel !== null) {
            ($this->onCancel)($player);
        }
    }

    public function jsonSerialize(): array {
        return [
            "type" => "modal",
            "title" => $this->plugin->getMessages()->get("form_title_confirm"),
            "content" => $this->message,
            "button1" => $this->plugin->getMessages()->get("button_confirm"),
            "button2" => $this->plugin->getMessages()->get("button_cancel")
        ];
    }

    public function send(): void {
        $this->player->sendForm($this);
    }
}
