<?php

namespace app\controllers;

use app\models\form\LoginForm;
use app\models\form\RegisterForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['login', 'register', 'logout'],
                'denyCallback' => function(){$this->goHome();},
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['login', 'register'],
                        'allow' => true,
                        'roles' => ['?']
                    ]
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        $this->view->title = 'Авторизация';

        $model = new LoginForm();

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())){
            if ($model->login()){
                return $this->redirect(['/account/']);
            }
        }

        return $this->render('login', compact('model'));
    }

    public function actionRegister()
    {
        $this->view->title = 'Регистрация';

        $model = new RegisterForm();

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())){
            if ($user = $model->save()){
                Yii::$app->user->login($user);

                return $this->redirect(['/account/']);
            }
        }

        return $this->render('register', compact('model'));
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
