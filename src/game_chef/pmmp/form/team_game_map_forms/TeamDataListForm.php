<?php


namespace game_chef\pmmp\form\team_game_map_forms;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use game_chef\models\TeamGameMap;
use pocketmine\Player;

class TeamDataListForm extends SimpleForm
{
    private TeamGameMap $teamGameMap;

    public function __construct(TeamGameMap $teamGameMap) {
        $this->teamGameMap = $teamGameMap;
        $buttons = [];
        foreach ($teamGameMap->getTeamDataList() as $teamDataOnMap) {
            $buttons[] = new SimpleFormButton(
                $teamDataOnMap->getTeamColorFormat() . $teamDataOnMap->getTeamName(),
                null,
                function (Player $player) use ($teamDataOnMap) {
                    $player->sendForm(new TeamDataDetailForm($this->teamGameMap, $teamDataOnMap));
                }
            );
        }

        parent::__construct("チームデータ一覧", $teamGameMap->getName(), $buttons);
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new TeamGameMapDetailForm($this->teamGameMap));
    }
}