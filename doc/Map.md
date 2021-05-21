# Mapの仕様

## Levelについて
ゲームで仕様するワールドはオリジナルをコピーしたものになります  
例:BattleMap(オリジナル) → BattleMap+848d24d53c4d(uuid)+forGameChef    
試合が終了するか、鯖を閉じた際にコピーされたワールドはすべて削除されます  

## Mapの概要
`/map`でマップを管理することができます  
TeamGameとFFAGameに分かれています  
### TeamGame
- マップ名
- 対応するゲームタイプ
- チームデータ
   - チーム名
   - チームカラー(テキストフォーマット)
   - 最大人数
   - 最小人数
   - スポーン地点
   - カスタム座標データ
   - カスタム配列型座標データ
- カスタム座標データ
- カスタム配列型座標データ

### FFAGame
 - マップ名
 - 対応するゲームタイプ
 - スポーン地点
 - カスタム座標データ
 - カスタム配列型座標データ

## (配列型)カスタム座標データについて
### カスタム座標データ
`カスタム座標データ`とは、ユーザーがマップに自由に設定(登録)できる座標データです  
ユニークなkeyを登録して、プラグインから取得すること出来ます  
例えば、試合中にある場所に武器をスポーンさせたい時、マップに武器のスポーン地点を設定することができます  
```php
$gameId = null;
$game = \game_chef\api\GameChef::findGameById($gameId);
$map = $game->getMap();
$vector3 = $map->getCustomVectorData("key");//取得
$vector3List = $map->getCustomVectorDataList();//マップに登録されているカスタム座標データ一覧
```

### 配列型カスタム座標データ
`配列型カスタム座標データ`は、一つのkeyに対して複数の座標データを登録できるものです  
`カスタム座標データ`は一つのkeyに対して座標データ１つです  
```php
$gameId = null;
$game = \game_chef\api\GameChef::findGameById($gameId);
$map = $game->getMap();
$vector3 = $map->getCustomArrayVectorData("key");//取得
$vector3List = $map->getCustomArrayVectorDataList();//マップに登録されている配列型カスタム座標データ一覧
```


### チームの(配列型)カスタム座標データ
TeamGameにのみ存在します  
マップの`(配列型)カスタム座標データ`と何ら代わりありませんが、チーム単位で登録できます  
これによって、そのチーム特有の座標データを登録できます  
例えば、CorePvPなどでコアの座標なんかをチーム毎に登録できます  

Teamから呼び出します  
```php
$gameId = null;
$game = \game_chef\api\GameChef::findTeamGameById($gameId);
$teamId = null;

$team = $game->getTeamById($teamId);
$vector3 = $team->getCustomVectorData("key");//取得
//etc...
```