<?php


namespace game_chef\pmmp\form\team_game_map_forms;


use form_builder\models\custom_form_elements\Input;
use form_builder\models\custom_form_elements\Label;
use form_builder\models\CustomForm;
use game_chef\models\GameType;
use game_chef\models\TeamGameMap;
use game_chef\repository\TeamGameMapRepository;
use pocketmine\Player;

class EditTeamGameMapGameTypeForm extends CustomForm
{
    private TeamGameMap $teamGameMap;

    private Input $gameTypeListElement;

    public function __construct(TeamGameMap $teamGameMap) {
        parent::__construct($teamGameMap->getName(), [
            new Label("ゲームタイプを編集"),
            $this->gameTypeListElement
        ]);
    }

    function onSubmit(Player $player): void {
        $gameTypeList = [];
        foreach (explode(",", $this->gameTypeListElement->getResult()) as $value) {
            $gameTypeList = new GameType($value);
        }

        $newMap = new TeamGameMap(
            $this->teamGameMap->getName(),
            $this->teamGameMap->getLevelName(),
            $gameTypeList,
            $this->teamGameMap->getCustomMapVectorDataList(),
            $this->teamGameMap->getCustomMapVectorsDataList(),
            $this->teamGameMap->getTeamDataList(),
        );

        try {
            TeamGameMapRepository::update($newMap);
        } catch (\Exception $e) {
            $player->sendMessage($e->getMessage());
            return;
        }

        $player->sendForm(new TeamGameMapDetailForm($newMap));
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new TeamGameMapDetailForm($this->teamGameMap));
    }
}