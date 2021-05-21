# Scoreboard
仕様は [scoreboard_builder](https://github.com/suinua/scoreboard_builder) と全く同じです

1,Scoreboardを継承したクラスを作成
2,__setup,__create(),__send(),__update()を用いて関数を実装
3,一番最初(onEnable)に__setupを実行
あとはScoreboardクラスのパブリックな関数をみてください
```php
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use game_chef\pmmp\scoreboard\Score;
use game_chef\pmmp\scoreboard\Scoreboard;
use game_chef\pmmp\scoreboard\ScoreboardSlot;
use game_chef\pmmp\scoreboard\ScoreSortType;

class Sample extends Scoreboard
{
    public static function init(): void {
        self::__setup(ScoreboardSlot::sideBar());
    }
    
    private static function create(Player $player): Scoreboard {
        $scores = [
            new Score(TextFormat::RESET . "----------------------"),
            new Score(TextFormat::BOLD . TextFormat::YELLOW . "お金:"),//new Score($text,$value)で、$valueに値を入れなければ自動で0から順にに重複していない数を与える
            new Score(TextFormat::BOLD ."> 所持金:100"),
            new Score(TextFormat::BOLD ."> 銀行残高:10000"),

            new Score(""),
            new Score(TextFormat::BOLD . TextFormat::YELLOW . "レベル:"),
            new Score(TextFormat::BOLD ."> 現在のレベル:12"),
            new Score(TextFormat::BOLD ."> 合計XP:120000"),
            new Score(TextFormat::BOLD ."> 次のレベルまで:10000"),
            new Score("----------------------"),
        ];
        return parent::__create("Sample", $scores, ScoreSortType::smallToLarge(), true);
    }

    static function send(Player $player) {
        $scoreboard = self::create($player);
        parent::__send($player, $scoreboard);
    }

    static function update(Player $player) {
        $scoreboard = self::create($player);
        parent::__update($player, $scoreboard);
    }
}

Sample::init();
Sample::send($player);
Sample::update($player);
```
