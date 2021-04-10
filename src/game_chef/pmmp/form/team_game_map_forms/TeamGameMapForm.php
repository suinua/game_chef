<?php


namespace game_chef\pmmp\form\team_game_map_forms;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use game_chef\pmmp\form\MainMapForm;
use pocketmine\Player;

class TeamGameMapForm extends SimpleForm
{

    public function __construct() {
        parent::__construct("チームゲーム用のマップ", "", [
            new SimpleFormButton(
                "新しく作成",
                null,
                function (Player $player) {
                    $player->sendForm(new CreateTeamGameMapForm());
                }
            ),
            new SimpleFormButton(
                "一覧",
                null,
                function (Player $player) {
                    $player->sendForm(new TeamGameMapListForm());
                }
            )
        ]);
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new MainMapForm());
    }
}