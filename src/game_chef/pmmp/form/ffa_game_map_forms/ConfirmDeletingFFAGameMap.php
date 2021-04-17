<?php


namespace game_chef\pmmp\form\ffa_game_map_forms;


use form_builder\models\modal_form_elements\ModalFormButton;
use form_builder\models\ModalForm;
use game_chef\models\map_data\FFAGameMapData;
use game_chef\repository\FFAGameMapDataRepository;
use pocketmine\Player;

class ConfirmDeletingFFAGameMap extends ModalForm
{
    private FFAGameMapData $mapData;

    public function __construct(FFAGameMapData $mapData) {
        $this->mapData = $mapData;

        parent::__construct(
            "マップを削除",
            "本当に{$mapData->getName()}を削除しますか？",
            new ModalFormButton("削除"),
            new ModalFormButton("キャンセル"));
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new FFAGameMapDetailForm($this->mapData));
    }

    public function onClickButton1(Player $player): void {
        try {
            FFAGameMapDataRepository::delete($this->mapData->getName());
        } catch (\Exception $e) {
            $player->sendMessage($e->getMessage());
            return;
        }

        $player->sendForm(new FFAGameMapListForm());
    }

    public function onClickButton2(Player $player): void {
        $player->sendForm(new FFAGameMapDetailForm($this->mapData));
    }
}