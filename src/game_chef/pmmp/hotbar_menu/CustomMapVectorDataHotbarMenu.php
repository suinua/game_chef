<?php


namespace game_chef\pmmp\hotbar_menu;


use game_chef\models\editors\CustomMapVectorDataEditor;
use game_chef\models\map_data\CustomMapVectorData;
use game_chef\models\map_data\FFAGameMapData;
use game_chef\models\map_data\MapData;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\pmmp\form\CustomMapVectorDataListForm;
use game_chef\repository\FFAGameMapDataRepository;
use game_chef\repository\TeamGameMapDataRepository;
use game_chef\store\EditorsStore;
use game_chef\TaskSchedulerStorage;
use pocketmine\block\Block;
use pocketmine\item\ItemIds;
use pocketmine\Player;

class CustomMapVectorDataHotbarMenu extends HotbarMenu
{
    private MapData $mapData;
    private CustomMapVectorData $customMapVectorData;

    public function __construct(Player $player, MapData $mapData, CustomMapVectorData $customMapVectorData) {
        $this->mapData = $mapData;
        $this->customMapVectorData = $customMapVectorData;
        parent::__construct($player, [
            new HotbarMenuItem(
                ItemIds::BOOK,
                0,
                "移動",
                null,
                function (Player $player, Block $block) {
                    try {
                        $this->customMapVectorData = new CustomMapVectorData($this->customMapVectorData->getKey(), $block->asVector3());
                        $this->mapData->updateCustomMapVectorData($this->customMapVectorData);
                        if ($this->mapData instanceof TeamGameMapData) {
                            TeamGameMapDataRepository::update($this->mapData);
                            $this->mapData = TeamGameMapDataRepository::loadByName($this->mapData->getName());
                        } else if ($this->mapData instanceof FFAGameMapData) {
                            FFAGameMapDataRepository::update($this->mapData);
                            $this->mapData = FFAGameMapDataRepository::loadByName($this->mapData->getName());
                        }

                        $editor = EditorsStore::get($player->getName());
                        $editor->reloadMap();
                    } catch (\Exception $exception) {
                        $player->sendMessage($exception->getMessage());
                    }
                }
            ),
            new HotbarMenuItem(
                ItemIds::TNT,
                0,
                "削除",
                function (Player $player, Block $block) {
                    $this->mapData->deleteCustomMapVectorData($this->customMapVectorData);
                    if ($this->mapData instanceof TeamGameMapData) {
                        TeamGameMapDataRepository::update($this->mapData);
                        $this->mapData = TeamGameMapDataRepository::loadByName($this->mapData->getName());
                    } else if ($this->mapData instanceof FFAGameMapData) {
                        FFAGameMapDataRepository::update($this->mapData);
                        $this->mapData = FFAGameMapDataRepository::loadByName($this->mapData->getName());
                    }
                    $this->close();
                    $player->sendForm(new CustomMapVectorDataListForm($this->mapData));
                }
            ),
            new HotbarMenuItem(
                ItemIds::FEATHER,
                0,
                "戻る",
                function (Player $player, Block $block) {
                    $this->close();
                    $player->sendForm(new CustomMapVectorDataListForm($this->mapData));
                }
            )
        ]);
    }

    public function send(): void {
        $editor = new CustomMapVectorDataEditor($this->mapData, $this->customMapVectorData, $this->player, TaskSchedulerStorage::get());
        try {
            EditorsStore::add($this->player->getName(), $editor);
            $editor->start();
        } catch (\Exception $e) {
            $this->player->sendMessage($e);
            return;
        }

        parent::send();
    }

    public function close(): void {
        try {
            EditorsStore::delete($this->player->getName());
        } catch (\Exception $e) {
            $this->player->sendMessage($e);
        }

        parent::close();
    }
}