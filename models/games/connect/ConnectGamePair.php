<?php

namespace app\models\games\connect;

use app\components\validators\GameExistsValidator;
use app\models\essence\Pair;
use app\models\games\generate\GenerateGamePair;
use Yii;
use yii\base\Model;

class ConnectGamePair extends Model
{
    public $userID;
    public $game_box;
    public $game_exists;
    public $type;

    public function rules()
    {
        return [
            ['game_box', 'default', 'value' => 20],
            ['game_box', 'in', 'range' => [20, 40, 60], 'message' => 'Указан неверный размер игрового поля'],

            ['type', 'default', 'value' => 'solo'],
            ['type', 'in', 'range' => ['solo', 'ai', 'player'], 'message' => 'Указан неверный тип игры'],

            ['game_exists', GameExistsValidator::class, 'skipOnEmpty' => false]
        ];
    }

    public function connectGame()
    {
        if ($this->validate()){
            if ($this->type == 'player'){
                $game = Pair::find()->where('type = "player" AND sum_pair = "' . $this->game_box / 2 . '" AND status = "wait"')->one();

                if ($game && $game->player_one != $this->userID){
                    $transaction = Pair::getDb()->beginTransaction();

                    try {
                        $game->status = 'active';
                        $game->player_two = $this->userID;

                        $players = [$game->player_one, $game->player_two];

                        $game->player_active = $players[rand(0, 1)];
                        $game->time_active = time() + 180;

                        $game->save();

                        $transaction->commit();
                    }catch (\Exception $e){
                        $transaction->rollBack();

                        $this->connectGame();
                    }

                    return true;
                }
            }

            return $this->createNewGame();
        }

        return false;
    }

    public function createNewGame()
    {
        $game = new Pair();

        $newGame = new GenerateGamePair();
        $game->player_one = $this->userID;
        $game->game_box = $newGame->runGame($this->game_box);
        $game->sum_pair = $newGame->getPair();
        $game->type = $this->type;

        if ($this->type == 'solo'){
            $game->status = 'active';
            $game->player_active = $game->player_one;
        }elseif ($this->type == 'ai'){
            $game->status = 'active';
            $players = [$game->player_one, 0];
            $game->player_active = $players[rand(0, 1)];
            $game->time_active = time() + 180;
        }else{
            $game->status = 'wait';
        }

        $game->security_key = Yii::$app->security->generateRandomString(20);

        return $game->save();
    }
}