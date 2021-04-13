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

    private bool $isFriendlyFire;

    public function __construct(GameId $gameId, GameType $gameType, Player $attacker, Player $killedPlayer, bool $isFriendlyFire) {
        $this->gameId = $gameId;
        $this->gameType = $gameType;
        $this->attacker = $attacker;
        $this->killedPlayer = $killedPlayer;
        $this->isFriendlyFire = $isFriendlyFire;
    }

    public function getAttacker(): Player {
        return $this->attacker;
    }

    public function getKilledPlayer(): Player {
        return $this->killedPlayer;
    }

    public function getGameId(): GameId {
        return $this->gameId;
    }

    public function getGameType(): GameType {
        return $this->gameType;
    }

    public function isFriendlyFire(): bool {
        return $this->isFriendlyFire;
    }
}