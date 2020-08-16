<?php

namespace app\components\validators;

use app\models\essence\Pair;
use yii\validators\Validator;

class GameExistsValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $pair = Pair::find()->where('(player_one = ' . $model->userID . ' OR player_two = ' . $model->userID . ') AND status != "end"')->one();

        if ($pair){
            $this->addError($model, $attribute, 'Завершите текущую активную игру прежде чем начать новую');
        }
    }
}