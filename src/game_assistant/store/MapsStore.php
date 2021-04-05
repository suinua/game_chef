<?php


namespace game_assistant\store;


use game_assistant\models\GameType;
use game_assistant\models\Map;
use game_assistant\models\FFAGameMap;
use game_assistant\models\TeamGameMap;

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
     * @return Map
     * @throws \Exception
     *
     * numberOfPlayersを設定すると、スポーン地点よりプレイヤーが多い場合エラーを吐きます
     * 同じところにスポーンしていい場合を除き、設定することを推奨します
     */
    static function borrowFFAGameMap(string $name, GameType $gameType, ?int $numberOfPlayers = null): Map {
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

    static function return(Map $map): Map { }
}