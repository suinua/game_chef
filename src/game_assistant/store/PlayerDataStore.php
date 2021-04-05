<?php


namespace game_assistant\store;


use game_assistant\models\PlayerData;
use game_assistant\models\TeamId;


class PlayerDataStore
{
    /**
     * @var PlayerData[]
     * name => PlayerData
     */
    static private array $playerDataList = [];

    /**
     * @param PlayerData $playerData
     * @throws \Exception
     */
    static function add(PlayerData $playerData): void {
        if (array_key_exists($playerData->getName(), self::$playerDataList)) {
            throw new \Exception("すでにその名前({$playerData->getName()})のプレイヤーデータが存在します");
        }

        self::$playerDataList[$playerData->getName()] = $playerData;
    }

    /**
     * @param string $name
     * @throws \Exception
     */
    static function delete(string $name): void {
        if (!array_key_exists($name, self::$playerDataList)) {
            throw new \Exception("その名前({$name})のプレイヤーデータは存在しません");
        }

        unset(self::$playerDataList[$name]);
    }

    /**
     * @param string $name
     * @return PlayerData
     * @throws \Exception
     */
    static function getByName(string $name): PlayerData {
        if (!array_key_exists($name, self::$playerDataList)) {
            throw new \Exception("その名前({$name})のプレイヤーデータは存在しません");
        }

        return self::$playerDataList[$name];
    }

    /**
     * @param PlayerData $playerData
     * @throws \Exception
     */
    static function update(PlayerData $playerData): void {
        if (!array_key_exists($playerData->getName(), self::$playerDataList)) {
            throw new \Exception("その名前({$playerData->getName()})のプレイヤーデータは存在しません");
        }

        self::$playerDataList[$playerData->getName()] = $playerData;
    }

    /**
     * @param TeamId $teamId
     * @return PlayerData[]
     */
    static function getTeamPlayerData(TeamId $teamId): array {
        $result = [];
        foreach (self::$playerDataList as $playerData) {
            if ($playerData->getBelongTeamId() === null) continue;
            if ($playerData->getBelongTeamId()->equals($teamId)) $result[] = $playerData;
        }

        return $result;
    }
}