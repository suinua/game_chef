<?php


namespace game_chef\pmmp\form\team_game_map_forms;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use game_chef\models\map_data\TeamDataOnMap;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\pmmp\hotbar_menu\CustomTeamArrayVectorDataHotbarMenu;
use pocketmine\Player;

class CustomTeamArrayVectorDataListForm extends SimpleForm
{
    private TeamGameMapData $mapData;
    private TeamDataOnMap $teamData;

    public function __construct(TeamGameMapData $mapData, TeamDataOnMap $teamData) {
        $this->mapData = $mapData;
        $this->teamData = $teamData;
        $buttons = [
            new SimpleFormButton(
                "追加",
                null,
                function (Player $player) {
                    $player->sendForm(new CreateCustomTeamArrayVectorDataForm($this->mapData, $this->teamData));
                }
            )
        ];
        foreach ($teamData->getCustomTeamArrayVectorDataList() as $customTeamArrayVectorData) {
            $buttons[] = new SimpleFormButton(
                $customTeamArrayVectorData->getKey(),
                null,
                function (Player $player) use ($customTeamArrayVectorData) {
                    $menu = new CustomTeamArrayVectorDataHotbarMenu($player, $this->mapData, $this->teamData, $customTeamArrayVectorData);
                    $menu->send();
                }
            );
        }

        parent::__construct("配列型カスタム座標データ", $mapData->getName(), $buttons);
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new TeamGameMapDetailForm($this->mapData));
    }
}