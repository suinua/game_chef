<?php


namespace game_chef\repository;


use game_chef\DataFolderPath;
use game_chef\models\TeamGameMap;
use game_chef\repository\dto\TeamGameMapDTO;

class TeamGameMapRepository
{
    static function add(TeamGameMap $teamGameMap) :void {
        //TODO:かぶってたらエラー
        $array = TeamGameMapDTO::encode($teamGameMap);
        file_put_contents(DataFolderPath::$teamGameMaps . $teamGameMap->getName() . ".json", json_encode($array));
    }

    static function update(TeamGameMap $teamGameMap): void {
        //TODO:なかったらエラー
        $array = TeamGameMapDTO::encode($teamGameMap);
        file_put_contents(DataFolderPath::$teamGameMaps . $teamGameMap->getName() . ".json", json_encode($array));
    }

    static function delete(string $mapName): void {
        //TODO:なかったらエラー
        unlink(DataFolderPath::$teamGameMaps . $mapName . ".json");
    }

    static function loadAll(string $mapName):void{
        //TODO:なかったらエラー
    }

    static function loadByName(string $mapName):void{
        //TODO:なかったらエラー
    }
}