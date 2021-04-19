<?php


namespace game_chef\pmmp\hotbar_menu;


use game_chef\models\map_data\FFAGameMapData;
use game_chef\pmmp\form\ffa_game_map_forms\FFAGameMapDetailForm;
use game_chef\repository\FFAGameMapDataRepository;
use game_chef\store\EditorsStore;
use pocketmine\block\Block;
use pocketmine\item\ItemIds;
use pocketmine\Player;

class FFAGameSpawnPointsHotbarMenu extends HotbarMenu
{
    private FFAGameMapData $mapData;

    public function __construct(Player $player, FFAGameMapData $mapData) {
        $this->mapData = $mapData;
        parent::__construct($player, [
            new HotbarMenuItem(ItemIds::BOOK, "スポーン地点を追加", function (Player $player, Block $block) {
                try {
                    $this->mapData->addSpawnPoint($block->asVector3());
                    FFAGameMapDataRepository::update($this->mapData);
                    $editor = EditorsStore::get($player->getName());
                    $editor->reloadMap();
                } catch (\Exception $exception) {
                    $player->sendMessage($exception->getMessage());
                }
            }),
            new HotbarMenuItem(ItemIds::FEATHER, "戻る", function (Player $player) {
                try {
                    EditorsStore::delete($player->getName());
                } catch (\Exception $exception) {
                    $player->sendMessage($exception->getMessage());
                }
                $player->sendForm(new FFAGameMapDetailForm($this->mapData));
                $this->close();
            })
        ]);
    }
}