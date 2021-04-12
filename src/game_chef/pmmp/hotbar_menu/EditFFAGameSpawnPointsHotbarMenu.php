<?php


namespace game_chef\pmmp\hotbar_menu;


use game_chef\store\FFAGameMapSpawnPointEditorStore;
use pocketmine\item\ItemIds;
use pocketmine\Player;

class EditFFAGameSpawnPointsHotbarMenu extends HotbarMenu
{
    public function __construct(Player $player) {
        parent::__construct($player, [
            new HotbarMenuItem(ItemIds::BOOK, "スポーン地点を追加", function (Player $player) {
                //TODO:実装
            }),
            new HotbarMenuItem(ItemIds::FEATHER, "戻る", function (Player $player) {
                try {
                    FFAGameMapSpawnPointEditorStore::delete($player->getName());
                } catch (\Exception $exception) {
                    $player->sendMessage($exception->getMessage());
                }
                $this->close();
            })
        ]);
    }
}