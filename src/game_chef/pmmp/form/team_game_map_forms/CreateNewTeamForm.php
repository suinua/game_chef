<?php


namespace game_chef\pmmp\form\team_game_map_forms;


use form_builder\models\custom_form_elements\Input;
use form_builder\models\CustomForm;
use game_chef\models\map_data\TeamDataOnMap;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\repository\TeamGameMapDataRepository;
use pocketmine\Player;

class CreateNewTeamForm extends CustomForm
{
    private TeamGameMapData $mapData;
    private Input $nameElement;
    private Input $teamColorFormatElement;

    public function __construct(TeamGameMapData $mapDataData) {
        $this->mapData = $mapDataData;
        $this->nameElement = new Input("チーム名を入力", "", "");
        $this->teamColorFormatElement = new Input("チームカラーフォーマット(例:§e)", "", "");

        parent::__construct("新しいチームを追加", [
            $this->nameElement,
            $this->teamColorFormatElement,
        ]);
    }

    function onSubmit(Player $player): void {
        $name = $this->nameElement->getResult();
        $colorFormat = $this->teamColorFormatElement->getResult();

        try {
            $teamData = new TeamDataOnMap($name, $colorFormat, [], null, null, [], []);
            $this->mapData->addTeamData($teamData);
            TeamGameMapDataRepository::update($this->mapData);
        } catch (\Exception $e) {
            $player->sendMessage($e->getMessage());
            return;
        }
        $player->sendForm(new TeamDataListForm($this->mapData));
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new TeamDataListForm($this->mapData));
    }
}