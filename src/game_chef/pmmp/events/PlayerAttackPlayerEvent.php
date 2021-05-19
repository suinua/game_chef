<?php


namespace game_chef\pmmp\events;


use game_chef\models\GameId;
use game_chef\models\GameType;
use pocketmine\event\Event;
use pocketmine\Player;

class PlayerAttackPlayerEvent extends Event
{
    private GameId $gameId;
    private GameType $gameType;

    private Player $attacker;
    private Player $target;

    private bool $isFriendlyFire;

    private int $cause;
    private float $baseDamage;
    private float $knockBack;


    public function __construct(GameId $gameId, GameType $gameType, Player $attacker, Player $target, bool $isFriendlyFire, int $cause, float $baseDamage, float $knockBack) {
        $this->gameId = $gameId;
        $this->gameType = $gameType;
        $this->attacker = $attacker;
        $this->target = $target;
        $this->isFriendlyFire = $isFriendlyFire;
        $this->cause = $cause;
        $this->baseDamage = $baseDamage;
        $this->knockBack = $knockBack;
    }

    public function getAttacker(): Player {
        return $this->attacker;
    }

    public function getTarget(): Player {
        return $this->target;
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

    public function getCause(): int {
        return $this->cause;
    }

    public function getBaseDamage(): float {
        return $this->baseDamage;
    }

    public function getKnockBack(): float {
        return $this->knockBack;
    }

    public function setBaseDamage(float $baseDamage): void {
        $this->baseDamage = $baseDamage;
    }

    public function setKnockBack(float $knockBack): void {
        $this->knockBack = $knockBack;
    }
}