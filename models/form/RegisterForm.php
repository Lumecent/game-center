<?php

namespace app\models\form;

use app\components\validators\EmailUniqueValidator;
use app\models\essence\User;
use Yii;
use yii\base\Model;

class RegisterForm extends Model
{
    public $login;
    public $email;
    public $password;
    public $password_repeat;

    public function rules()
    {
        return [
            ['login', 'required', 'message' => 'Введите логин'],
            [
                'login', 'string',
                'min' => 3, 'tooShort' => 'Логин может содержать не менее 3 символов',
                'max' => 10, 'tooLong' => 'Логин может содержать не более 10 символов'
            ],
            ['login', 'trim'],

            ['email', 'required', 'message' => 'Введите E-mail адрес'],
            ['email', 'email', 'message' => 'Введен неверный формат E-mail адреса'],
            ['email', EmailUniqueValidator::class],

            ['password', 'required', 'message' => 'Введите пароль'],
            [
                'password', 'string',
                'min' => 6, 'tooShort' => 'Пароль может содержать не менее 6 символов'
            ],
            ['password', 'compare', 'message' => 'Пароли не совпадают'],

            ['password_repeat', 'required', 'message' => 'Введите пароль'],
        ];
    }

    public function save()
    {
        if ($this->validate()){
            $user = new User();

            $user->login = $this->login;
            $user->email = $this->email;
            $user->password = Yii::$app->security->generatePasswordHash($this->password);
            $user->security_key = Yii::$app->security->generateRandomString(20);

            if ($user->save()){
                return $user;
            }
        }

        return false;
    }
}