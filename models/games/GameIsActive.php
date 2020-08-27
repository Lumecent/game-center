<?php

namespace app\models\games;

use app\models\essence\Pair;
use Yii;

class GameIsActive
{
    protected $gamesPath = [
        'account/games/pair'
    ];

    public function __construct($userID)
    {
        if ($userID && !$this->isGamePath()){
            $pair = Pair::find()->where('(player_one = ' . $userID . ' OR player_two = ' . $userID . ') AND status = "active"')->one();

            if ($pair){
                Yii::$app->getResponse()->redirect('/account/games/pair');
            }
        }
    }

    public function isGamePath()
    {
        return in_array(Yii::$app->request->pathInfo, $this->gamesPath);
    }
}