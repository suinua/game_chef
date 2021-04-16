<?php


namespace game_chef\pmmp\form\ffa_game_map_forms;


use form_builder\models\custom_form_elements\Input;
use form_builder\models\custom_form_elements\Label;
use form_builder\models\CustomForm;
use game_chef\models\FFAGameMap;
use game_chef\models\GameType;
use game_chef\services\FFAGameMapService;
use pocketmine\Player;

class EditFFAGameMapGameTypeForm extends CustomForm
{
    private FFAGameMap $ffaGameMap;

    private Input $gameTypeListElement;

    public function __construct(FFAGameMap $ffaGameMap) {
        $this->ffaGameMap = $ffaGameMap;
        $typeListAsString = "";
        foreach ($ffaGameMap->getAdaptedGameTypes() as $key => $type) {
            $typeListAsString .= strval($type) . ",";
        }
        $this->gameTypeListElement = new Input("", "", $typeListAsString);

        parent::__construct($ffaGameMap->getName(), [
            new Label("ゲームタイプを編集"),
            $this->gameTypeListElement
        ]);
    }

    function onSubmit(Player $player): void {
        $gameTypeList = [];
        foreach (explode(",", $this->gameTypeListElement->getResult()) as $value) {
            if ($value === "") continue;
            $gameTypeList[] = new GameType($value);
        }

        $newMap = new FFAGameMap(
            $this->ffaGameMap->getName(),
            $this->ffaGameMap->getLevelName(),
            $gameTypeList,
            $this->ffaGameMap->getCustomMapVectorDataList(),
            $this->ffaGameMap->getCustomMapArrayVectorDataList(),
            $this->ffaGameMap->getSpawnPoints()
        );

        try {
            FFAGameMapService::update($newMap);
        } catch (\Exception $e) {
            $player->sendMessage($e->getMessage());
            return;
        }

        $player->sendForm(new FFAGameMapDetailForm($newMap));
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new FFAGameMapDetailForm($this->ffaGameMap));
    }
}