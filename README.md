# Game Chef

## Game
### 試合の登録
### 試合に参加
### 試合から抜ける
### 試合の開始
### 試合を終了させる

## FFAGame
### マップの作成
### 試合の作成
### スポーン地点を設定
### 一括でスポーン地点を設定
### スコアを追加

## TeamGame
### マップの作成
### 試合の作成
### チームの移動
### スポーン地点を設定
### 一括でスポーン地点を設定
### スコアを追加


```php
use game_chef\TeamGameBuilder;
use game_chef\models\GameType;
use game_chef\models\Score;

try {
    $builder = new TeamGameBuilder();
    $builder->setNumberOfTeams(2);
    $builder->setGameType(new GameType(""));
    $builder->setTimeLimit(400);
    $builder->setVictoryScore(new Score(30));
    $builder->setCanJumpIn(true);
    $builder->selectMapByName("");

    //マップ中から使用するチームだけをsetUpする
    $builder->setUpTeam("", 10, 0);
    $builder->setFriendlyFire(false);
    $builder->setMaxPlayersDifference(2);
    $builder->setCanMoveTeam(true);
} catch (Exception $exception) {

}

```


# 依存関係
できるだけ減らす  
form_builder  
scoreboard_builder  
bossbar_system
