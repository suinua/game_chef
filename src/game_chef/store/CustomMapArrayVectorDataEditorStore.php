<?php


namespace game_chef\store;


use game_chef\models\editors\CustomMapArrayVectorDataEditor;

class CustomMapArrayVectorDataEditorStore
{
    /**
     * @var CustomMapArrayVectorDataEditor[]
     */
    static private array $editors = [];

    /**
     * @param string $playerName
     * @param CustomMapArrayVectorDataEditor $editor
     * @throws \Exception
     */
    static function add(string $playerName, CustomMapArrayVectorDataEditor $editor): void {
        if (array_key_exists($playerName, self::$editors)) {
            throw new \Exception($playerName . "はすでに配列型カスタム座標データエディターを持っています");
        }

        self::$editors[$playerName] = $editor;
    }

    /**
     * @param string $playerName
     * @throws \Exception
     */
    static function delete(string $playerName): void {
        if (!array_key_exists($playerName, self::$editors)) {
            throw new \Exception($playerName . "は配列型カスタム座標データエディターを持っていないので、削除することが出来ませんでした");
        }

        self::$editors[$playerName]->stop();
        unset(self::$editors[$playerName]);
    }

    /**
     * @param string $playerName
     * @return CustomMapArrayVectorDataEditor
     * @throws \Exception
     */
    static function get(string $playerName): CustomMapArrayVectorDataEditor {
        if (!array_key_exists($playerName, self::$editors)) {
            throw new \Exception($playerName . "は配列型カスタム座標データエディターを持っていません");
        }

        return self::$editors[$playerName];
    }

    static function isExist(string $playerName): bool {
        return array_key_exists($playerName, self::$editors);
    }
}