<?php


namespace game_chef\pmmp\hotbar_menu;


use game_chef\models\map_data\CustomMapVectorData;
use game_chef\models\map_data\FFAGameMapData;
use game_chef\models\map_data\MapData;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\pmmp\form\CustomMapVectorDataListForm;
use game_chef\repository\FFAGameMapDataRepository;
use game_chef\repository\TeamGameMapDataRepository;
use pocketmine\block\Block;
use pocketmine\item\ItemIds;
use pocketmine\Player;

class CreateCustomMapVectorDataHotbarMenu extends HotbarMenu
{
    public function __construct(Player $player, MapData $mapData, string $key) {
        parent::__construct($player, [
            new HotbarMenuItem(
                ItemIds::BOOK,
                0,
                $key,
                null,
                function (Player $player, Block $block) use ($mapData, $key) {
                    try {
                        $mapData->addCustomMapVectorData(new CustomMapVectorData($key, $block->asVector3()));
                        if ($mapData instanceof TeamGameMapData) {
                            TeamGameMapDataRepository::update($mapData);
                        } else if ($mapData instanceof FFAGameMapData) {
                            FFAGameMapDataRepository::update($mapData);
                        }
                    } catch (\Exception $exception) {
                        $player->sendMessage($exception->getMessage());
                    }

                    $player->sendForm(new CustomMapVectorDataListForm($mapData));
                }
            )
        ]);
    }

}