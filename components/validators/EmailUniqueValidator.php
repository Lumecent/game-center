<?php

namespace app\components\validators;

use app\models\essence\User;
use yii\validators\Validator;

class EmailUniqueValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        if (User::findOne(['email' => $model->$attribute])){
            $this->addError($model, $attribute, 'Введенный E-mail уже зарегистрирован в системе');
        }
    }
}