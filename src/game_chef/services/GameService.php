<?php


namespace game_chef\services;


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
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

class GameService
{
    /**
     * @param Game $game
     * @throws \Exception
     */
    static function register(Game $game) {
        GamesStore::add($game);
        $timer = new GameTimer($game->getId(), $game->getType(), $game->getTimeLimit());
        GameTimersStore::add($timer);
    }

    /**
     * @param GameId $gameId
     * @param TaskScheduler $scheduler
     * @throws \Exception
     */
    static function start(GameId $gameId, TaskScheduler $scheduler): void {
        $game = GamesStore::getById($gameId);
        $timer = GameTimersStore::getById($gameId);

        $game->start();
        $timer->start($scheduler);
        (new StartedGameEvent($gameId, $game->getType()))->call();
    }

    /**
     * @param GameId $gameId
     * @throws \Exception
     */
    static function finish(GameId $gameId) {
        $timer = GameTimersStore::getById($gameId);
        $game = GamesStore::getById($gameId);

        $timer->stop();
        $game->finished();

        (new FinishedGameEvent($gameId, $game->getType()))->call();

        //最後に実行
        GameTimersStore::delete($gameId);
        GamesStore::delete($gameId);
        foreach (PlayerDataStore::getByGameId($gameId) as $playerData) {
            PlayerDataStore::update(new PlayerData($playerData->getName()));
        }
    }

    /**
     * @param Player $player
     * @throws \Exception
     */
    static function quit(Player $player) {
        if (!$player->isOnline()) {
            throw new \Exception("オンラインでないプライヤーは操作できません");
        }

        $playerData = PlayerDataStore::getByName($player->getName());
        if ($playerData->getBelongTeamId() === null) {
            throw new \Exception("そのプレイヤー({$player->getName()})は試合に参加していません");
        }

        $game = GamesStore::getById($playerData->getBelongGameId());
        PlayerDataStore::update(new PlayerData($player->getName()));
        (new PlayerQuitGameEvent($player, $game->getId(), $game->getType(), $playerData->getBelongTeamId()))->call();
    }
}