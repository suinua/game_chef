<?php


namespace game_chef\pmmp\hotbar_menu;


use game_chef\models\FFAGameMap;
use game_chef\services\FFAGameMapService;
use game_chef\store\FFAGameMapSpawnPointEditorStore;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\Player;

class DeleteFFASpawnPointHotbarMenu extends HotbarMenu
{
    public function __construct(Player $player, FFAGameMap $map, Vector3 $spawnPoint) {
        parent::__construct($player,
            [
                new HotbarMenuItem(ItemIds::FEATHER, "戻る", function () {
                    $this->close();
                }),
                new HotbarMenuItem(ItemIds::TNT, "削除", function (Player $player) use ($map, $spawnPoint) {
                    try {
                        FFAGameMapService::deleteSpawnPoint($map, $spawnPoint);
                        $editor = FFAGameMapSpawnPointEditorStore::get($player->getName());
                        $editor->reloadMap();
                    } catch (\Exception $exception) {
                        $player->sendMessage($exception->getMessage());
                    }

                    $this->close();
                })
            ]
        );
    }

    public function close(): void {
        $menu = new EditFFAGameSpawnPointsHotbarMenu($this->player);
        $menu->send();
        parent::close();
    }
}