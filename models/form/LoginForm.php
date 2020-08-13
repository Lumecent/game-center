<?php

namespace app\models\form;

use app\models\essence\User;
use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $email;
    public $password;
    public $remember = false;

    private $_user = null;

    public function rules()
    {
        return [
            ['email', 'required', 'message' => 'Введите E-mail адрес'],
            ['email', 'email', 'message' => 'Введен неверный формат E-mail адреса'],

            ['password', 'required', 'message' => 'Введите пароль'],
            ['password', 'validatePassword'],
        ];
    }

    public function validatePassword($attribute)
    {
        $user = $this->getUser();

        if (!$user || !$user->validatePassword($this->$attribute)){
            $this->addError($attribute, 'E-mail или пароль введены неверно');
        }
    }

    public function getUser()
    {
        if ($this->_user === null){
            $this->_user = User::findOne(['email' => $this->email]);
        }

        return $this->_user;
    }

    public function login()
    {
        if ($this->validate()){
            $user = $this->getUser();

            $user->security_key = Yii::$app->security->generateRandomString(20);
            $user->save();

            return Yii::$app->user->login($user, $this->remember ? 86400*30 : 0);
        }

        return false;
    }
}