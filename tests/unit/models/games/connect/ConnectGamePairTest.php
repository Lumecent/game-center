<?php namespace models\games\connect;

use app\models\games\connect\ConnectGamePair;
use app\tests\fixtures\PairFixture;
use app\tests\fixtures\UserFixture;

class ConnectGamePairTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
        $this->tester->haveFixtures([
           'user' => [
               'class' => UserFixture::class,
               'dataFile' => codecept_data_dir() . 'user.php'
           ],

           'pair' => [
               'class' => PairFixture::class,
               'dataFile' => codecept_data_dir() . 'pair.php'
           ]
        ]);
    }

    public function testConnectNewGamePairSolo()
    {
        $model = new ConnectGamePair([
            'userID' => 1,
            'game_box' =>  20,
            'type' => 'solo'
        ]);

        expect_that($model->connectGame());
    }

    public function testConnectNewGamePairAI()
    {
        $model = new ConnectGamePair([
            'userID' => 1,
            'game_box' =>  40,
            'type' => 'ai'
        ]);

        expect_that($model->connectGame());
    }

    public function testConnectNewGamePairPlayer()
    {
        $model = new ConnectGamePair([
            'userID' => 1,
            'game_box' =>  60,
            'type' => 'player'
        ]);

        expect_that($model->connectGame());
    }

    public function testConnectExistsGamePairPlayer()
    {
        $model = new ConnectGamePair([
            'userID' => 1,
            'game_box' =>  20,
            'type' => 'player'
        ]);

        expect_that($model->connectGame());
    }

    public function testConnectNewGamePairExists()
    {
        $model = new ConnectGamePair([
            'userID' => 3,
            'game_box' =>  20,
            'type' => 'player'
        ]);

        expect_not($model->connectGame());
    }

    public function testConnectNewGamePairWrongGameBox()
    {
        $model = new ConnectGamePair([
            'userID' => 1,
            'game_box' =>  15,
            'type' => 'solo'
        ]);

        expect_not($model->connectGame());

        $model = new ConnectGamePair([
            'userID' => 1,
            'game_box' =>  '',
            'type' => 'solo'
        ]);

        expect_that($model->connectGame());
    }

    public function testConnectNewGamePairWrongType()
    {
        $model = new ConnectGamePair([
            'userID' => 1,
            'game_box' =>  20,
            'type' => 'user'
        ]);

        expect_not($model->connectGame());

        $model = new ConnectGamePair([
            'userID' => 1,
            'game_box' =>  20,
            'type' => ''
        ]);

        expect_that($model->connectGame());
    }
}