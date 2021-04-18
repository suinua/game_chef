<?php


namespace game_chef\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use game_chef\models\editors\CustomMapVectorDataEditor;
use game_chef\models\map_data\FFAGameMapData;
use game_chef\models\map_data\MapData;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\pmmp\form\ffa_game_map_forms\FFAGameMapDetailForm;
use game_chef\pmmp\form\team_game_map_forms\TeamGameMapDetailForm;
use game_chef\pmmp\hotbar_menu\CustomMapVectorDataHotbarMenu;
use game_chef\store\CustomMapVectorDataEditorStore;
use game_chef\TaskSchedulerStorage;
use pocketmine\Player;

class CustomMapVectorDataListForm extends SimpleForm
{
    private MapData $mapData;

    public function __construct(MapData $mapData) {
        $this->mapData = $mapData;
        $buttons = [
            new SimpleFormButton(
                "追加",
                null,
                function (Player $player) {
                    $player->sendForm(new CreateCustomMapVectorDataForm($this->mapData));
                }
            )
        ];
        foreach ($mapData->getCustomMapVectorDataList() as $customMapVectorData) {
            $vector = $customMapVectorData->getVector3();
            $buttons[] = new SimpleFormButton(
                $customMapVectorData->getKey() . ":" . strval($vector),
                null,
                function (Player $player) use ($customMapVectorData) {
                    $menu = new CustomMapVectorDataHotbarMenu($player, $this->mapData, $customMapVectorData);
                    $menu->send();
                }
            );
        }

        parent::__construct("カスタム座標データ", $mapData->getName(), $buttons);
    }

    function onClickCloseButton(Player $player): void {
        if ($this->mapData instanceof TeamGameMapData) {
            $player->sendForm(new TeamGameMapDetailForm($this->mapData));
        } else if ($this->mapData instanceof FFAGameMapData) {
            $player->sendForm(new FFAGameMapDetailForm($this->mapData));
        }
    }
}