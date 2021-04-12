<?php


namespace game_chef\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use game_chef\pmmp\form\ffa_game_map_forms\FFAGameMapForm;
use game_chef\pmmp\form\ffa_game_map_forms\FFAGameMapListForm;
use game_chef\pmmp\form\team_game_map_forms\TeamGameMapForm;
use pocketmine\Player;

//TODO:同時に複数の人がマップを編集できるかどうか
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
                    $player->sendForm(new FFAGameMapForm());
                }
            )
        ]);
    }

    function onClickCloseButton(Player $player): void {
    }
}