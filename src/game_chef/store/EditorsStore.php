<?php


namespace game_chef\store;


use game_chef\models\editors\CustomMapArrayVectorDataEditor;
use game_chef\models\editors\Editor;

class EditorsStore
{
    static private array $editors = [];

    /**
     * @param string $playerName
     * @param Editor $editor
     * @throws \Exception
     */
    static function add(string $playerName, Editor $editor): void {
        if (array_key_exists($playerName, self::$editors)) {
            throw new \Exception($playerName . "はすでに他のエディターを持っています");
        }

        self::$editors[$playerName] = $editor;
    }

    /**
     * @param string $playerName
     * @throws \Exception
     */
    static function delete(string $playerName): void {
        if (!array_key_exists($playerName, self::$editors)) {
            throw new \Exception($playerName . "はエディターを持っていないので、削除することが出来ませんでした");
        }

        self::$editors[$playerName]->stop();
        unset(self::$editors[$playerName]);
    }

    /**
     * @param string $playerName
     * @return CustomMapArrayVectorDataEditor
     * @throws \Exception
     */
    static function get(string $playerName): Editor {
        if (!array_key_exists($playerName, self::$editors)) {
            throw new \Exception($playerName . "はエディターを持っていません");
        }

        return self::$editors[$playerName];
    }

    static function isExist(string $playerName): bool {
        return array_key_exists($playerName, self::$editors);
    }
}