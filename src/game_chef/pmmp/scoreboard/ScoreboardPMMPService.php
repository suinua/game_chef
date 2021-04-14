<?php


namespace game_chef\pmmp\scoreboard;


use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\Player;

class ScoreboardPMMPService
{
    static function addScore(Player $player, ScoreboardSlot $slot, Score $score): void {
        $entry = new ScorePacketEntry();
        $entry->objectiveName = $slot->getText();
        $entry->type = $entry::TYPE_FAKE_PLAYER;
        $entry->customName = $score->getText();
        $entry->score = $score->getValue();
        $entry->scoreboardId = $score->getId();

        $pk = new SetScorePacket();
        $pk->type = $pk::TYPE_CHANGE;
        $pk->entries[] = $entry;
        $player->sendDataPacket($pk);
    }

    static function deleteScore(Player $player, ScoreboardSlot $slot, int $scoreId): void {
        $entry = new ScorePacketEntry();
        $entry->objectiveName = $slot;
        $entry->scoreboardId = $scoreId;

        $pk = new SetScorePacket();
        $pk->type = $pk::TYPE_REMOVE;
        $pk->entries[] = $entry;
        $player->sendDataPacket($pk);
    }

    static function delete(Player $player, ScoreboardSlot $slot): void {
        $pk = new RemoveObjectivePacket();
        $pk->objectiveName = $slot->getText();
        $player->sendDataPacket($pk);
    }

    static function send(Player $player, Scoreboard $scoreboard): void {
        $pk = new SetDisplayObjectivePacket();
        $pk->displaySlot = $scoreboard->getSlot()->getText();
        $pk->objectiveName = $scoreboard->getSlot()->getText();
        $pk->displayName = $scoreboard->getTitle();
        $pk->criteriaName = "dummy";
        $pk->sortOrder = $scoreboard->getSortType()->getValue();
        $player->sendDataPacket($pk);
    }
}