<?php


namespace game_chef\pmmp\hotbar_menu;


use game_chef\models\editors\CustomMapArrayVectorDataEditor;
use game_chef\models\map_data\CustomMapArrayVectorData;
use game_chef\models\map_data\FFAGameMapData;
use game_chef\models\map_data\MapData;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\pmmp\form\CustomMapArrayVectorDataListForm;
use game_chef\repository\FFAGameMapDataRepository;
use game_chef\repository\TeamGameMapDataRepository;
use game_chef\store\EditorsStore;
use game_chef\TaskSchedulerStorage;
use pocketmine\block\Block;
use pocketmine\item\ItemIds;
use pocketmine\Player;

class CustomMapArrayVectorDataHotbarMenu extends HotbarMenu
{
    private MapData $mapData;
    private CustomMapArrayVectorData $customMapArrayVectorData;

    public function __construct(Player $player, MapData $mapData, CustomMapArrayVectorData $customMapArrayVectorData) {
        $this->mapData = $mapData;
        $this->customMapArrayVectorData = $customMapArrayVectorData;
        parent::__construct($player, [
            new HotbarMenuItem(
                ItemIds::BOOK,
                0,
                "追加",
                null,
                function (Player $player, Block $block) {
                    try {
                        $this->customMapArrayVectorData->addVector3($block->asVector3());
                        $this->mapData->updateCustomMapArrayVectorData($this->customMapArrayVectorData);
                        if ($this->mapData instanceof TeamGameMapData) {
                            TeamGameMapDataRepository::update($this->mapData);
                        } else if ($this->mapData instanceof FFAGameMapData) {
                            FFAGameMapDataRepository::update($this->mapData);
                        }

                        $editor = EditorsStore::get($player->getName());
                        $editor->reloadMap();
                    } catch (\Exception $exception) {
                        $player->sendMessage($exception->getMessage());
                    }
                }
            ),
            new HotbarMenuItem(
                ItemIds::FEATHER,
                0,
                "戻る",
                function (Player $player) {
                    $this->close();
                    $player->sendForm(new CustomMapArrayVectorDataListForm($this->mapData));
                }
            )
            //todo:ustomMapArrayVectorDataの削除機能
        ]);
    }

    public function send(): void {
        $editor = new CustomMapArrayVectorDataEditor($this->mapData, $this->customMapArrayVectorData, $this->player, TaskSchedulerStorage::get());
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