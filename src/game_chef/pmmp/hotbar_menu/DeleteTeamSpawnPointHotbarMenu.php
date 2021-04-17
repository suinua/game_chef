<?php


namespace game_chef\pmmp\hotbar_menu;


use game_chef\models\map_data\TeamDataOnMap;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\repository\TeamGameMapDataRepository;
use game_chef\store\TeamGameMapSpawnPointEditorStore;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\Player;

class DeleteTeamSpawnPointHotbarMenu extends HotbarMenu
{
    private TeamGameMapData $mapData;
    private TeamDataOnMap $teamData;

    public function __construct(Player $player, TeamGameMapData $mapData, TeamDataOnMap $teamDataOnMap, Vector3 $targetSpawnPoint) {
        $this->mapData = $mapData;
        $this->teamData = $teamDataOnMap;
        parent::__construct($player,
            [
                new HotbarMenuItem(ItemIds::FEATHER, "戻る", function () {
                    $this->close();
                }),
                new HotbarMenuItem(ItemIds::TNT, "削除", function (Player $player) use ($targetSpawnPoint) {
                    try {
                        $this->teamData->deleteSpawnPoint($targetSpawnPoint);
                        $this->mapData->updateTeamData($this->teamData);

                        $editor = TeamGameMapSpawnPointEditorStore::get($player->getName());
                        $editor->reloadMap();

                        $this->mapData = TeamGameMapDataRepository::loadByName($this->mapData->getName());
                        $this->teamData = $this->mapData->getTeamData($this->teamData->getName());
                    } catch (\Exception $exception) {
                        $player->sendMessage($exception->getMessage());
                    }

                    $this->close();
                })
            ]
        );
    }

    public function close(): void {
        parent::close();
        $menu = new TeamGameSpawnPointsHotbarMenu($this->player, $this->mapData, $this->teamData);
        $menu->send();
    }
}