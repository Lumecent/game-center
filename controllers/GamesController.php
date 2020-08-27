<?php

namespace app\controllers;

use app\models\essence\Pair;
use app\models\essence\User;
use app\models\games\connect\ConnectGamePair;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class GamesController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'denyCallback' => function(){$this->goHome();},
                'rules' => [
                    [
                        'actions' => [
                            'pair', 'pair-settings'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionPair()
    {
        $this->view->title = 'Пары';

        $userID = Yii::$app->user->getId();
        $user = User::findOne($userID);

        $pair = Pair::find()->where('(player_one = ' . $userID . ' OR player_two = ' . $userID . ') AND status != "end" ')->one();

        if (!$pair){
            return $this->redirect('/account/games');
        }

        return $this->render('pair', compact('pair', 'user'));
    }

    public function actionPairSettings()
    {
        $this->view->title = 'Настройки игры пары';

        $model = new ConnectGamePair();

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())){
            $model->userID = Yii::$app->user->getId();

            if ($model->connectGame()){
                return $this->redirect(['account/games/pair']);
            }
        }

        return $this->render('pair-settings', compact('model'));
    }


}