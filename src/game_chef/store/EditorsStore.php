<?php


namespace game_chef\store;


use game_chef\models\editors\CustomMapArrayVectorDataEditor;
use game_chef\models\editors\Editor;

class EditorsStore
{
    static private array $editors = [];

    static function add(string $playerName, Editor $editor): void {
        if (array_key_exists($playerName, self::$editors)) {
            throw new \LogicException($playerName . "はすでに他のエディターを持っています");
        }

        self::$editors[$playerName] = $editor;
    }

    static function delete(string $playerName): void {
        if (!array_key_exists($playerName, self::$editors)) {
            throw new \LogicException($playerName . "はエディターを持っていないので、削除することが出来ません");
        }

        self::$editors[$playerName]->stop();
        unset(self::$editors[$playerName]);
    }

    static function get(string $playerName): Editor {
        if (!array_key_exists($playerName, self::$editors)) {
            throw new \LogicException($playerName . "はエディターを持っていないので、取得できません");
        }

        return self::$editors[$playerName];
    }

    static function isExist(string $playerName): bool {
        return array_key_exists($playerName, self::$editors);
    }
}