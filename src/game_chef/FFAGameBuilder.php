<?php


namespace game_chef;


use game_chef\models\FFAGame;
use game_chef\models\FFAGameMap;
use game_chef\store\MapsStore;

class FFAGameBuilder extends GameBuilder
{
    private FFAGameMap $map;

    private bool $alreadySetMaxPlayers = false;
    private ?int $maxPlayers;


    /**
     * @param int|null $maxPlayers
     * @throws \Exception
     */
    public function setMaxPlayers(?int $maxPlayers): void {
        if ($this->alreadySetMaxPlayers) {
            throw new \Exception("再度セットすることは出来ません");
        }
        $this->maxPlayers = $maxPlayers;
    }

    /**
     * @param string $mapName
     * @throws \Exception
     */
    public function selectMapByName(string $mapName): void {
        if ($this->gameType === null or !$this->alreadySetMaxPlayers) {
            throw new \Exception("GameTypeまたはチーム数より先にセットすることは出来ません");
        }

        $this->map = MapsStore::borrowFFAGameMap($mapName, $this->gameType, $this->maxPlayers);
    }

    /**
     * @return FFAGame
     * @throws \Exception
     */
    public function build(): FFAGame {
        //TODO:TeamGameBuilderと共通
        if ($this->map === null) throw new \Exception("mapをセットしていない状態でゲームを作ることはできません");
        if ($this->gameType === null) throw new \Exception("gameTypeをセットしていない状態でゲームを作ることはできません");

        if ($this->maxPlayers === null) throw new \Exception("maxPlayersをセットしていない状態でゲームを作ることはできません");

        return new FFAGame(
            $this->map,
            $this->gameType,
            $this->victoryScore,
            $this->canJumpIn,
            $this->timeLimit,
            [],
            $this->maxPlayers
        );
    }

}