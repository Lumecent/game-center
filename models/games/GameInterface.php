<?php

namespace app\models\games;

interface GameInterface
{
    public function gameExists($userID, $gameKey);
    public function start($userID, $data);
    public function renderBox();
    public function excludeUser($game);
    public function endGame($game);
    public function renderGameBox();
    public function createMessage();
    public function giveUp();

}