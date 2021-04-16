<?php


namespace game_chef\store;


use game_chef\models\FFAGameMapSpawnPointEditor;

class FFAGameMapSpawnPointEditorStore
{
    /**
     * @var FFAGameMapSpawnPointEditor[]
     */
    static private array $editors = [];

    /**
     * @param string $playerName
     * @param FFAGameMapSpawnPointEditor $editor
     * @throws \Exception
     */
    static function add(string $playerName, FFAGameMapSpawnPointEditor $editor): void {
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
     * @return FFAGameMapSpawnPointEditor
     * @throws \Exception
     */
    static function get(string $playerName): FFAGameMapSpawnPointEditor {
        if (!array_key_exists($playerName, self::$editors)) {
            throw new \Exception($playerName . "はスポーン地点エディターを持っていません");
        }

        return self::$editors[$playerName];
    }

    static function isExist(string $playerName): bool {
        return array_key_exists($playerName, self::$editors);
    }
}