<?php


namespace game_chef\pmmp\hotbar_menu;


use game_chef\models\map_data\CustomTeamVectorData;
use game_chef\models\map_data\TeamDataOnMap;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\pmmp\form\CustomMapVectorDataListForm;
use game_chef\repository\TeamGameMapDataRepository;
use pocketmine\block\Block;
use pocketmine\item\ItemIds;
use pocketmine\Player;

class CreateCustomTeamVectorDataHotbarMenu extends HotbarMenu
{
    public function __construct(Player $player, TeamGameMapData $teamGameMapData, TeamDataOnMap $teamDataOnMap, string $key) {
        parent::__construct($player, [
            new HotbarMenuItem(
                ItemIds::BOOK,
                0,
                $key,
                null,
                function (Player $player, Block $block) use ($teamGameMapData, $teamDataOnMap, $key) {
                    try {
                        $teamDataOnMap->addCustomVectorData(new CustomTeamVectorData($key, $teamDataOnMap->getName(), $block->asVector3()));
                        $teamGameMapData->updateTeamData($teamDataOnMap);

                        TeamGameMapDataRepository::update($teamGameMapData);
                    } catch (\Exception $exception) {
                        $player->sendMessage($exception->getMessage());
                    }

                    $player->sendForm(new CustomMapVectorDataListForm($teamGameMapData));
                }
            )
            //todo:戻る
        ]);
    }
}