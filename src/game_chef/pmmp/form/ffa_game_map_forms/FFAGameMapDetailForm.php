<?php


namespace game_chef\pmmp\form\ffa_game_map_forms;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use game_chef\models\FFAGameMap;
use pocketmine\Player;

class FFAGameMapDetailForm extends SimpleForm
{
    private FFAGameMap $ffaGameMap;

    public function __construct(FFAGameMap $ffaGameMap) {
        $this->ffaGameMap = $ffaGameMap;

        parent::__construct($ffaGameMap->getName(), "", [
            new SimpleFormButton(
                "ゲームタイプの変更",
                null,
                function (Player $player) {
                    //TODO:実装
                }
            ),
            new SimpleFormButton(
                "スポーン地点の変更",
                null,
                function (Player $player) {
                    //TODO:実装
                }
            ),
            new SimpleFormButton(
                "削除",
                null,
                function (Player $player) {
                    $player->sendForm(new ConfirmDeletingFFAGameMap($this->ffaGameMap));
                }
            )
        ]);
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new FFAGameMapListForm());
    }
}