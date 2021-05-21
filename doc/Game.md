# Game
GameはTeamGameとFFAGameの二種類に分かれます  

## GameTypeについて
`GameType`とはユーザーが指定するもので、ゲームの種類を表しています  
例:GameType("TeamDeathMatch"),GameType("CorePvP")  
これのおかげで複数種類のゲームを同時に進行することを可能にしています  
GameTypeはユニークなものでないので、複数の試合タイプを複数個同時進行することが可能です  
例:TeamDeathMatchを4つ CorePvPを3つ　同時進行   

## TeamGame
 - ゲームタイプ
 - チーム数
 - 時間制限
 - 勝利判定スコア
 - 途中参加の許可
 - フレンドリーファイア
 - チームの最大人数差
 - チーム移動の許可
 
#### 試合を作成する
```php
$builder = new \game_chef\api\TeamGameBuilder();
$builder->setNumberOfTeams(2);
$builder->setGameType(new \game_chef\models\GameType(""));
$builder->setTimeLimit(600);
$builder->setVictoryScore(new \game_chef\models\Score(30));
$builder->setCanJumpIn(true);

$mapNames = \game_chef\API\GameChef::getTeamGameMapNamesByType(new \game_chef\models\GameType(""));
$builder->selectMapByName($mapNames[0]);//randomにしたほうがいい

$builder->setFriendlyFire(false);
$builder->setMaxPlayersDifference(2);
$builder->setCanMoveTeam(true);

$game = $builder->build();
\game_chef\API\GameChef::registerGame($game);
```
 
 
## FFAGame
 - ゲームタイプ
 - 時間制限
 - 勝利判定スコア
 - 途中参加の許可
 - 最大人数

#### 試合を作成する
```php
$ffaGameBuilder = new \game_chef\api\FFAGameBuilder();
$ffaGameBuilder->setGameType(new \game_chef\models\GameType(""));
$ffaGameBuilder->setMaxPlayers(null);
$ffaGameBuilder->setTimeLimit(600);
$ffaGameBuilder->setVictoryScore(new \game_chef\models\Score(15));
$ffaGameBuilder->setCanJumpIn(true);

$mapNames = \game_chef\API\GameChef::getFFAGameMapNamesByType(new \game_chef\models\GameType(""));
$ffaGameBuilder->selectMapByName($mapNames[0]);

$ffaGame = $ffaGameBuilder->build();
\game_chef\API\GameChef::registerGame($ffaGame);
``` 