<?php


namespace game_chef\pmmp\form\team_game_map_forms;


use form_builder\models\modal_form_elements\ModalFormButton;
use form_builder\models\ModalForm;
use game_chef\models\TeamGameMap;
use game_chef\services\TeamGameMapService;
use pocketmine\Player;

class ConfirmDeletingTeamGameMap extends ModalForm
{
    private TeamGameMap $map;

    public function __construct(TeamGameMap $map) {
        $this->map = $map;

        parent::__construct(
            "マップを削除",
            "本当に{$map->getName()}を削除しますか？",
            new ModalFormButton("削除"),
            new ModalFormButton("キャンセル"));
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new TeamGameMapDetailForm($this->map));
    }

    public function onClickButton1(Player $player): void {
        try {
            TeamGameMapService::delete($this->map->getName());
        } catch (\Exception $e) {
            $player->sendMessage($e->getMessage());
            $player->sendForm(new TeamGameMapDetailForm($this->map));
            return;
        }

        $player->sendForm(new TeamGameMapListForm());
    }

    public function onClickButton2(Player $player): void {
        $player->sendForm(new TeamGameMapDetailForm($this->map));
    }
}