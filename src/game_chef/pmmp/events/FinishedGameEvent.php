<?php


namespace game_chef\pmmp\events;


use game_chef\models\GameId;
use pocketmine\event\Event;

class FinishedGameEvent extends Event
{
    private GameId $gameId;

    public function __construct(GameId $gameId) {
        $this->gameId = $gameId;
    }

    public function getGameId(): GameId {
        return $this->gameId;
    }
}