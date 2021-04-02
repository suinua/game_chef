<?php


namespace game_assistant\models;


//Memo:マップと同期してなくてはだめ。マップにセットされてるものから選択できるように
class Team
{
    private TeamId $id;
    private string $name;
    private Score $score;

    private ?int $maxPlayer;
    private ?int $minPlayer;
    private string $TeamColorFormat;
}

class TeamId {

}

