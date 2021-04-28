<?php


namespace game_chef\pmmp\scoreboard;


use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Scoreboard
{
    static protected ScoreboardSlot $slot;
    static protected string $title;
    static protected ScoreSortType $sortType;
    static protected array $scores;
    static private bool $autoIndex;

    private function __construct( string $title, array $scores, ScoreSortType $sortType, bool $autoIndex = true) {
        self::$title = $title;
        self::$sortType = $sortType;
        self::$scores = $scores;
        self::$autoIndex = $autoIndex;
    }

    static function init(ScoreboardSlot $slot): void {
        self::$slot = $slot;
    }

    protected static function __create(string $title, array $scores, ScoreSortType $sortType, bool $autoIndex = true): Scoreboard {
        if (self::$slot === null) {
            throw new \LogicException("Scoreboard::__createの前に、Scoreboard::initでslotを設定してください");
        }
        return new Scoreboard($title, $scores, $sortType, $autoIndex);
    }

    protected static function __send(Player $player, Scoreboard $scoreboard): void {
        ScoreboardPMMPService::send($player, $scoreboard);

        if (self::$autoIndex) {
            $usedValues = [];
            foreach (self::$scores as $score) {
                if ($score->getValue() !== null) $usedValues[] = $score->getValue();
            }

            $index = 0;
            foreach ($scoreboard->getScores() as $score) {
                if ($score->getValue() !== null) {
                    self::addScore($player, $score);
                    continue;
                }

                while (in_array($index, $usedValues)) {
                    $index++;
                }

                $usedValues[] = $index;
                self::addScore($player, new Score($score->getText(), $index, $index));
            }
        } else {
            foreach ($scoreboard->getScores() as $score) {
                self::addScore($player, $score);
            }
        }
    }

    protected static function __update(Player $player, Scoreboard $scoreboard): void {
        self::__delete($player);
        self::__send($player, $scoreboard);
    }

    static function __delete(Player $player): void {
        ScoreboardPMMPService::delete($player, self::$slot);
    }

    public static function addScore(Player $player, Score $score): void {
        self::$scores[] = $score;

        if (self::hasSameTextScore($score->getText())) {
            $text = $score->getText() . str_repeat(TextFormat::RESET, self::countSameTextScore($score->getText()));
            $score = new Score($text, $score->getValue(), $score->getId());
        }
        ScoreboardPMMPService::addScore($player, self::$slot, $score);
    }

    public static function deleteScore(Player $player, Score $targetScore): void {
        foreach (self::$scores as $index => $score) {
            if ($score->getId() === $targetScore) {
                unset(self::$scores[$index]);
            }
        }

        ScoreboardPMMPService::deleteScore($player, self::$slot, $targetScore->getId());
    }

    static function updateScore(Player $player, Score $score) {
        self::deleteScore($player, $score);
        self::addScore($player, $score);
    }

    public static function getSlot(): ScoreboardSlot {
        return self::$slot;
    }

    public static function getTitle(): string {
        return self::$title;
    }

    public static function getSortType(): ScoreSortType {
        return self::$sortType;
    }

    public static function getScores(): array {
        return self::$scores;
    }

    public static function getAutoIndex(): bool {
        return self::$autoIndex;
    }


    private static function hasSameTextScore(string $text): bool {
        foreach (self::$scores as $score) {
            if ($score->getText() === $text) {
                return true;
            }
        }

        return false;
    }

    private static function countSameTextScore(string $text): int {

        $count = 0;

        foreach (self::$scores as $score) {
            if ($score->getText() === $text) {
                $count++;
            }
        }

        return $count;
    }
}