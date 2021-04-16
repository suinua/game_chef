<?php


namespace game_chef\pmmp\form\team_game_map_forms;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use game_chef\models\TeamDataOnMap;
use game_chef\models\TeamGameMap;
use game_chef\models\TeamGameMapSpawnPointEditor;
use game_chef\store\TeamGameMapSpawnPointEditorStore;
use game_chef\TaskSchedulerStorage;
use pocketmine\Player;

class TeamDataDetailForm extends SimpleForm
{

    private TeamGameMap $teamGameMap;

    public function __construct(TeamGameMap $teamGameMap, TeamDataOnMap $teamDataOnMap) {

        $this->teamGameMap = $teamGameMap;
        parent::__construct(
            $teamDataOnMap->getTeamColorFormat() . $teamDataOnMap->getTeamName(),
            $teamGameMap->getName(),
            [
                new SimpleFormButton(
                    "人数制限を変更",
                    null,
                    function (Player $player) use ($teamDataOnMap) {
                        $player->sendForm(new EditTeamPlayersForm($this->teamGameMap, $teamDataOnMap));
                    }
                ),
                new SimpleFormButton(
                    "スポーン地点を変更",
                    null,
                    function (Player $player) use ($teamDataOnMap) {
                        $editor = new TeamGameMapSpawnPointEditor($this->teamGameMap, $teamDataOnMap, $player, TaskSchedulerStorage::get());
                        try {
                            TeamGameMapSpawnPointEditorStore::add($player->getName(), $editor);
                            $editor->start();
                        } catch (\Exception $exception) {
                            $player->sendMessage($exception->getMessage());
                            return;
                        }
                    }
                )
            ]);
    }


    function onClickCloseButton(Player $player): void {
        $player->sendForm(new TeamDataListForm($this->teamGameMap));
    }
}