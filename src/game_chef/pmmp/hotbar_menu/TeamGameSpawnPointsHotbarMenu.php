<?php


namespace game_chef\pmmp\hotbar_menu;


use game_chef\models\map_data\TeamDataOnMap;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\pmmp\form\team_game_map_forms\TeamDataDetailForm;
use game_chef\repository\TeamGameMapDataRepository;
use game_chef\store\EditorsStore;
use pocketmine\block\Block;
use pocketmine\item\ItemIds;
use pocketmine\Player;

class TeamGameSpawnPointsHotbarMenu extends HotbarMenu
{
    private TeamGameMapData $mapData;
    private TeamDataOnMap $teamDataOnMap;

    public function __construct(Player $player, TeamGameMapData $teamGameMapDataData, TeamDataOnMap $teamDataOnMap) {
        $this->mapData = $teamGameMapDataData;
        $this->teamDataOnMap = $teamDataOnMap;

        parent::__construct($player, [
            new HotbarMenuItem(
                ItemIds::BOOK,
                0,
                "スポーン地点を追加",
                null,
                function (Player $player, Block $block) {
                    try {
                        $this->teamDataOnMap->addSpawnPoint($block->asVector3());
                        TeamGameMapDataRepository::update($this->mapData);

                        $editor = EditorsStore::get($player->getName());
                        $editor->reloadMap();
                    } catch (\Exception $exception) {
                        $player->sendMessage($exception->getMessage());
                    }
                }),
            new HotbarMenuItem(
                ItemIds::FEATHER,
                0,
                "戻る",
                function (Player $player) {
                    try {
                        EditorsStore::delete($player->getName());
                    } catch (\Exception $exception) {
                        $player->sendMessage($exception->getMessage());
                    }
                    $player->sendForm(new TeamDataDetailForm($this->mapData, $this->teamDataOnMap));
                    $this->close();
                })
        ]);
    }

    public function close(): void {
        parent::close();
        $this->player->sendForm(new TeamDataDetailForm($this->mapData, $this->teamDataOnMap));
    }
}