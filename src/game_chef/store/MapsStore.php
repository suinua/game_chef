<?php


namespace game_chef\store;


use game_chef\models\GameType;
use game_chef\models\Map;
use game_chef\models\FFAGameMap;
use game_chef\models\TeamGameMap;

class MapsStore
{
    /**
     * @var FFAGameMap[]
     */
    static array $ffaGameMaps;
    /**
     * @var TeamGameMap[]
     */
    static array $teamGameMaps;

    /**
     * @param string $name
     * @param GameType $gameType
     * @param int|null $numberOfPlayers
     * @return FFAGameMap
     * @throws \Exception numberOfPlayersを設定すると、スポーン地点よりプレイヤーが多い場合エラーを吐きます
     * 同じところにスポーンしていい場合を除き、設定することを推奨します
     */
    static function borrowFFAGameMap(string $name, GameType $gameType, ?int $numberOfPlayers = null): FFAGameMap {
        foreach (self::$ffaGameMaps as $key => $map) {
            if ($map->getName() === $name) {
                if ($map->isAdaptedGameType($gameType)) {
                    if ($numberOfPlayers !== null) {
                        if ($map->getSpawnPoints() <= $numberOfPlayers) {
                            throw new \Exception("そのマップ({$name})はその人数({$numberOfPlayers})に対応していません");
                        }
                    }
                    unset(self::$ffaGameMaps[$key]);
                    self::$ffaGameMaps = array_values(self::$ffaGameMaps);
                    return $map;
                } else {
                    throw new \Exception("そのマップ({$name})はそのゲームタイプ({$gameType})に対応していません");
                }
            }
        }

        throw new \Exception("そのマップ({$name})が存在しないか、すでに使用しています");
    }


    /**
     * @param string $name
     * @param GameType $gameType
     * @param int $numberOfTeams
     * @return TeamGameMap
     * @throws \Exception
     */
    static function borrowTeamGameMap(string $name, GameType $gameType, int $numberOfTeams): TeamGameMap {
        foreach (self::$teamGameMaps as $key => $map) {
            if ($map->getName() === $name) {
                if ($map->isAdaptedGameType($gameType)) {
                    if ($numberOfTeams !== null) {
                        //登録してあるチームデータより、多いチームすうはムリ
                        if ($map->getTeamDataList() <= $numberOfTeams) {
                            throw new \Exception("そのマップ({$name})はそのチーム数({$numberOfTeams})に対応していません");
                        }
                    }
                    unset(self::$ffaGameMaps[$key]);
                    self::$ffaGameMaps = array_values(self::$ffaGameMaps);
                    return $map;
                } else {
                    throw new \Exception("そのマップ({$name})はそのゲームタイプ({$gameType})に対応していません");
                }
            }
        }

        throw new \Exception("そのマップ({$name})が存在しないか、すでに使用しています");
    }

    static function returnFFAGameMap(FFAGameMap $map): void { }
    static function returnTeamGameMap(TeamGameMap $map): void { }

    static function updateFFAGameMap(FFAGameMap $map): void { }
    static function updateTeamGameMap(TeamGameMap $map): void { }

    static function registerFFAGameMap(FFAGameMap $map): void { }
    static function registerTeamGameMap(TeamGameMap $map): void { }

    static function removeFFAGameMap(FFAGameMap $map): void { }
    static function removeTeamGameMap(TeamGameMap $map): void { }
}