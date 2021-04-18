<?php


namespace game_chef\pmmp\form\ffa_game_map_forms;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use game_chef\models\editors\FFAGameMapSpawnPointEditor;
use game_chef\models\map_data\FFAGameMapData;
use game_chef\pmmp\form\CustomMapVectorDataListForm;
use game_chef\pmmp\hotbar_menu\FFAGameSpawnPointsHotbarMenu;
use game_chef\store\FFAGameMapSpawnPointEditorStore;
use game_chef\TaskSchedulerStorage;
use pocketmine\Player;

class FFAGameMapDetailForm extends SimpleForm
{
    private FFAGameMapData $ffaGameMapData;

    public function __construct(FFAGameMapData $ffaGameMapData) {
        $this->ffaGameMapData = $ffaGameMapData;

        parent::__construct($ffaGameMapData->getName(), "", [
            new SimpleFormButton(
                "ゲームタイプの変更",
                null,
                function (Player $player) {
                    $player->sendForm(new EditFFAGameMapGameTypeForm($this->ffaGameMapData));
                }
            ),
            new SimpleFormButton(
                "スポーン地点の変更",
                null,
                function (Player $player) {
                    $editor = new FFAGameMapSpawnPointEditor($this->ffaGameMapData, $player, TaskSchedulerStorage::get());
                    try {
                        FFAGameMapSpawnPointEditorStore::add($player->getName(), $editor);
                    } catch (\Exception $e) {
                        $player->sendMessage($e);
                        return;
                    }

                    try {
                        $menu = new FFAGameSpawnPointsHotbarMenu($player, $this->ffaGameMapData);
                        $menu->send();
                        $editor->start();
                    } catch (\Exception $exception) {
                        $player->sendMessage($exception->getMessage());
                        return;
                    }
                }
            ),
            new SimpleFormButton(
                "カスタム座標データの管理",
                null,
                function (Player $player) {
                    $player->sendForm(new CustomMapVectorDataListForm($this->ffaGameMapData));
                }
            ),
            new SimpleFormButton(
                "配列型のカスタム座標データの管理",
                null,
                function (Player $player) {
                    //TODO:
                }
            ),
            new SimpleFormButton(
                "削除",
                null,
                function (Player $player) {
                    $player->sendForm(new ConfirmDeletingFFAGameMap($this->ffaGameMapData));
                }
            )
        ]);
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new FFAGameMapListForm());
    }
}