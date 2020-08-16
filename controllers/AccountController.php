<?php

namespace app\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;

class AccountController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'denyCallback' => function(){$this->goHome();},
                'rules' => [
                    [
                        'actions' => ['index', 'games'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $this->view->title = 'Мой аккаунт';

        return $this->render('index');
    }

    public function actionGames()
    {
        $this->view->title = 'Игры';

        return $this->render('games');
    }
}