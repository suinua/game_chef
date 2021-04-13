<?php


namespace game_chef\pmmp\scoreboard;


class ScoreboardSlot
{
    private string $text;

    public function __construct(string $text) {
        $this->text = $text;
    }

    static function sideBar() : ScoreboardSlot {
        return new ScoreboardSlot("sidebar");
    }

    static function list() : ScoreboardSlot {
        return new ScoreboardSlot("list");
    }

    public function getText(): string {
        return $this->text;
    }
}