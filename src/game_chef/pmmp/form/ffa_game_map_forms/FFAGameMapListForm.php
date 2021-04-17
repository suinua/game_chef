<?php


namespace game_chef\pmmp\form\ffa_game_map_forms;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use game_chef\repository\FFAGameMapDataRepository;
use pocketmine\Player;

class FFAGameMapListForm extends SimpleForm
{
    public function __construct() {
        try {
            $mapDataList = FFAGameMapDataRepository::loadAll();
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            parent::__construct("エラーが発生しました", $errorMessage, []);
            return;
        }

        $mapListAsElements = [];
        foreach ($mapDataList as $mapData) {
            $mapListAsElements[] = new SimpleFormButton(
                $mapData->getName(),
                null,
                function (Player $player) use ($mapData) {
                    $player->sendForm(new FFAGameMapDetailForm($mapData));
                }
            );
        }

        parent::__construct("FFA用のマップ一覧", "", $mapListAsElements);
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new FFAGameMapForm());
    }
}

