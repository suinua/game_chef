<?php


namespace game_chef\pmmp\form\ffa_game_map_forms;


use form_builder\models\modal_form_elements\ModalFormButton;
use form_builder\models\ModalForm;
use game_chef\models\FFAGameMap;
use game_chef\services\FFAGameMapService;
use pocketmine\Player;

class ConfirmDeletingFFAGameMap extends ModalForm
{
    private FFAGameMap $map;

    public function __construct(FFAGameMap $map) {
        $this->map = $map;

        parent::__construct(
            "マップを削除",
            "本当に{$map->getName()}を削除しますか？",
            new ModalFormButton("削除"),
            new ModalFormButton("キャンセル"));
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new FFAGameMapDetailForm($this->map));
    }

    public function onClickButton1(Player $player): void {
        try {
            FFAGameMapService::delete($this->map->getName());
        } catch (\Exception $e) {
            $player->sendMessage($e->getMessage());
            $player->sendForm(new FFAGameMapDetailForm($this->map));
            return;
        }

        $player->sendForm(new FFAGameMapListForm());
    }

    public function onClickButton2(Player $player): void {
        $player->sendForm(new FFAGameMapDetailForm($this->map));
    }
}