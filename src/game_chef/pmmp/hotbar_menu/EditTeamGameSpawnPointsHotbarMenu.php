<?php


namespace game_chef\pmmp\hotbar_menu;


use game_chef\store\TeamGameMapSpawnPointEditorStore;
use pocketmine\item\ItemIds;
use pocketmine\Player;

class EditTeamGameSpawnPointsHotbarMenu extends HotbarMenu
{
    public function __construct(Player $player) {
        parent::__construct($player, [
            new HotbarMenuItem(ItemIds::BOOK, "スポーン地点を追加", function (Player $player) {
                //TODO:実装
            }),
            new HotbarMenuItem(ItemIds::FEATHER, "戻る", function (Player $player) {
                try {
                    TeamGameMapSpawnPointEditorStore::delete($player->getName());
                } catch (\Exception $exception) {
                    $player->sendMessage($exception->getMessage());
                }
                $this->close();
            })
        ]);
    }
}