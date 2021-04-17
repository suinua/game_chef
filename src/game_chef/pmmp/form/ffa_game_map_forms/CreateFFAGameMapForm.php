<?php


namespace game_chef\pmmp\form\ffa_game_map_forms;


use form_builder\models\custom_form_elements\Input;
use form_builder\models\CustomForm;
use game_chef\models\map_data\FFAGameMapData;
use game_chef\repository\FFAGameMapDataRepository;
use game_chef\utilities\GameTypeListFromString;
use pocketmine\Player;

class CreateFFAGameMapForm extends CustomForm
{
    private Input $nameElement;
    private Input $gameTypeListElement;

    public function __construct() {
        $this->nameElement = new Input("マップ名を入力", "", "");
        $this->gameTypeListElement = new Input("ゲームタイプを入力", "type1,type2", "");

        parent::__construct("新しいFFA用のマップを作成", [
            $this->nameElement,
            $this->gameTypeListElement
        ]);
    }

    function onSubmit(Player $player): void {
        try {
            $gameTypeList = GameTypeListFromString::execute($this->gameTypeListElement->getResult());
            $mapData = FFAGameMapData::asNew($this->nameElement->getResult(), $player->getLevel()->getName(), $gameTypeList);
            FFAGameMapDataRepository::add($mapData);
        } catch (\Exception $e) {
            $player->sendMessage($e->getMessage());
            return;
        }

        $player->sendForm(new FFAGameMapListForm());
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new FFAGameMapForm());
    }
}