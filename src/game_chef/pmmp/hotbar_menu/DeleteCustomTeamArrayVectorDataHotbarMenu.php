<?php


namespace game_chef\pmmp\hotbar_menu;


use game_chef\models\map_data\CustomTeamArrayVectorData;
use game_chef\models\map_data\TeamDataOnMap;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\repository\TeamGameMapDataRepository;
use game_chef\store\EditorsStore;
use pocketmine\block\Block;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\Player;

class DeleteCustomTeamArrayVectorDataHotbarMenu extends HotbarMenu
{
    private TeamGameMapData $mapData;
    private TeamDataOnMap $teamData;
    private CustomTeamArrayVectorData $customTeamArrayVectorData;

    public function __construct(Player $player, TeamGameMapData $mapData, TeamDataOnMap $teamData, CustomTeamArrayVectorData $customTeamArrayVectorData, Vector3 $target) {
        $this->mapData = $mapData;
        $this->teamData = $teamData;
        $this->customTeamArrayVectorData = $customTeamArrayVectorData;

        parent::__construct($player,
            [
                new HotbarMenuItem(
                    ItemIds::TNT,
                    0,
                    "削除",
                    function (Player $player) use ($target) {
                        $this->customTeamArrayVectorData->deleteVector3($target);
                        $this->teamData->updateCustomArrayVectorData($this->customTeamArrayVectorData);

                        TeamGameMapDataRepository::update($this->mapData);

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
        $menu = new CustomTeamArrayVectorDataHotbarMenu($this->player, $this->mapData, $this->teamData, $this->customTeamArrayVectorData);
        $menu->send();
    }
}