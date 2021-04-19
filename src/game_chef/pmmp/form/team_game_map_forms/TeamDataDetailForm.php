<?php


namespace game_chef\pmmp\form\team_game_map_forms;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use game_chef\models\editors\TeamGameMapSpawnPointEditor;
use game_chef\models\map_data\TeamDataOnMap;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\pmmp\hotbar_menu\TeamGameSpawnPointsHotbarMenu;
use game_chef\store\EditorsStore;
use game_chef\TaskSchedulerStorage;
use pocketmine\Player;

class TeamDataDetailForm extends SimpleForm
{

    private TeamGameMapData $teamGameMapData;

    public function __construct(TeamGameMapData $teamGameMapDataData, TeamDataOnMap $teamDataOnMap) {

        $this->teamGameMapData = $teamGameMapDataData;
        parent::__construct(
            $teamDataOnMap->getColorFormat() . $teamDataOnMap->getName(),
            $teamGameMapDataData->getName(),
            [
                new SimpleFormButton(
                    "人数制限を変更",
                    null,
                    function (Player $player) use ($teamDataOnMap) {
                        $player->sendForm(new EditTeamPlayersForm($this->teamGameMapData, $teamDataOnMap));
                    }
                ),
                new SimpleFormButton(
                    "スポーン地点を変更",
                    null,
                    function (Player $player) use ($teamDataOnMap) {
                        $editor = new TeamGameMapSpawnPointEditor($this->teamGameMapData, $teamDataOnMap, $player, TaskSchedulerStorage::get());
                        try {
                            EditorsStore::add($player->getName(), $editor);
                            $editor->start();

                            $menu = new TeamGameSpawnPointsHotbarMenu($player, $this->teamGameMapData, $teamDataOnMap);
                            $menu->send();
                        } catch (\Exception $exception) {
                            $player->sendMessage($exception->getMessage());
                            return;
                        }
                    }
                )
            ]);
    }


    function onClickCloseButton(Player $player): void {
        $player->sendForm(new TeamDataListForm($this->teamGameMapData));
    }
}