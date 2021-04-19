<?php


namespace game_chef\pmmp\form\team_game_map_forms;


use form_builder\models\custom_form_elements\Input;
use form_builder\models\CustomForm;
use game_chef\models\map_data\TeamDataOnMap;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\pmmp\hotbar_menu\CreateCustomTeamVectorDataHotbarMenu;
use pocketmine\Player;

class CreateCustomTeamVectorDataForm extends CustomForm
{
    private TeamGameMapData $teamGameMapData;
    private TeamDataOnMap $teamData;
    private Input $keyElement;

    public function __construct(TeamGameMapData $teamGameMapData, TeamDataOnMap $teamData) {
        $this->teamGameMapData = $teamGameMapData;
        $this->teamData = $teamData;
        $this->keyElement = new Input("keyを入力", "", "");
        parent::__construct("カスタムチーム座標データを作成", [
            $this->keyElement
        ]);
    }

    function onSubmit(Player $player): void {
        $key = $this->keyElement->getResult();
        $menu = new CreateCustomTeamVectorDataHotbarMenu($player, $this->teamGameMapData, $this->teamData, $key);
        $menu->send();
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new CustomTeamVectorDataListForm($this->teamGameMapData, $this->teamData));
    }
}