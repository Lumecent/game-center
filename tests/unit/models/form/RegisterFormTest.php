<?php

namespace models\form;

use app\models\form\RegisterForm;
use app\tests\fixtures\UserFixture;

class RegisterFormTest extends \Codeception\Test\Unit
{
    protected $tester;

    protected function _before()
    {
        $this->tester->haveFixtures([
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . '/user.php',
            ]
        ]);
    }

    public function testRegisterUser()
    {
        $model = new RegisterForm([
            'login' => 'new_login',
            'email' => 'my@mail.loc',
            'password' => 'password',
            'password_repeat' => 'password',
        ]);

        expect_that($model->save());
    }

    public function testRegisterUserWrongLogin()
    {
        $model = new RegisterForm([
            'login' => '',
            'email' => 'my@mail.loc',
            'password' => 'password',
            'password_repeat' => 'password',
        ]);

        expect_not($model->save());

        $model->login = 'jo';

        expect_not($model->save());

        $model->login = 'wrong_length_login';

        expect_not($model->save());
    }

    public function testRegisterUserWrongEmail()
    {
        $model = new RegisterForm([
            'login' => 'new_login',
            'email' => '',
            'password' => 'password',
            'password_repeat' => 'password',
        ]);

        expect_not($model->save());

        $model->email = 'my@mail';

        expect_not($model->save());

        $model->email = 'login2@mail.loc';

        expect_not($model->save());
    }

    public function testRegisterUserWrongPassword()
    {
        $model = new RegisterForm([
            'login' => 'new_login',
            'email' => 'my@mail.loc',
            'password' => '',
            'password_repeat' => 'password',
        ]);

        expect_not($model->save());

        $model->password = 'short';

        expect_not($model->save());

        $model->password = 'password';
        $model->password_repeat = 'other_password';

        expect_not($model->save());

        $model->password_repeat = '';

        expect_not($model->save());
    }
}