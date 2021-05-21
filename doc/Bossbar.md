# BossBarSystem
仕様は [bossbar_api](https://github.com/suinua/bossbar_api) とほぼ同じです

BossBarには `ID`と`TYPE` の２つがあります。
`ID`は一つ一つが固有なものです。ユーザーが指定することはありません。

`TYPE`は複数のボスバーが同じ値を持つことができますが、一つのプレイヤーが同じ`TYPE`のボスバーを持つことはできません。  
ユーザー自身が指定します。(BossBarTypesなどのクラスを作って管理するといいと思います)

### 生成
```php
use game_chef\pmmp\bossbar\Bossbar;
use game_chef\pmmp\bossbar\BossbarType;
use pocketmine\Player;

/** @var Player $player */
$bossbar = new Bossbar($player, new BossbarType("Lobby"), "Hello!", 1.0);
```

### 送り方
```php
use game_chef\pmmp\bossbar\Bossbar;

/** @var Bossbar $bossbar */
$bossbar->send();
```

### 取得
```php
use game_chef\pmmp\bossbar\BossBar;
use game_chef\pmmp\bossbar\BossbarType;
use game_chef\pmmp\bossbar\BossbarId;
use pocketmine\Player;

/** @var BossbarId $bossbarId */
$bossbar = Bossbar::findById($bossbarId);

/** @var Player $player */
/** @var BossbarType $bossbarType */
$bossbar = BossBar::findByType($player,$bossbarType);

$bossbar = BossBar::getBossbars($player);
```

### 削除
```php
use game_chef\pmmp\bossbar\Bossbar;

/** @var Bossbar $bossbar */
$bossbar->remove();
```

### 更新
```php
use game_chef\pmmp\bossbar\Bossbar;

/** @var Bossbar $bossbar */
$bossbar->updatePercentage(0.5);
$bossbar->updateTitle("50%");
```

### GameTypeからBossbarTypeへ変換
```php
$gameType = new \game_chef\models\GameType("");
$gameType->toBossbarType();
```
