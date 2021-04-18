<?php


namespace game_chef\pmmp\form;


use form_builder\models\custom_form_elements\Input;
use form_builder\models\CustomForm;
use game_chef\models\map_data\CustomMapArrayVectorData;
use game_chef\models\map_data\FFAGameMapData;
use game_chef\models\map_data\MapData;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\repository\FFAGameMapDataRepository;
use game_chef\repository\TeamGameMapDataRepository;
use pocketmine\Player;

class CreateCustomMapArrayVectorDataForm extends CustomForm
{
    private MapData $mapData;
    private Input $keyElement;

    public function __construct(MapData $mapData) {
        $this->mapData = $mapData;
        $this->keyElement = new Input("keyを入力", "", "");
        parent::__construct("配列型カスタム座標データを作成", [
            $this->keyElement
        ]);
    }

    function onSubmit(Player $player): void {
        $key = $this->keyElement->getResult();
        try {
            $this->mapData->addCustomMapArrayVectorData(new CustomMapArrayVectorData($key, []));
            if ($this->mapData instanceof TeamGameMapData) {
                TeamGameMapDataRepository::update($this->mapData);
                $this->mapData = TeamGameMapDataRepository::loadByName($this->mapData->getName());
            } else if ($this->mapData instanceof FFAGameMapData) {
                FFAGameMapDataRepository::update($this->mapData);
                $this->mapData = FFAGameMapDataRepository::loadByName($this->mapData->getName());
            }

        } catch (\Exception $e) {
            $player->sendMessage($e->getMessage());
        }

        $player->sendForm(new CustomMapArrayVectorDataListForm($this->mapData));
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new CustomMapArrayVectorDataListForm($this->mapData));
    }
}