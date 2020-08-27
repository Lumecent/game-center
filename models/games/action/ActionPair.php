<?php

namespace app\models\games\action;

use app\models\essence\Pair;
use app\models\games\GameAction;
use app\models\games\GameInterface;

class ActionPair extends GameAction implements GameInterface
{
    protected $gameActions = [
        'render' => 'renderBox',
        'cell' => 'cellActive',
        'ai' => 'aiAction',
        'exclude' => 'excludeAction',
        'give' => 'giveUp'
    ];

    /**
     * @var Pair
     */
    protected $_game = false;

    public function gameExists($userID, $gameKey)
    {
        $game = Pair::find()->where('(player_one = ' . $userID . ' OR player_two = ' . $userID . ') AND security_key = "' . $gameKey . '"')->one();

        if ($game){
            $this->setGame($game);

            return true;
        }

        return false;
    }

    public function start($userID, $data)
    {
        $this->setUserID($userID);

        if (!empty($data->game_action)){
            $action = $this->getAction($data->game_action);
        }else{
            $action = 'renderBox';
        }

        $cell = $data->cell ?? null;

        if (method_exists($this, $action) && $this->getGame()->status == 'active'){
            call_user_func([$this, $action], $cell);
        }

        return $this->createMessage();
    }

    public function renderBox()
    {
        $game = $this->getGame();

        $this->excludeUser($game);
        $this->checkedPair($game);
    }

    public function excludeAction()
    {
        $game = $this->getGame();

        $this->excludeUser($game);
    }

    public function cellActive($cell)
    {
        $game = $this->getGame();

        if ($this->cellIsActive($cell) && $game->player_active == $this->getUserID()){
            if ($game->type == 'solo' || time() < $game->time_active){
                $this->checkedCell($game, $cell);
            }else{
                $this->excludeUser($game);
            }
        }
    }

    public function aiAction()
    {
        $game = $this->getGame();

        $memory = explode(';', $game->ai_memory);
        $pair = [];
        $cell = null;

        foreach ($memory as $id => $value){
            if ($value){
                $pair = array_keys($memory, $value);

                if (count($pair) >= 2) break;
            }
        }

        if (count($pair) >= 2){
            foreach ($pair as $id => $key){
                if ($key !== $game->cell_active_one){
                    $cell = $key;
                }
            }
        }

        if (is_null($cell)){
            $gameBox = $this->getGameBox();

            $emptyCell = array_keys($gameBox, 'inactive');

            if ($emptyCell){
                foreach ($emptyCell as $id => $key){
                    unset($gameBox[$key]);
                }
            }

            $cell = array_rand($gameBox, 1);

            if ($cell == $game->cell_active_one){
                foreach ($gameBox as $key => $value){
                    if ($key !== $game->cell_active_one){
                        $cell = $key;
                    }
                }
            }
        }

        $this->cellActive($cell);
    }

    public function cellIsActive($cell)
    {
        $gameBox = $this->getGameBox();

        return $gameBox[$cell] && $gameBox[$cell] != 'inactive';
    }

    public function checkedCell($game, $cell)
    {
        if (is_null($game->cell_active_one)){
            $game->cell_active_one = $cell;

            if ($game->type == 'ai' && $game->player_active == 0){
                $this->createGameAction($game, 'pair', 'ai', 5);
            }
        }elseif (is_null($game->cell_active_two)){
            if ($game->cell_active_one != $cell){
                $game->cell_active_two = $cell;
                $game->hide_active_cell = time() + 5;

                $this->createGameAction($game, 'pair', 'render', 5);
                $this->deleteGameAction($game->player_active, 'pair', $game->security_key, 'exclude');
            }
        }

        $game->ai_memory = $this->aiMemory($game, $cell, 'check');

        $game->save();

        $this->setGame($game);
    }

    public function checkedPair($game)
    {
        $gameBox = $this->getGameBox();

        if (!is_null($game->hide_active_cell) && time() >= $game->hide_active_cell){
            $transaction = Pair::getDb()->beginTransaction();

            if ($gameBox[$game->cell_active_one] == $gameBox[$game->cell_active_two]){
                $game->found_pair += 1;

                if ($game->player_one == $this->getUserID()){
                    $game->cell_player_one += 1;
                }else{
                    $game->cell_player_two += 1;
                }

                if ($game->type != 'solo'){
                    $game->time_active = time() + 180;
                }

                $gameBox[$game->cell_active_one] = 'inactive';
                $gameBox[$game->cell_active_two] = 'inactive';

                $game->game_box = implode(';', $gameBox);
                $game->ai_memory = $this->aiMemory($game, [], 'delete');
            }else{
                if ($game->type != 'solo'){
                    if ($game->player_one == $this->getUserID()){
                        $game->player_active = $game->player_two;
                    }else{
                        $game->player_active = $game->player_one;
                    }

                    $game->time_active = time() + 180;
                    $this->createGameAction($game, 'pair', 'exclude', 185);
                }
            }

            if ($game->type == 'ai' && $game->player_active == 0){
                $this->createGameAction($game, 'pair', 'ai', 10);
            }

            $game->cell_active_one = null;
            $game->cell_active_two = null;
            $game->hide_active_cell = null;

            $game = $this->endGame($game);

            try {
                $game->save();

                $this->setGame($game);

                $transaction->commit();
            }catch (\Exception $e){
                $transaction->rollBack();
            }
        }
    }

    public function aiMemory($game, $cell, $type)
    {
        if ($game->type == 'ai'){
            $memory = explode(';', $game->ai_memory);
            $gameBox = $this->getGameBox();

            if ($type == 'check'){
                $memory[$cell] = $gameBox[$cell];
            }else{
                $memory[$game->cell_active_one] = '';
                $memory[$game->cell_active_two] = '';
            }

            return implode(';', $memory);
        }

        return $game->ai_memory;
    }

    public function excludeUser($game)
    {
        if ($game->time_active <= time() && $game->type != 'solo'){
            if ($game->player_active == $game->player_one){
                $game->winner = $game->player_two;
            }else{
                $game->winner = $game->player_one;
            }

            $game->status = 'end';
            $game->message = 'Игрок исключен за бездействие';
            $game->date_end = date('d-m-y H:i:s');
            $game->save();

            $this->setGame($game);
        }
    }

    public function giveUp()
    {
        $game = $this->getGame();

        $game->status = 'end';

        if ($game->player_one == $this->getUserID()){
            if (is_null($game->player_two)){
                $game->winner = 0;
            }else{
                $game->winner = $game->player_two;
            }
        }else{
            $game->winner = $game->player_one;
        }

        $game->message = 'Игрок сдался';
        $game->date_end = date('d-m-y H:i:s');
        $game->save();

        $this->setGame($game);
    }

    public function endGame($game)
    {
        if ($game->found_pair == $game->sum_pair){
            if ($game->type == 'solo'){
                $game->winner = $game->player_one;
            }else{
                if ($game->cell_player_one > $game->cell_player_two){
                    $game->winner = $game->player_one;
                }elseif ($game->cell_player_one == $game->cell_player_two){
                    $game->winner = null;
                }else{
                    $game->winner = $game->player_two;
                }
            }

            $game->status = 'end';
            $game->date_end = date('d-m-y H:i:s');
        }

        return $game;
    }

    public function renderGameBox()
    {
        $game = $this->getGame();

        $gameBox = [];

        foreach ($this->getGameBox() as $cell => $idPair){
            if ($idPair != 'inactive'){
                if (is_null($game->cell_active_one) && is_null($game->cell_active_two)){
                    $gameBox[] = 'question';
                }elseif (!is_null($game->cell_active_one) && $cell == $game->cell_active_one || !is_null($game->cell_active_two) && $cell == $game->cell_active_two){
                    $gameBox[] = $idPair;
                }else{
                    $gameBox[] = 'question';
                }
            }else{
                $gameBox[] = '';
            }
        }

        return $gameBox;
    }

    public function createMessage()
    {
        $game = $this->getGame();

        return [
            'box' => $this->renderGameBox(),
            'winner' => $game->winner,
            'player_one' => $game->player_one,
            'player_two' => $game->player_two,
            'player_active' => $game->player_active,
            'time_active' => $game->time_active - time(),
            'sum_pair' => $game->sum_pair,
            'found_pair' => $game->found_pair,
            'cell_player_one' => $game->cell_player_one,
            'cell_player_two' => $game->cell_player_two,
            'status' => $game->status,
            'type' => $game->type,
            'message' => $this->gameMessage($game)
        ];
    }
}