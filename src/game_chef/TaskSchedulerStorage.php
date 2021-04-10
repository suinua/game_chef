<?php


namespace game_chef;


use pocketmine\scheduler\TaskScheduler;

//本当は良くないだろうけど、めんどくさくなっちゃった
class TaskSchedulerStorage
{
    static private TaskScheduler $taskScheduler;

    static function init($taskScheduler): void {
        self::$taskScheduler = $taskScheduler;
    }

    static function get(): TaskScheduler {
        return self::$taskScheduler;
    }
}