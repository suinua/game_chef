<?php


namespace game_chef\pmmp\events;


use game_chef\models\GameId;
use game_chef\models\GameType;
use pocketmine\event\Event;
use pocketmine\Player;

//味方などの攻撃の通らないプレイヤーをタップしたときに呼ばれる
class PlayerTapPlayerEvent extends Event
{
    private GameId $gameId;
    private GameType $gameType;

    private Player $player;
    private Player $target;

    private bool $isTeammate;

    public function __construct(GameId $gameId, GameType $gameType, Player $attacker, Player $target, bool $isTeammate) {
        $this->gameId = $gameId;
        $this->gameType = $gameType;
        $this->player = $attacker;
        $this->target = $target;
        $this->isTeammate = $isTeammate;
    }

    public function getPlayer(): Player {
        return $this->player;
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

    public function isTeammate(): bool {
        return $this->isTeammate;
    }
}