<?php


namespace game_chef\pmmp\hotbar_menu;


use game_chef\models\editors\CustomTeamArrayVectorDataEditor;
use game_chef\models\map_data\CustomTeamArrayVectorData;
use game_chef\models\map_data\TeamDataOnMap;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\pmmp\form\CustomMapArrayVectorDataListForm;
use game_chef\repository\TeamGameMapDataRepository;
use game_chef\store\EditorsStore;
use game_chef\TaskSchedulerStorage;
use pocketmine\block\Block;
use pocketmine\item\ItemIds;
use pocketmine\Player;

class CustomTeamArrayVectorDataHotbarMenu extends HotbarMenu
{
    private TeamGameMapData $mapData;
    private TeamDataOnMap $teamData;
    private CustomTeamArrayVectorData $customTeamArrayVectorData;

    public function __construct(Player $player, TeamGameMapData $mapData, TeamDataOnMap $teamData, CustomTeamArrayVectorData $customTeamArrayVectorData) {
        $this->mapData = $mapData;
        $this->teamData = $teamData;
        $this->customTeamArrayVectorData = $customTeamArrayVectorData;
        parent::__construct($player, [
            new HotbarMenuItem(
                ItemIds::BOOK,
                "追加",
                function (Player $player, Block $block) {
                    try {
                        $this->customTeamArrayVectorData->addVector3($block->asVector3());
                        $this->teamData->updateCustomArrayVectorData($this->customTeamArrayVectorData);
                        $this->mapData->updateTeamData($this->teamData);

                        TeamGameMapDataRepository::update($this->mapData);
                        $editor = EditorsStore::get($player->getName());
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
            //todo:削除機能
        ]);
    }

    public function send(): void {
        $editor = new CustomTeamArrayVectorDataEditor($this->mapData, $this->teamData, $this->customTeamArrayVectorData, $this->player, TaskSchedulerStorage::get());
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