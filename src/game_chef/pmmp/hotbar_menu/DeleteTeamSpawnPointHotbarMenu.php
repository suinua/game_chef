<?php


namespace game_chef\pmmp\hotbar_menu;


use game_chef\models\TeamDataOnMap;
use game_chef\models\TeamGameMap;
use game_chef\repository\TeamGameMapRepository;
use game_chef\services\TeamGameMapService;
use game_chef\store\TeamGameMapSpawnPointEditorStore;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\Player;

class DeleteTeamSpawnPointHotbarMenu extends HotbarMenu
{
    private TeamGameMap $map;
    private TeamDataOnMap $teamData;

    public function __construct(Player $player, TeamGameMap $map, TeamDataOnMap $teamDataOnMap, Vector3 $targetSpawnPoint) {
        $this->map = $map;
        $this->teamData = $teamDataOnMap;
        parent::__construct($player,
            [
                new HotbarMenuItem(ItemIds::FEATHER, "戻る", function () {
                    $this->close();
                }),
                new HotbarMenuItem(ItemIds::TNT, "削除", function (Player $player) use ($targetSpawnPoint) {
                    $newSpawnPoints = [];
                    foreach ($this->teamData->getSpawnPoints() as $spawnPoint) {
                        if (!$spawnPoint->equals($targetSpawnPoint)) {
                            $newSpawnPoints[] = $targetSpawnPoint;
                        }
                    }

                    try {
                        $newTeam = new TeamDataOnMap(
                            $this->teamData->getTeamName(),
                            $this->teamData->getTeamColorFormat(),
                            $this->teamData->getMaxPlayer(),
                            $this->teamData->getMinPlayer(),
                            $newSpawnPoints,
                            $this->teamData->getCustomTeamVectorDataList(),
                            $this->teamData->getCustomTeamArrayVectorDataList()
                        );

                        TeamGameMapService::updateTeamData($this->map, $newTeam);
                        $this->map = TeamGameMapRepository::loadByName($this->map->getName());
                        $editor = TeamGameMapSpawnPointEditorStore::get($player->getName());
                        $editor->reloadMap();
                        $this->map = TeamGameMapRepository::loadByName($this->map->getName());
                        $this->teamData = $this->map->getTeamDataOnMapByName($this->teamData->getTeamName());
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
        $menu = new TeamGameSpawnPointsHotbarMenu($this->player, $this->map, $this->teamData);
        $menu->send();
    }
}