<?php


namespace game_chef\pmmp\events;


use game_chef\models\GameId;
use game_chef\models\GameType;
use pocketmine\event\Event;

class FinishedGameEvent extends Event
{
    private GameId $gameId;
    private GameType $gameType;

    public function __construct(GameId $gameId, GameType $gameType) {
        $this->gameId = $gameId;
        $this->gameType = $gameType;
    }

    public function getGameId(): GameId {
        return $this->gameId;
    }

    public function getGameType(): GameType {
        return $this->gameType;
    }
}