<?php


namespace game_chef\pmmp\form\team_game_map_forms;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use game_chef\repository\TeamGameMapDataRepository;
use pocketmine\Player;

class TeamGameMapListForm extends SimpleForm
{
    public function __construct() {
        try {
            $mapList = TeamGameMapDataRepository::loadAll();
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            parent::__construct("エラーが発生しました", $errorMessage, []);
            return;
        }

        $mapListAsElements = [];
        foreach ($mapList as $map) {
            $mapListAsElements[] = new SimpleFormButton(
                $map->getName(),
                null,
                function (Player $player) use ($map) {
                    $player->sendForm(new TeamGameMapDetailForm($map));
                }
            );
        }

        parent::__construct("チームゲーム用のマップ一覧", "", $mapListAsElements);
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new TeamGameMapForm());
    }
}

