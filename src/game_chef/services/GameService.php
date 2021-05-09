<?php


namespace game_chef\services;


use game_chef\models\FFAGame;
use game_chef\models\Game;
use game_chef\models\GameId;
use game_chef\models\GameTimer;
use game_chef\models\PlayerData;
use game_chef\pmmp\events\FinishedGameEvent;
use game_chef\pmmp\events\PlayerQuitGameEvent;
use game_chef\pmmp\events\StartedGameEvent;
use game_chef\store\GamesStore;
use game_chef\store\GameTimersStore;
use game_chef\store\PlayerDataStore;
use game_chef\TaskSchedulerStorage;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;

class GameService
{
    static function register(Game $game) {
        GamesStore::add($game);
        $timer = new GameTimer($game->getId(), $game->getType(), $game->getTimeLimit());
        GameTimersStore::add($timer);
    }

    static function start(GameId $gameId, TaskScheduler $scheduler): void {
        $game = GamesStore::getById($gameId);
        $timer = GameTimersStore::getById($gameId);

        $game->start();
        $timer->start($scheduler);
        (new StartedGameEvent($gameId, $game->getType()))->call();
    }

    static function finish(GameId $gameId) {
        $timer = GameTimersStore::getById($gameId);
        $game = GamesStore::getById($gameId);

        $timer->stop();
        $game->finished();

        (new FinishedGameEvent($gameId, $game->getType()))->call();
    }

    static function discard(GameId $gameId): void {
        $game = GamesStore::getById($gameId);

        GameTimersStore::delete($gameId);
        GamesStore::delete($gameId);
        foreach (PlayerDataStore::getByGameId($gameId) as $playerData) {
            PlayerDataStore::update(new PlayerData($playerData->getName()));
        }

        MapService::deleteInstantWorld($game->getMap()->getLevelName());
    }

    static function quit(Player $player): void {
        $playerData = PlayerDataStore::getByName($player->getName());
        if ($playerData->getBelongTeamId() === null) {
            throw new \LogicException("試合に参加していないプレイヤー({$player->getName()})を抜けさせることはできません");
        }

        $game = GamesStore::getById($playerData->getBelongGameId());

        if ($game instanceof FFAGame) {
            //todo　ここにあるべきじゃない
            $game->deleteTeam($player->getName());
        }

        PlayerDataStore::update(new PlayerData($player->getName()));
        (new PlayerQuitGameEvent($player, $game->getId(), $game->getType(), $playerData->getBelongTeamId()))->call();
    }
}