<?php


namespace game_chef\store;


use game_chef\models\editors\CustomMapVectorDataEditor;

class CustomMapVectorDataEditorStore
{
    /**
     * @var CustomMapVectorDataEditor[]
     */
    static private array $editors = [];

    /**
     * @param string $playerName
     * @param CustomMapVectorDataEditor $editor
     * @throws \Exception
     */
    static function add(string $playerName, CustomMapVectorDataEditor $editor): void {
        if (array_key_exists($playerName, self::$editors)) {
            throw new \Exception($playerName . "はすでにカスタム座標データエディターを持っています");
        }

        self::$editors[$playerName] = $editor;
    }

    /**
     * @param string $playerName
     * @throws \Exception
     */
    static function delete(string $playerName): void {
        if (!array_key_exists($playerName, self::$editors)) {
            throw new \Exception($playerName . "はカスタム座標データエディターを持っていないので、削除することが出来ませんでした");
        }

        self::$editors[$playerName]->stop();
        unset(self::$editors[$playerName]);
    }

    /**
     * @param string $playerName
     * @return CustomMapVectorDataEditor
     * @throws \Exception
     */
    static function get(string $playerName): CustomMapVectorDataEditor {
        if (!array_key_exists($playerName, self::$editors)) {
            throw new \Exception($playerName . "はカスタム座標データエディターを持っていません");
        }

        return self::$editors[$playerName];
    }

    static function isExist(string $playerName): bool {
        return array_key_exists($playerName, self::$editors);
    }
}