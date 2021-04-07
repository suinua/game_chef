<?php


namespace game_chef\store;


use game_chef\models\GameType;
use game_chef\models\Map;
use game_chef\models\FFAGameMap;
use game_chef\models\TeamGameMap;
use game_chef\repository\FFAGameMapRepository;
use game_chef\repository\TeamGameMapRepository;

class MapsStore
{

    static array $loanOutFFAGameMapNames;
    static array $loanOutTeamGameMapName;

    /**
     * @param string $name
     * @param GameType $gameType
     * @param int|null $numberOfPlayers
     * @return FFAGameMap
     * @throws \Exception numberOfPlayersを設定すると、スポーン地点よりプレイヤーが多い場合エラーを吐きます
     * 同じところにスポーンしていい場合を除き、設定することを推奨します
     */
    static function borrowFFAGameMap(string $name, GameType $gameType, ?int $numberOfPlayers = null): FFAGameMap {
        if (in_array($name, self::$loanOutTeamGameMapName)) {
            throw new \Exception("そのマップ({$name})はすでに使用されています");
        }

        $map = FFAGameMapRepository::loadByName($name);
        if ($map->isAdaptedGameType($gameType)) {
            if ($numberOfPlayers !== null) {
                if ($map->getSpawnPoints() <= $numberOfPlayers) {
                    throw new \Exception("そのマップ({$name})はその人数({$numberOfPlayers})に対応していません");
                }
            }
            self::$loanOutFFAGameMapNames[] = $name;
            return $map;
        } else {
            throw new \Exception("そのマップ({$name})はそのゲームタイプ({$gameType})に対応していません");
        }
    }


    /**
     * @param string $name
     * @param GameType $gameType
     * @param int $numberOfTeams
     * @return TeamGameMap
     * @throws \Exception
     */
    static function borrowTeamGameMap(string $name, GameType $gameType, int $numberOfTeams): TeamGameMap {
        if (in_array($name, self::$loanOutTeamGameMapName)) {
            throw new \Exception("そのマップ({$name})はすでに使用されています");
        }

        $map = TeamGameMapRepository::loadByName($name);
        if ($map->isAdaptedGameType($gameType)) {
            if ($numberOfTeams !== null) {
                //登録してあるチームデータより、多いチームすうはムリ
                if ($map->getTeamDataList() <= $numberOfTeams) {
                    throw new \Exception("そのマップ({$name})はそのチーム数({$numberOfTeams})に対応していません");
                }
            }
            self::$loanOutTeamGameMapName[] = $name;
            return $map;
        } else {
            throw new \Exception("そのマップ({$name})はそのゲームタイプ({$gameType})に対応していません");
        }
    }
}