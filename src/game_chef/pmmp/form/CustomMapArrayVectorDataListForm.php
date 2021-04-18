<?php


namespace game_chef\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use game_chef\models\map_data\FFAGameMapData;
use game_chef\models\map_data\MapData;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\pmmp\form\ffa_game_map_forms\FFAGameMapDetailForm;
use game_chef\pmmp\form\team_game_map_forms\TeamGameMapDetailForm;
use game_chef\pmmp\hotbar_menu\CustomMapArrayVectorDataHotbarMenu;
use pocketmine\Player;

class CustomMapArrayVectorDataListForm extends SimpleForm
{
    private MapData $mapData;

    public function __construct(MapData $mapData) {
        $this->mapData = $mapData;
        $buttons = [
            new SimpleFormButton(
                "追加",
                null,
                function (Player $player) {
                    $player->sendForm(new CreateCustomMapArrayVectorDataForm($this->mapData));
                }
            )
        ];
        foreach ($mapData->getCustomMapArrayVectorDataList() as $customMapArrayVectorData) {
            $buttons[] = new SimpleFormButton(
                $customMapArrayVectorData->getKey(),
                null,
                function (Player $player) use ($customMapArrayVectorData) {
                    $menu = new CustomMapArrayVectorDataHotbarMenu($player, $this->mapData, $customMapArrayVectorData);
                    $menu->send();
                }
            );
        }

        parent::__construct("配列型カスタム座標データ", $mapData->getName(), $buttons);
    }

    function onClickCloseButton(Player $player): void {
        if ($this->mapData instanceof TeamGameMapData) {
            $player->sendForm(new TeamGameMapDetailForm($this->mapData));
        } else if ($this->mapData instanceof FFAGameMapData) {
            $player->sendForm(new FFAGameMapDetailForm($this->mapData));
        }
    }
}