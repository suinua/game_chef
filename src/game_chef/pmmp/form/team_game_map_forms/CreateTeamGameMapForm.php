<?php


namespace game_chef\pmmp\form\team_game_map_forms;


use form_builder\models\custom_form_elements\Input;
use form_builder\models\custom_form_elements\Label;
use form_builder\models\CustomForm;
use game_chef\models\GameType;
use game_chef\services\TeamGameMapService;
use pocketmine\Player;

class CreateTeamGameMapForm extends CustomForm
{
    private Input $nameElement;
    private Input $gameTypeListElement;

    public function __construct() {
        $this->nameElement = new Input("", "マップ名", "");
        $this->gameTypeListElement = new Input("", "ゲームタイプ", "");

        parent::__construct("新しいチームゲーム用のマップを作成", [
            $this->nameElement,
            new Label("type1,type2"),
            $this->gameTypeListElement
        ]);
    }

    function onSubmit(Player $player): void {
        try {
            $gameTypeList = [];
            foreach (explode(",", $this->gameTypeListElement->getResult()) as $value) {
                if ($value === "") continue;
                $gameTypeList[] = new GameType($value);
            }
            TeamGameMapService::create($this->nameElement->getResult(), $player->getLevel()->getName(), $gameTypeList);

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