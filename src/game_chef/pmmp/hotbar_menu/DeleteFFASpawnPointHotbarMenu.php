<?php


namespace game_chef\pmmp\hotbar_menu;


use game_chef\models\FFAGameMap;
use game_chef\repository\FFAGameMapRepository;
use game_chef\services\FFAGameMapService;
use game_chef\store\FFAGameMapSpawnPointEditorStore;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\Player;

class DeleteFFASpawnPointHotbarMenu extends HotbarMenu
{
    private FFAGameMap $map;

    public function __construct(Player $player, FFAGameMap $map, Vector3 $spawnPoint) {
        $this->map = $map;

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
        try {
            $map = FFAGameMapRepository::loadByName($this->map->getName());
        } catch (\Exception $e) {
            $this->player->sendMessage($e->getMessage());
            parent::close();
            return;
        }

        $menu = new FFAGameSpawnPointsHotbarMenu($this->player, $map);
        $menu->send();
    }
}