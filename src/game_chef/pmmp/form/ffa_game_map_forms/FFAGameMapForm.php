<?php


namespace game_chef\pmmp\form\ffa_game_map_forms;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use game_chef\pmmp\form\MainMapForm;
use pocketmine\Player;

class FFAGameMapForm extends SimpleForm
{

    public function __construct() {
        parent::__construct("FFA用のマップ", "", [
            new SimpleFormButton(
                "新しく作成",
                null,
                function (Player $player) {
                    $player->sendForm(new CreateFFAGameMapForm());
                }
            ),
            new SimpleFormButton(
                "一覧",
                null,
                function (Player $player) {
                    $player->sendForm(new FFAGameMapListForm());
                }
            )
        ]);
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new MainMapForm());
    }
}