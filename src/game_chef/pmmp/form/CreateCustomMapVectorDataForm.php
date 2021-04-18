<?php


namespace game_chef\pmmp\form;


use form_builder\models\custom_form_elements\Input;
use form_builder\models\CustomForm;
use game_chef\models\map_data\MapData;
use game_chef\pmmp\hotbar_menu\CreateCustomMapVectorDataHotbarMenu;
use pocketmine\Player;

class CreateCustomMapVectorDataForm extends CustomForm
{
    private MapData $mapData;
    private Input $keyElement;

    public function __construct(MapData $mapData) {
        $this->mapData = $mapData;
        $this->keyElement = new Input("keyを入力", "", "");
        parent::__construct("カスタム座標データを作成", [
            $this->keyElement
        ]);
    }

    function onSubmit(Player $player): void {
        $key = $this->keyElement->getResult();
        $menu = new CreateCustomMapVectorDataHotbarMenu($player, $this->mapData, $key);
        $menu->send();
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new CustomMapVectorDataListForm($this->mapData));
    }
}