<?php


namespace game_chef\pmmp\hotbar_menu;


use game_chef\models\FFAGameMap;
use game_chef\services\FFAGameMapService;
use game_chef\store\FFAGameMapSpawnPointEditorStore;
use pocketmine\block\Block;
use pocketmine\item\ItemIds;
use pocketmine\Player;

class FFAGameSpawnPointsHotbarMenu extends HotbarMenu
{
    public function __construct(Player $player, FFAGameMap $map) {
        parent::__construct($player, [
            new HotbarMenuItem(ItemIds::BOOK, "スポーン地点を追加", function (Player $player, Block $block) use ($map) {
                try {
                    FFAGameMapService::addSpawnPoint($map, $block->asVector3());
                    $editor = FFAGameMapSpawnPointEditorStore::get($player->getName());
                    $editor->reloadMap();
                } catch (\Exception $exception) {
                    $player->sendMessage($exception->getMessage());
                }
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