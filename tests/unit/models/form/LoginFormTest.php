<?php

namespace models\form;

use app\models\form\LoginForm;
use app\tests\fixtures\UserFixture;

class LoginFormTest extends \Codeception\Test\Unit
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

    public function testLoginUser()
    {
        $model = new LoginForm([
            'email' => 'login1@mail.loc',
            'password' => 'my_password',
        ]);

        expect_that($model->login());
    }

    public function testLoginUserWrongEmail()
    {
        $model = new LoginForm([
            'email' => '',
            'password' => 'my_password'
        ]);

        expect_not($model->login());

        $model->email = 'login1@loc';

        expect_not($model->login());

        $model->email = 'login@mail.loc';

        expect_not($model->login());
    }

    public function testLoginUserWrongPassword()
    {
        $model = new LoginForm([
            'email' => 'login1@mail.loc',
            'password' => ''
        ]);

        expect_not($model->login());

        $model->password = 'not_exist_pass';

        expect_not($model->login());
    }
}