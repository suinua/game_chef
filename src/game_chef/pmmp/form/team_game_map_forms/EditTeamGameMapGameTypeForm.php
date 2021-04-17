<?php


namespace game_chef\pmmp\form\team_game_map_forms;


use form_builder\models\custom_form_elements\Input;
use form_builder\models\custom_form_elements\Label;
use form_builder\models\CustomForm;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\repository\TeamGameMapDataRepository;
use game_chef\utilities\GameTypeListFromString;
use pocketmine\Player;

class EditTeamGameMapGameTypeForm extends CustomForm
{
    private TeamGameMapData $teamGameMapData;

    private Input $gameTypeListElement;

    public function __construct(TeamGameMapData $teamGameMapDataData) {
        $this->teamGameMapData = $teamGameMapDataData;
        $typeListAsString = "";
        foreach ($teamGameMapDataData->getAdaptedGameTypes() as $key => $type) {
            $typeListAsString .= strval($type) . ",";
        }
        $this->gameTypeListElement = new Input("", "", $typeListAsString);

        parent::__construct($teamGameMapDataData->getName(), [
            new Label("ゲームタイプを編集"),
            $this->gameTypeListElement
        ]);
    }

    function onSubmit(Player $player): void {
        try {
            $gameTypeList = GameTypeListFromString::execute($this->gameTypeListElement->getResult());
            $this->teamGameMapData->setAdaptedGameTypes($gameTypeList);
            TeamGameMapDataRepository::update($this->teamGameMapData);

            $this->teamGameMapData = TeamGameMapDataRepository::loadByName($this->teamGameMapData->getName());
        } catch (\Exception $e) {
            $player->sendMessage($e->getMessage());
            return;
        }

        $player->sendForm(new TeamGameMapDetailForm($this->teamGameMapData));
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new TeamGameMapDetailForm($this->teamGameMapData));
    }
}