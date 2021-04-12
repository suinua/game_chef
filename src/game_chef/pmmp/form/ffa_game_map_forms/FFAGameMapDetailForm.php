<?php


namespace game_chef\pmmp\form\ffa_game_map_forms;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use game_chef\models\FFAGameMap;
use game_chef\models\FFAGameMapSpawnPointEditor;
use game_chef\store\FFAGameMapSpawnPointEditorStore;
use game_chef\TaskSchedulerStorage;
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
                    $player->sendForm(new EditFFAGameMapGameTypeForm($this->ffaGameMap));
                }
            ),
            new SimpleFormButton(
                "スポーン地点の変更",
                null,
                function (Player $player) {
                    $editor = new FFAGameMapSpawnPointEditor($this->ffaGameMap, $player, TaskSchedulerStorage::get());
                    try {
                        FFAGameMapSpawnPointEditorStore::add($player->getName(), $editor);
                    } catch (\Exception $e) {
                        $player->sendMessage($e);
                        return;
                    }

                    try {
                        $editor->start();
                    } catch (\Exception $exception) {
                        $player->sendMessage($exception->getMessage());
                        return;
                    }
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