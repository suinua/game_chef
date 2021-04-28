<?php


namespace game_chef\pmmp\hotbar_menu;


use game_chef\models\editors\CustomTeamVectorDataEditor;
use game_chef\models\map_data\CustomTeamVectorData;
use game_chef\models\map_data\TeamDataOnMap;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\pmmp\form\team_game_map_forms\CustomTeamVectorDataListForm;
use game_chef\repository\TeamGameMapDataRepository;
use game_chef\store\EditorsStore;
use game_chef\TaskSchedulerStorage;
use pocketmine\block\Block;
use pocketmine\item\ItemIds;
use pocketmine\Player;

class CustomTeamVectorDataHotbarMenu extends HotbarMenu
{
    private TeamGameMapData $teamGameMapData;
    private TeamDataOnMap $teamData;
    private CustomTeamVectorData $customTeamVectorData;

    public function __construct(Player $player, TeamGameMapData $teamGameMapData, TeamDataOnMap $teamDataOnMap, CustomTeamVectorData $customTeamVectorData) {
        $this->teamGameMapData = $teamGameMapData;
        $this->teamData = $teamDataOnMap;
        $this->customTeamVectorData = $customTeamVectorData;
        parent::__construct($player, [
            new HotbarMenuItem(
                ItemIds::BOOK,
                0,
                "移動",
                null,
                function (Player $player, Block $block) {
                    try {
                        $this->customTeamVectorData = new CustomTeamVectorData($this->customTeamVectorData->getKey(), $this->teamData->getName(), $block->asVector3());
                        $this->teamData->updateCustomVectorData($this->customTeamVectorData);
                        $this->teamGameMapData->updateTeamData($this->teamData);
                        TeamGameMapDataRepository::update($this->teamGameMapData);

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
                function (Player $player) {
                    $this->teamData->deleteCustomVectorData($this->customTeamVectorData);
                    $this->teamGameMapData->updateTeamData($this->teamData);
                    TeamGameMapDataRepository::update($this->teamGameMapData);

                    $this->close();
                    $player->sendForm(new CustomTeamVectorDataListForm($this->teamGameMapData, $this->teamData));
                }
            ),
            new HotbarMenuItem(
                ItemIds::FEATHER,
                0,
                "戻る",
                function (Player $player) {
                    $this->close();
                    $player->sendForm(new CustomTeamVectorDataListForm($this->teamGameMapData, $this->teamData));
                }
            )
        ]);
    }

    public function send(): void {
        $editor = new CustomTeamVectorDataEditor($this->teamGameMapData, $this->teamData, $this->customTeamVectorData, $this->player, TaskSchedulerStorage::get());
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