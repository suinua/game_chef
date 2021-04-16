<?php


namespace game_chef\pmmp\form\team_game_map_forms;


use form_builder\models\custom_form_elements\Input;
use form_builder\models\CustomForm;
use game_chef\models\TeamDataOnMap;
use game_chef\models\TeamGameMap;
use game_chef\repository\TeamGameMapRepository;
use game_chef\services\TeamGameMapService;
use pocketmine\Player;

class CreateNewTeamForm extends CustomForm
{
    private TeamGameMap $map;
    private Input $nameElement;
    private Input $teamColorFormatElement;

    public function __construct(TeamGameMap $map) {
        $this->map = $map;
        $this->nameElement = new Input("", "マップ名", "");
        $this->teamColorFormatElement = new Input("", "チームカラーフォーマット(例:§e)", "");
        parent::__construct("新しいチームを追加", [
            $this->nameElement,
            $this->teamColorFormatElement
        ]);
    }

    function onSubmit(Player $player): void {
        $name = $this->nameElement->getResult();
        $colorFormat = $this->teamColorFormatElement->getResult();

        try {
            $team = new TeamDataOnMap($name, $colorFormat, null, null, [], [], []);
            TeamGameMapService::addTeamData($this->map->getName(), $team);
            $this->map = TeamGameMapRepository::loadByName($this->map->getName());
        } catch (\Exception $e) {
            $player->sendMessage($e->getMessage());
            return;
        }
        $player->sendForm(new TeamDataListForm($this->map));
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new TeamDataListForm($this->map));
    }
}