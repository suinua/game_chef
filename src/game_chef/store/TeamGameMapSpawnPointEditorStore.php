<?php


namespace game_chef\store;


use game_chef\models\TeamGameMapSpawnPointEditor;

class TeamGameMapSpawnPointEditorStore
{
    /**
     * @var TeamGameMapSpawnPointEditor[]
     */
    static private array $editors = [];

    /**
     * @param string $playerName
     * @param TeamGameMapSpawnPointEditor $editor
     * @throws \Exception
     */
    static function add(string $playerName, TeamGameMapSpawnPointEditor $editor): void {
        if (array_key_exists($playerName, self::$editors)) {
            throw new \Exception($playerName . "はすでにスポーン地点エディターを持っています");
        }

        self::$editors[$playerName] = $editor;
    }

    /**
     * @param string $playerName
     * @throws \Exception
     */
    static function delete(string $playerName): void {
        if (!array_key_exists($playerName, self::$editors)) {
            throw new \Exception($playerName . "はスポーン地点エディターを持っていないので、削除することが出来ませんでした");
        }

        self::$editors[$playerName]->stop();
        unset(self::$editors[$playerName]);
    }

    /**
     * @param string $playerName
     * @return TeamGameMapSpawnPointEditor
     * @throws \Exception
     */
    static function get(string $playerName): TeamGameMapSpawnPointEditor {
        if (!array_key_exists($playerName, self::$editors)) {
            throw new \Exception($playerName . "はスポーン地点エディターを持っていません");
        }

        return self::$editors[$playerName];
    }

    static function isExist(string $playerName): bool {
        return array_key_exists($playerName, self::$editors);
    }
}