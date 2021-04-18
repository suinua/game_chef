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
use game_chef\store\CustomMapArrayVectorDataEditorStore;
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
                "追加",
                function (Player $player, Block $block) {
                    try {
                        $this->customMapArrayVectorData->addVector3($block->asVector3());
                        $this->mapData->updateCustomMapArrayVectorData($this->customMapArrayVectorData);
                        if ($this->mapData instanceof TeamGameMapData) {
                            TeamGameMapDataRepository::update($this->mapData);
                        } else if ($this->mapData instanceof FFAGameMapData) {
                            FFAGameMapDataRepository::update($this->mapData);
                        }

                        $editor = CustomMapArrayVectorDataEditorStore::get($player->getName());
                        $editor->reloadMap();
                    } catch (\Exception $exception) {
                        $player->sendMessage($exception->getMessage());
                    }
                }
            ),
            new HotbarMenuItem(
                ItemIds::FEATHER,
                "戻る",
                function (Player $player, Block $block) {
                    $this->close();
                    $player->sendForm(new CustomMapArrayVectorDataListForm($this->mapData));
                }
            )
        ]);
    }

    public function send(): void {
        $editor = new CustomMapArrayVectorDataEditor($this->mapData, $this->customMapArrayVectorData, $this->player, TaskSchedulerStorage::get());
        try {
            CustomMapArrayVectorDataEditorStore::add($this->player->getName(), $editor);
            $editor->start();
        } catch (\Exception $e) {
            $this->player->sendMessage($e);
            return;
        }

        parent::send();
    }

    public function close(): void {
        try {
            CustomMapArrayVectorDataEditorStore::delete($this->player->getName());
        } catch (\Exception $e) {
            $this->player->sendMessage($e);
        }

        parent::close();
    }
}