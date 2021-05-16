<?php


namespace game_chef\pmmp\hotbar_menu;


use game_chef\models\map_data\FFAGameMapData;
use game_chef\repository\FFAGameMapDataRepository;
use game_chef\store\EditorsStore;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\Player;

class DeleteFFASpawnPointHotbarMenu extends HotbarMenu
{
    private FFAGameMapData $mapData;

    public function __construct(Player $player, FFAGameMapData $mapData, Vector3 $spawnPoint) {
        $this->mapData = $mapData;

        parent::__construct($player,
            [
                new HotbarMenuItem(
                    ItemIds::FEATHER,
                    0,
                    "戻る",
                    function () {
                        $this->close();
                    }),
                new HotbarMenuItem(
                    ItemIds::TNT,
                    0,
                    "削除",
                    function (Player $player) use ($mapData, $spawnPoint) {
                        try {
                            $this->mapData->deleteSpawnPoint($spawnPoint);
                            FFAGameMapDataRepository::update($this->mapData);

                            $editor = EditorsStore::get($player->getName());
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
        parent::close();
        $menu = new FFAGameSpawnPointsHotbarMenu($this->player, $this->mapData);
        $menu->send();
    }
}