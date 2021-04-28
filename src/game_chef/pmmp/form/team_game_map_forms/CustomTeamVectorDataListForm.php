<?php


namespace game_chef\pmmp\form\team_game_map_forms;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use game_chef\models\map_data\TeamDataOnMap;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\pmmp\hotbar_menu\CustomTeamVectorDataHotbarMenu;
use pocketmine\Player;

class CustomTeamVectorDataListForm extends SimpleForm
{

    public function __construct(TeamGameMapData $teamGameMapData, TeamDataOnMap $teamDataOnMap) {
        $buttons = [
            new SimpleFormButton(
                "追加",
                null,
                function (Player $player) use ($teamGameMapData, $teamDataOnMap) {
                    $player->sendForm(new CreateCustomTeamVectorDataForm($teamGameMapData, $teamDataOnMap));
                }
            )
        ];
        foreach ($teamDataOnMap->getCustomTeamVectorDataList() as $customTeamVectorData) {
            $buttons[] = new SimpleFormButton(
                strval($customTeamVectorData->getVector3()),
                null,
                function (Player $player) use ($customTeamVectorData, $teamGameMapData, $teamDataOnMap) {
                    $menu = new CustomTeamVectorDataHotbarMenu($player, $teamGameMapData, $teamDataOnMap, $customTeamVectorData);
                    $menu->send();
                }
            );
        }

        parent::__construct("カスタムチーム座標データ", $teamGameMapData->getName(), $buttons);
    }

    function onClickCloseButton(Player $player): void {
    }
}