<?php


namespace game_chef\pmmp\hotbar_menu;


use game_chef\models\TeamDataOnMap;
use game_chef\models\TeamGameMap;
use game_chef\repository\TeamGameMapRepository;
use game_chef\services\TeamGameMapService;
use game_chef\store\TeamGameMapSpawnPointEditorStore;
use pocketmine\block\Block;
use pocketmine\item\ItemIds;
use pocketmine\Player;

class TeamGameSpawnPointsHotbarMenu extends HotbarMenu
{
    private TeamGameMap $map;
    private TeamDataOnMap $teamDataOnMap;

    public function __construct(Player $player, TeamGameMap $teamGameMap, TeamDataOnMap $teamDataOnMap) {
        $this->map = $teamGameMap;
        $this->teamDataOnMap = $teamDataOnMap;

        parent::__construct($player, [
            new HotbarMenuItem(ItemIds::BOOK, "スポーン地点を追加", function (Player $player,Block $block)  {
                $spawnPoints = $this->teamDataOnMap->getSpawnPoints();
                foreach ($spawnPoints as $spawnPoint) {
                    if ($spawnPoint->equals($block->asVector3())) {
                        $player->sendMessage("TeamGameMapでは、１チームが同じ座標に２つ以上スポーン地点を追加することはできません");
                        return;
                    }
                }

                $spawnPoints[] = $block->asVector3();

                try {
                    $newTeam = new TeamDataOnMap(
                        $this->teamDataOnMap->getTeamName(),
                        $this->teamDataOnMap->getTeamColorFormat(),
                        $this->teamDataOnMap->getMaxPlayer(),
                        $this->teamDataOnMap->getMinPlayer(),
                        $spawnPoints,
                        $this->teamDataOnMap->getCustomTeamVectorDataList(),
                        $this->teamDataOnMap->getCustomTeamArrayVectorDataList()
                    );

                    TeamGameMapService::updateTeamData($this->map->getName(), $newTeam);
                    $editor = TeamGameMapSpawnPointEditorStore::get($player->getName());
                    $editor->reloadMap();
                    $this->map = TeamGameMapRepository::loadByName($this->map->getName());
                    $this->teamDataOnMap = $this->map->getTeamDataOnMapByName($this->teamDataOnMap->getTeamName());
                } catch (\Exception $exception) {
                    $player->sendMessage($exception->getMessage());
                }
            }),
            new HotbarMenuItem(ItemIds::FEATHER, "戻る", function (Player $player) {
                try {
                    TeamGameMapSpawnPointEditorStore::delete($player->getName());
                } catch (\Exception $exception) {
                    $player->sendMessage($exception->getMessage());
                }
                $this->close();
            })
        ]);
    }
}