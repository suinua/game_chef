<?php


namespace game_chef\pmmp\hotbar_menu;


use game_chef\models\TeamDataOnMap;
use game_chef\models\TeamGameMap;
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
                new HotbarMenuItem(ItemIds::TNT, "削除", function (Player $player) use ($map, $teamDataOnMap, $targetSpawnPoint) {
                    $newSpawnPoints = [];
                    foreach ($teamDataOnMap->getSpawnPoints() as $spawnPoint) {
                        if (!$spawnPoint->equals($targetSpawnPoint)) {
                            $newSpawnPoints[] = $targetSpawnPoint;
                        }
                    }

                    try {
                        $newTeam = new TeamDataOnMap(
                            $teamDataOnMap->getTeamName(),
                            $teamDataOnMap->getTeamColorFormat(),
                            $teamDataOnMap->getMaxPlayer(),
                            $teamDataOnMap->getMinPlayer(),
                            $newSpawnPoints,
                            $teamDataOnMap->getCustomTeamVectorDataList(),
                            $teamDataOnMap->getCustomTeamVectorsDataList()
                        );

                        TeamGameMapService::updateTeamData($map, $newTeam);
                        $editor = TeamGameMapSpawnPointEditorStore::get($player->getName());
                        $editor->reloadMap();
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