<?php


namespace game_chef\pmmp\events;


use game_chef\models\GameId;
use game_chef\models\GameType;
use game_chef\models\TeamId;
use pocketmine\event\Event;
use pocketmine\Player;

class PlayerJoinedGameEvent extends Event
{
    private Player $player;
    private GameId $gameId;
    private GameType $gameType;
    private TeamId $teamId;


    public function __construct(Player $player, GameId $gameId, GameType $gameType, TeamId $teamId) {
        $this->player = $player;
        $this->gameId = $gameId;
        $this->gameType = $gameType;
        $this->teamId = $teamId;
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

    public function getTeamId(): TeamId {
        return $this->teamId;
    }
}