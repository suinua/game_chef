<?php


namespace game_chef\services;


use game_chef\models\TeamDataOnMap;
use game_chef\models\TeamGameMap;
use game_chef\repository\TeamGameMapRepository;
use game_chef\store\MapsStore;

class TeamGameMapService
{
    /**
     * @param string $name
     * @param string $levelName
     * @param array $gameTypeList
     * @throws \Exception
     */
    static function create(string $name, string $levelName, array $gameTypeList): void {
        $map = new TeamGameMap($name, $levelName, $gameTypeList, [], [], []);
        TeamGameMapRepository::add($map);
    }

    /**
     * @param string $name
     * @throws \Exception
     */
    static function delete(string $name): void {
        if (in_array($name, MapsStore::getLoanOutTeamGameMapName())) {
            throw new \Exception("使用中のマップは削除できません");
        }

        TeamGameMapRepository::delete($name);
    }

    /**
     * @param TeamGameMap $teamGameMap
     * @throws \Exception
     */
    static function update(TeamGameMap $teamGameMap): void {
        if (in_array($teamGameMap->getName(), MapsStore::getLoanOutTeamGameMapName())) {
            throw new \Exception("使用中のマップは編集できません");
        }

        TeamGameMapRepository::update($teamGameMap);
    }

    /**
     * @param string $mapName
     * @param TeamDataOnMap $target
     * @throws \Exception
     */
    static function updateTeamData(string $mapName, TeamDataOnMap $target): void {
        $map = TeamGameMapRepository::loadByName($mapName);
        $newTeams = [];
        foreach ($map->getTeamDataList() as $teamDataOnMap) {
            if ($teamDataOnMap->getTeamName() === $target->getTeamName()) {
                $newTeams[] = $target;
            } else {
                $newTeams[] = $teamDataOnMap;
            }
        }

        self::update(new TeamGameMap(
            $map->getName(),
            $map->getLevelName(),
            $map->getAdaptedGameTypes(),
            $map->getCustomMapVectorDataList(),
            $map->getCustomMapArrayVectorDataList(),
            $newTeams
        ));
    }

    /**
     * @param string $mapName
     * @param TeamDataOnMap $target
     * @throws \Exception
     */
    static function addTeamData(string $mapName, TeamDataOnMap $target): void {
        $map = TeamGameMapRepository::loadByName($mapName);
        foreach ($map->getTeamDataList() as $teamDataOnMap) {
            if ($teamDataOnMap->getTeamName() === $target->getTeamName()) {
                throw new \Exception("同じ名前のチーム({$target->getTeamName()})を登録することはできません");
            }
        }

        self::update(new TeamGameMap(
            $map->getName(),
            $map->getLevelName(),
            $map->getAdaptedGameTypes(),
            $map->getCustomMapVectorDataList(),
            $map->getCustomMapArrayVectorDataList(),
            $map->getTeamDataList() + [$target]
        ));
    }
}