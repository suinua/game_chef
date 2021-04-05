<?php


namespace game_assistant\store;


use game_assistant\models\GameType;
use game_assistant\models\Map;
use game_assistant\models\SoloGameMap;

class MapsStore
{
    /**
     * @var SoloGameMap[]
     */
    static array $soloGameMaps;


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
    static function borrowSoloGameMap(string $name, GameType $gameType, ?int $numberOfPlayers = null): Map {
        foreach (self::$soloGameMaps as $key => $map) {
            if ($map->getName() === $name) {
                if ($map->isAdaptedGameType($gameType)) {
                    if ($numberOfPlayers !== null) {
                        if ($map->getSpawnPoints() <= $numberOfPlayers) {
                            throw new \Exception("そのマップ({$name})はその人数({$numberOfPlayers})に対応していません");
                        }
                    }
                    unset(self::$soloGameMaps[$key]);
                    self::$soloGameMaps = array_values(self::$soloGameMaps);
                    return $map;
                } else {
                    throw new \Exception("そのマップ({$name})はそのゲームタイプ({$gameType})に対応していません");
                }
            }
        }

        throw new \Exception("そのマップ({$name})が存在しないか、すでに貸し出しています");
    }

    static function borrowTeamGameMap(GameType $gameType, int $numberOfTeams): Map { }

    static function return(Map $map): Map { }
}