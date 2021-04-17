<?php


namespace game_chef\pmmp\form\team_game_map_forms;


use form_builder\models\modal_form_elements\ModalFormButton;
use form_builder\models\ModalForm;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\repository\TeamGameMapDataRepository;
use pocketmine\Player;

class ConfirmDeletingTeamGameMap extends ModalForm
{
    private TeamGameMapData $mapData;

    public function __construct(TeamGameMapData $mapDataData) {
        $this->mapData = $mapDataData;

        parent::__construct(
            "マップを削除",
            "本当に{$mapDataData->getName()}を削除しますか？",
            new ModalFormButton("削除"),
            new ModalFormButton("キャンセル"));
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new TeamGameMapDetailForm($this->mapData));
    }

    public function onClickButton1(Player $player): void {
        try {
            TeamGameMapDataRepository::delete($this->mapData->getName());
        } catch (\Exception $e) {
            $player->sendMessage($e->getMessage());
            return;
        }

        $player->sendForm(new TeamGameMapListForm());
    }

    public function onClickButton2(Player $player): void {
        $player->sendForm(new TeamGameMapDetailForm($this->mapData));
    }
}