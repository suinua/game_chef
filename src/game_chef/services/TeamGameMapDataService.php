<?php


namespace game_chef\services;


use game_chef\models\map_data\TeamGameMapData;
use game_chef\repository\TeamGameMapDataRepository;
use game_chef\store\MapsStore;

class TeamGameMapDataService
{
    /**
     * @param string $name
     * @param string $levelName
     * @param array $gameTypeList
     * @throws \Exception
     */
    static function create(string $name, string $levelName, array $gameTypeList): void {
        $map = new TeamGameMapData($name, $levelName, $gameTypeList, [], [], []);
        TeamGameMapDataRepository::add($map);
    }

    /**
     * @param string $name
     * @throws \Exception
     */
    static function delete(string $name): void {
        if (in_array($name, MapsStore::getLoanOutTeamGameMapName())) {
            throw new \Exception("使用中のマップは削除できません");
        }

        TeamGameMapDataRepository::delete($name);
    }

    /**
     * @param TeamGameMapData $teamGameMapData
     * @throws \Exception
     */
    static function update(TeamGameMapData $teamGameMapData): void {
        if (in_array($teamGameMapData->getName(), MapsStore::getLoanOutTeamGameMapName())) {
            throw new \Exception("使用中のマップは編集できません");
        }

        TeamGameMapDataRepository::update($teamGameMapData);
    }
}