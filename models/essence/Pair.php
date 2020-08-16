<?php

namespace app\models\essence;

use yii\db\ActiveRecord;

class Pair extends ActiveRecord
{
    public static function tableName()
    {
        return 'game_pair';
    }
}