<?php


namespace game_chef\pmmp\form\team_game_map_forms;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use game_chef\models\map_data\TeamGameMapData;
use pocketmine\Player;

class TeamGameMapDetailForm extends SimpleForm
{
    private TeamGameMapData $teamGameMapData;

    public function __construct(TeamGameMapData $teamGameMapDataData) {
        $this->teamGameMapData = $teamGameMapDataData;

        parent::__construct($teamGameMapDataData->getName(), "", [
            new SimpleFormButton(
                "ゲームタイプの変更",
                null,
                function (Player $player) {
                    $player->sendForm(new EditTeamGameMapGameTypeForm($this->teamGameMapData));
                }
            ),
            new SimpleFormButton(
                "チームのデータの変更",
                null,
                function (Player $player) {
                    $player->sendForm(new TeamDataListForm($this->teamGameMapData));
                }
            ),
            new SimpleFormButton(
                "削除",
                null,
                function (Player $player) {
                    $player->sendForm(new ConfirmDeletingTeamGameMap($this->teamGameMapData));
                }
            )
        ]);
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new TeamGameMapListForm());
    }
}