# Game Chef

## マップの設定
### FFAGameMap
 - name
 - game type
 - spawn points
 - custom vector data list
 - custom vectors data list
 
## TeamGameMap
 - name
 - game type
 - team data list
    - name
    - color format
    - max players
    - min players
    - spawn points
    - custom team vector data list
    - custom team vectors data list
 - custom vector data list
 - custom vectors data list
 
## FFA,TeamGame共通のAPI
### 試合の登録
```php
```
### 試合に参加
```php
```
### 試合から抜ける
```php
```
### 試合の開始
```php
```
### 試合を終了させる
```php
```
## FFAGameのAPI
### マップの作成
### 試合の作成
```php
```
### スポーン地点を設定
```php
```
### 一括でスポーン地点を設定
```php
```
### スコアを追加
```php
```
## TeamGameのAPI
### マップの作成
### 試合の作成
```php
use game_chef\TeamGameBuilder;
use game_chef\models\GameType;
use game_chef\models\Score;

try {
    $builder = new TeamGameBuilder();
    $builder->setNumberOfTeams(2);//チーム数
    //TODO:GameTypeの解説
    $builder->setGameType(new GameType(""));
    $builder->setTimeLimit(400);//時間制限
    $builder->setVictoryScore(new Score(30));//勝利判定スコア
    $builder->setCanJumpIn(true);//途中参加
    $builder->selectMapByName("");//TODO: マップ選択 細かい説明

    //マップ中から使用するチームだけをsetUpする
    $builder->setUpTeam("", 10, 0);
    $builder->setFriendlyFire(false);//フレンドリーファイアー
    $builder->setMaxPlayersDifference(2);//チームの最大人数差
    $builder->setCanMoveTeam(true);//チーム移動
} catch (Exception $exception) {

}
```
### チームの移動
```php
```
### マップに設定した座標データを取得
### マップに設定した配列の座標データを取得
### スポーン地点を設定
```php
```
### 一括でスポーン地点を設定
```php
```
### スコアを追加
```php
```


## Event

### StartedGameEvent
```php
```
### FinishedGameEvent
```php
```
### PlayerJoinedGameEvent
```php
```
### PlayerQuitGameEvent
```php
```
### PlayerKilledPlayerEvent
```php
```
### UpdatedGameTimerEvent
```php
```
### AddedScoreEvent
```php
```

## その他API
### プレイヤーのデータ取得
```php
```
### 特定のゲームに参加しているプレイヤー一覧
```php
```
### 特定のチームに参加しているプレイヤー一覧
```php
```
### IDからゲーム取得
```php
```

# 依存関係
できるだけ減らす  
form_builder  