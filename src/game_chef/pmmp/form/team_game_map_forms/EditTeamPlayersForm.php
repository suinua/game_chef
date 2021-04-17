<?php


namespace game_chef\pmmp\form\team_game_map_forms;


use form_builder\models\custom_form_elements\Slider;
use form_builder\models\custom_form_elements\Toggle;
use form_builder\models\CustomForm;
use game_chef\models\map_data\TeamDataOnMap;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\repository\TeamGameMapDataRepository;
use pocketmine\Player;

class EditTeamPlayersForm extends CustomForm
{
    private TeamGameMapData $map;
    private TeamDataOnMap $teamData;

    private Slider $maxElement;
    private Slider $minElement;
    private Toggle $maxToggle;
    private Toggle $minToggle;

    public function __construct(TeamGameMapData $teamGameMapData, TeamDataOnMap $teamDataOnMap) {
        $this->map = $teamGameMapData;
        $this->teamData = $teamDataOnMap;

        $max = $teamDataOnMap->getMaxPlayers();
        $min = $teamDataOnMap->getMinPlayers();

        $this->maxElement = new Slider("最大人数", 0, 100, $max ?? 100);
        $this->minElement = new Slider("最小人数", 0, 100, $min ?? 100);
        $this->maxToggle = new Toggle("最大人数を設定しない", $max === null);
        $this->minToggle = new Toggle("最小人数を設定しない", $min === null);
        parent::__construct("チーム({$teamDataOnMap->getName()})の最大、最小人数を設定", [
            $this->maxToggle,
            $this->maxElement,
            $this->minToggle,
            $this->minElement,
        ]);
    }

    function onSubmit(Player $player): void {
        if ($this->maxToggle->getResult()) {
            $max = null;
        } else {
            $max = $this->maxElement->getResult();
        }

        if ($this->minToggle->getResult()) {
            $min = null;
        } else {
            $min = $this->minElement->getResult();
        }

        $this->teamData->setMaxPlayers($max);
        $this->teamData->setMinPlayers($min);

        try {
            $this->map->updateTeamData($this->teamData);
            TeamGameMapDataRepository::update($this->map);
            $this->map = TeamGameMapDataRepository::loadByName($this->map->getName());
        } catch (\Exception $e) {
            $player->sendMessage($e->getMessage());
            return;
        }

        $player->sendForm(new TeamDataDetailForm($this->map, $this->teamData));
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new TeamDataDetailForm($this->map, $this->teamData));
    }
}