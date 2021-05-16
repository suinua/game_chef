<?php


namespace game_chef\pmmp\hotbar_menu;


use game_chef\models\map_data\CustomMapArrayVectorData;
use game_chef\models\map_data\FFAGameMapData;
use game_chef\models\map_data\MapData;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\repository\FFAGameMapDataRepository;
use game_chef\repository\TeamGameMapDataRepository;
use game_chef\store\EditorsStore;
use pocketmine\block\Block;
use pocketmine\item\ItemIds;
use pocketmine\Player;

class DeleteCustomMapArrayVectorDataHotbarMenu extends HotbarMenu
{
    private MapData $mapData;
    private CustomMapArrayVectorData $customMapArrayVectorData;

    public function __construct(Player $player, MapData $mapData, CustomMapArrayVectorData $customMapArrayVectorData) {
        $this->mapData = $mapData;
        $this->customMapArrayVectorData = $customMapArrayVectorData;
        parent::__construct($player,
            [
                new HotbarMenuItem(
                    ItemIds::TNT,
                    0,
                    "削除",
                    function (Player $player, Block $block) {
                        $this->customMapArrayVectorData->deleteVector3($block->asVector3());
                        $this->mapData->updateCustomMapArrayVectorData($this->customMapArrayVectorData);

                        if ($this->mapData instanceof TeamGameMapData) {
                            TeamGameMapDataRepository::update($this->mapData);
                        } else if ($this->mapData instanceof FFAGameMapData) {
                            FFAGameMapDataRepository::update($this->mapData);
                        }

                        $editor = EditorsStore::get($player->getName());
                        $editor->reloadMap();
                        $this->close();
                    }
                ),
                new HotbarMenuItem(
                    ItemIds::FEATHER,
                    0,
                    "戻る",
                    function (Player $player) {
                        $this->close();
                    }
                )
            ]
        );
    }

    public function close(): void {
        parent::close();
        $menu = new CustomMapArrayVectorDataHotbarMenu($this->player, $this->mapData, $this->customMapArrayVectorData);
        $menu->send();
    }
}