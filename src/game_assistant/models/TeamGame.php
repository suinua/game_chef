<?php


namespace game_assistant\models;


class TeamGame extends Game
{
    private array $teams;
    private TeamGameMap $map;

    protected int $maxPlayersDifference;

}