<?php


namespace game_chef\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use game_chef\pmmp\form\team_game_map_forms\TeamGameMapForm;
use pocketmine\Player;

class MainMapForm extends SimpleForm
{

    public function __construct() {
        parent::__construct("Map", "", [
            new SimpleFormButton(
                "チームゲーム用のマップ設定",
                null,
                function (Player $player) {
                    $player->sendForm(new TeamGameMapForm());
                }
            ),
            new SimpleFormButton(
                "FFA用のマップ設定",
                null,
                function (Player $player) {
                    //TODO:実装
                }
            )
        ]);
    }

    function onClickCloseButton(Player $player): void {
    }
}