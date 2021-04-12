<?php


namespace game_chef\pmmp\hotbar_menu;


use game_chef\models\TeamDataOnMap;
use game_chef\models\TeamGameMap;
use game_chef\services\TeamGameMapService;
use game_chef\store\TeamGameMapSpawnPointEditorStore;
use pocketmine\block\Block;
use pocketmine\item\ItemIds;
use pocketmine\Player;

class TeamGameSpawnPointsHotbarMenu extends HotbarMenu
{
    public function __construct(Player $player, TeamGameMap $teamGameMap, TeamDataOnMap $teamDataOnMap) {
        parent::__construct($player, [
            new HotbarMenuItem(ItemIds::BOOK, "スポーン地点を追加", function (Player $player,Block $block) use ($teamGameMap,$teamDataOnMap) {
                $spawnPoints = $teamDataOnMap->getSpawnPoints();
                foreach ($spawnPoints as $spawnPoint) {
                    if ($spawnPoint->equals($block->asVector3())) {
                        $player->sendMessage("TeamGameMapでは、１チームが同じ座標に２つ以上スポーン地点を追加することはできません");
                        return;
                    }
                }

                $spawnPoints[] = $block->asVector3();

                try {
                    $newTeam = new TeamDataOnMap(
                        $teamDataOnMap->getTeamName(),
                        $teamDataOnMap->getTeamColorFormat(),
                        $teamDataOnMap->getMaxPlayer(),
                        $teamDataOnMap->getMinPlayer(),
                        $spawnPoints,
                    );

                    TeamGameMapService::updateTeamData($teamGameMap, $newTeam);
                    $editor = TeamGameMapSpawnPointEditorStore::get($player->getName());
                    $editor->reloadMap();
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