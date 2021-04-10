<?php


namespace game_chef\pmmp\form\ffa_game_map_forms;


use form_builder\models\custom_form_elements\Input;
use form_builder\models\CustomForm;
use game_chef\models\GameType;
use game_chef\services\FFAGameMapService;
use pocketmine\Player;

class CreateFFAGameMapForm extends CustomForm
{
    private Input $nameElement;
    private Input $gameTypeListElement;

    public function __construct() {
        $this->nameElement = new Input("", "マップ名", "");
        $this->gameTypeListElement = new Input("", "マップ名", "");

        parent::__construct("新しいFFA用のマップを作成", [
            $this->nameElement,
            $this->gameTypeListElement
        ]);
    }

    function onSubmit(Player $player): void {
        try {
            $gameTypeList = [];
            foreach (explode(",", $this->gameTypeListElement->getResult()) as $value) {
                $gameTypeList = new GameType($value);
            }
            FFAGameMapService::create($this->nameElement->getResult(), $player->getLevel(), $gameTypeList);

        } catch (\Exception $e) {
            $player->sendMessage($e->getMessage());
            return;
        }

        //$player->sendForm(new TeamGameMapListForm());
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new FFAGameMapForm());
    }
}