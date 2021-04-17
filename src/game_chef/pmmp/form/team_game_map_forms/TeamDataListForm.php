<?php


namespace game_chef\pmmp\form\team_game_map_forms;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use game_chef\models\map_data\TeamGameMapData;
use pocketmine\Player;

class TeamDataListForm extends SimpleForm
{
    private TeamGameMapData $teamGameMapData;

    public function __construct(TeamGameMapData $teamGameMapDataData) {
        $this->teamGameMapData = $teamGameMapDataData;
        $buttons = [
            new SimpleFormButton(
                "新しいチームを追加",
                null,
                function (Player $player)  {
                    $player->sendForm(new CreateNewTeamForm($this->teamGameMapData));
                }
            )
        ];
        foreach ($this->teamGameMapData->getTeamDataList() as $teamDataOnMap) {
            $buttons[] = new SimpleFormButton(
                $teamDataOnMap->getColorFormat() . $teamDataOnMap->getName(),
                null,
                function (Player $player) use ($teamDataOnMap) {
                    $player->sendForm(new TeamDataDetailForm($this->teamGameMapData, $teamDataOnMap));
                }
            );
        }

        parent::__construct("チームデータ一覧", $this->teamGameMapData->getName(), $buttons);
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new TeamGameMapDetailForm($this->teamGameMapData));
    }
}