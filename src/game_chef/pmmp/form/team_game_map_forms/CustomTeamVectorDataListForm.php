<?php


namespace game_chef\pmmp\form\team_game_map_forms;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use game_chef\models\map_data\TeamDataOnMap;
use game_chef\models\map_data\TeamGameMapData;
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
        foreach ($teamGameMapData->getCustomMapVectorDataList() as $customMapVectorData) {
            $buttons[] = new SimpleFormButton(
                strval($customMapVectorData),
                null,
                function (Player $player) {
                    //todo:
                }
            );
        }

        parent::__construct("カスタムチーム座標データ", $teamGameMapData->getName(), $buttons);
    }

    function onClickCloseButton(Player $player): void {
    }
}