<?php


namespace game_chef\pmmp\events;


use game_chef\models\GameId;
use game_chef\models\GameType;
use pocketmine\event\Event;
use pocketmine\Player;

class PlayerKilledPlayerEvent extends Event
{
    private GameId $gameId;
    private GameType $gameType;

    private Player $attacker;
    private Player $killedPlayer;

    public function __construct(GameId $gameId,GameType $gameType,Player $attacker, Player $killedPlayer) {
        $this->gameId = $gameId;
        $this->gameType = $gameType;
        $this->attacker = $attacker;
        $this->killedPlayer = $killedPlayer;
    }

    public function getAttacker(): Player {
        return $this->attacker;
    }

    public function getKilledPlayer(): Player {
        return $this->killedPlayer;
    }
}