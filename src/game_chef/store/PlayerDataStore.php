<?php


namespace game_chef\store;


use game_chef\models\GameId;
use game_chef\models\PlayerData;
use game_chef\models\TeamId;


class PlayerDataStore
{
    /**
     * @var PlayerData[]
     * name => PlayerData
     */
    static private array $playerDataList = [];

    static function add(PlayerData $playerData): void {
        if (array_key_exists($playerData->getName(), self::$playerDataList)) {
            throw new \LogicException("すでにその名前({$playerData->getName()})のプレイヤーデータが存在します");
        }

        self::$playerDataList[$playerData->getName()] = $playerData;
    }

    static function delete(string $name): void {
        if (!array_key_exists($name, self::$playerDataList)) {
            throw new \LogicException("存在しないプレイヤーデータ({$name})を削除することはできません");
        }

        unset(self::$playerDataList[$name]);
    }

    static function getByName(string $name): PlayerData {
        if (!array_key_exists($name, self::$playerDataList)) {
            throw new \LogicException("その名前({$name})のプレイヤーデータは存在しません");
        }

        return self::$playerDataList[$name];
    }

    static function findByName(string $name): ?PlayerData {
        if (!array_key_exists($name, self::$playerDataList)) {
            return null;
        }

        return self::$playerDataList[$name];
    }

    static function update(PlayerData $playerData): void {
        if (!array_key_exists($playerData->getName(), self::$playerDataList)) {
            throw new \LogicException("存在しないプレイヤーデータ({$playerData->getName()})を更新することはできません");
        }

        self::$playerDataList[$playerData->getName()] = $playerData;
    }

    /**
     * @param GameId $gameId
     * @return PlayerData[]
     */
    static function getByGameId(GameId $gameId): array {
        $result = [];
        foreach (self::$playerDataList as $playerData) {
            if ($playerData->getBelongGameId() === null) continue;
            if ($playerData->getBelongGameId()->equals($gameId)) $result[] = $playerData;
        }

        return $result;
    }

    /**
     * @param TeamId $teamId
     * @return PlayerData[]
     */
    static function getByTeamId(TeamId $teamId): array {
        $result = [];
        foreach (self::$playerDataList as $playerData) {
            if ($playerData->getBelongTeamId() === null) continue;
            if ($playerData->getBelongTeamId()->equals($teamId)) $result[] = $playerData;
        }

        return $result;
    }
}