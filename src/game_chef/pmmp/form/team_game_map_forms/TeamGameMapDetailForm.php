<?php


namespace game_chef\pmmp\form\team_game_map_forms;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use game_chef\models\TeamGameMap;
use pocketmine\Player;

class TeamGameMapDetailForm extends SimpleForm
{
    private TeamGameMap $teamGameMap;

    public function __construct(TeamGameMap $teamGameMap) {
        $this->teamGameMap = $teamGameMap;

        parent::__construct($teamGameMap->getName(), "", [
            new SimpleFormButton(
                "ゲームタイプの変更",
                null,
                function (Player $player) {
                    $player->sendForm(new EditTeamGameMapGameTypeForm($this->teamGameMap));
                }
            ),
            new SimpleFormButton(
                "チームのデータの変更",
                null,
                function (Player $player) {
                    //TODO:実装
                }
            ),
            new SimpleFormButton(
                "削除",
                null,
                function (Player $player) {
                    $player->sendForm(new ConfirmDeletingTeamGameMap($this->teamGameMap));
                }
            )
        ]);
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new TeamGameMapListForm());
    }
}