<?php


namespace game_chef\pmmp\form\ffa_game_map_forms;


use form_builder\models\custom_form_elements\Input;
use form_builder\models\custom_form_elements\Label;
use form_builder\models\CustomForm;
use game_chef\models\map_data\FFAGameMapData;
use game_chef\repository\FFAGameMapDataRepository;
use game_chef\utilities\GameTypeListFromString;
use pocketmine\Player;

class EditFFAGameMapGameTypeForm extends CustomForm
{
    private FFAGameMapData $ffaGameMapData;

    private Input $gameTypeListElement;

    public function __construct(FFAGameMapData $ffaGameMapData) {
        $this->ffaGameMapData = $ffaGameMapData;
        $typeListAsString = "";
        foreach ($ffaGameMapData->getAdaptedGameTypes() as $key => $type) {
            $typeListAsString .= strval($type) . ",";
        }
        $this->gameTypeListElement = new Input("", "", $typeListAsString);

        parent::__construct($ffaGameMapData->getName(), [
            new Label("ゲームタイプを編集"),
            $this->gameTypeListElement
        ]);
    }

    function onSubmit(Player $player): void {
        try {
            $gameTypeList = GameTypeListFromString::execute($this->gameTypeListElement->getResult());
            $this->ffaGameMapData->setAdaptedGameTypes($gameTypeList);

            FFAGameMapDataRepository::update($this->ffaGameMapData);
            $this->ffaGameMapData = FFAGameMapDataRepository::loadByName($this->ffaGameMapData->getName());
        } catch (\Exception $e) {
            $player->sendMessage($e->getMessage());
            return;
        }

        $player->sendForm(new FFAGameMapDetailForm($this->ffaGameMapData));
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new FFAGameMapDetailForm($this->ffaGameMapData));
    }
}