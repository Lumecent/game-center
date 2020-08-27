<?php

namespace app\models\games;

class GameAction
{
    protected $gameActions = [];

    protected $_game = false;
    protected $_userID;

    public function setGame($game)
    {
        $this->_game = $game;
    }

    public function getGame()
    {
        return $this->_game;
    }

    public function setUserID($userID)
    {
        $this->_userID = $userID;
    }

    public function getUserID()
    {
        return $this->_userID;
    }

    public function getAction($action)
    {
        return $this->gameActions[$action] ?? 'renderBox';
    }

    public function getGameBox()
    {
        return explode(';', $this->getGame()->game_box);
    }

    public function createGameAction($game, $gameName, $gameAction, $wait)
    {
        GameActionCreate::createAction($game, $gameName, $gameAction, $wait);
    }

    public function deleteGameAction($userID, $gameName, $gameKey, $gameAction)
    {
        GameActionCreate::deleteGameAction($userID, $gameName, $gameKey, $gameAction);
    }

    public function gameMessage($game)
    {
        if ($game->status == 'end'){
            return $game->message;
        }

        if ($game->status == 'wait'){
            return 'Ожидание противника';
        }

        if ($game->player_active == $this->getUserID()){
            return 'Ваш ход';
        }else{
            return 'Ожидайте ход противника';
        }
    }
}