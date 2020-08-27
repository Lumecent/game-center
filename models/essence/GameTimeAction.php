<?php

namespace app\models\essence;

use yii\db\ActiveRecord;

class GameTimeAction extends ActiveRecord
{
    public static function tableName()
    {
        return 'game_time_action';
    }
}