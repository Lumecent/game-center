<?php

require(__DIR__ . '/vendor/autoload.php');
require(__DIR__ . '/vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/config/console.php');
new yii\console\Application($config);

use app\models\games\GameServerAction;

// Start render game box
(new GameServerAction())->server();
