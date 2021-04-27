<?php


namespace game_chef\pmmp\events;


use game_chef\models\GameId;
use game_chef\models\GameType;
use game_chef\models\TeamId;
use pocketmine\event\Cancellable;
use pocketmine\event\Event;
use pocketmine\Player;

class PlayerMoveTeamEvent extends Event implements Cancellable
{
    private Player $player;
    private GameId $gameId;
    private GameType $gameType;
    private TeamId $newTeamId;
    private TeamId $oldTeamId;

    public function __construct(Player $player, GameId $gameId, GameType $gameType, TeamId $newTeamId, TeamId $oldTeamId) {
        $this->player = $player;
        $this->gameId = $gameId;
        $this->gameType = $gameType;
        $this->newTeamId = $newTeamId;
        $this->oldTeamId = $oldTeamId;
    }

    public function getPlayer(): Player {
        return $this->player;
    }

    public function getGameId(): GameId {
        return $this->gameId;
    }

    public function getGameType(): GameType {
        return $this->gameType;
    }

    public function getNewTeamId(): TeamId {
        return $this->newTeamId;
    }

    public function getOldTeamId(): TeamId {
        return $this->oldTeamId;
    }
}