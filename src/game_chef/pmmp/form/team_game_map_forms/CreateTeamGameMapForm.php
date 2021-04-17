<?php


namespace game_chef\pmmp\form\team_game_map_forms;


use form_builder\models\custom_form_elements\Input;
use form_builder\models\CustomForm;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\repository\TeamGameMapDataRepository;
use game_chef\utilities\GameTypeListFromString;
use pocketmine\Player;

class CreateTeamGameMapForm extends CustomForm
{
    private Input $nameElement;
    private Input $gameTypeListElement;

    public function __construct() {
        $this->nameElement = new Input("マップ名を入力", "", "");
        $this->gameTypeListElement = new Input("ゲームタイプを入力", "type1,typ2", "");

        parent::__construct("新しいチームゲーム用のマップを作成", [
            $this->nameElement,
            $this->gameTypeListElement
        ]);
    }

    function onSubmit(Player $player): void {
        try {
            $gameTypeList = GameTypeListFromString::execute($this->gameTypeListElement->getResult());
            $mapData = TeamGameMapData::asNew($this->nameElement->getResult(), $player->getLevel()->getName(), $gameTypeList);
            TeamGameMapDataRepository::add($mapData);
        } catch (\Exception $e) {
            $player->sendMessage($e->getMessage());
            return;
        }

        $player->sendForm(new TeamGameMapListForm());
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new TeamGameMapForm());
    }
}