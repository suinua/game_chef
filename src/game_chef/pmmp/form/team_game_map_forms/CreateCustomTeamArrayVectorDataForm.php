<?php


namespace game_chef\pmmp\form\team_game_map_forms;


use form_builder\models\custom_form_elements\Input;
use form_builder\models\CustomForm;
use game_chef\models\map_data\CustomTeamArrayVectorData;
use game_chef\models\map_data\TeamDataOnMap;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\pmmp\form\CustomMapArrayVectorDataListForm;
use game_chef\repository\TeamGameMapDataRepository;
use pocketmine\Player;

class CreateCustomTeamArrayVectorDataForm extends CustomForm
{
    private TeamGameMapData $mapData;
    private TeamDataOnMap $teamData;
    private Input $keyElement;

    public function __construct(TeamGameMapData $mapData, TeamDataOnMap $teamData) {
        $this->mapData = $mapData;
        $this->teamData = $teamData;
        $this->keyElement = new Input("keyを入力", "", "");
        parent::__construct("配列型チームカスタム座標データを作成", [
            $this->keyElement
        ]);
    }

    function onSubmit(Player $player): void {
        $key = $this->keyElement->getResult();
        try {
            $this->teamData->addCustomArrayVectorData(new CustomTeamArrayVectorData($key, $this->teamData->getName(), []));
            TeamGameMapDataRepository::update($this->mapData);
            $this->mapData = TeamGameMapDataRepository::loadByName($this->mapData->getName());
        } catch (\Exception $e) {
            $player->sendMessage($e->getMessage());
        }

        $player->sendForm(new CustomMapArrayVectorDataListForm($this->mapData));
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new CustomMapArrayVectorDataListForm($this->mapData));
    }
}