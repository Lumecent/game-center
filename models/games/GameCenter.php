<?php

namespace app\models\games;

class GameCenter
{
    protected static $gameClasses = [
        'pair' => 'Pair'
    ];

    protected static $gameClass;

    public static function getGameClass($game)
    {
        return self::$gameClasses[$game] ?? null;
    }

    public static function setGameObj($gameClass){
        self::$gameClass = new $gameClass();
    }

    public static function getGameObj()
    {
        return self::$gameClass;
    }

    public static function gameExists($game, $userID, $gameKey)
    {
        $gameClass = self::getGameClass($game);

        if ($gameClass === null){
            return false;
        }

        $gameClass = 'app\models\games\action\Action' . $gameClass;

        if (class_exists($gameClass)){
            self::setGameObj($gameClass);

            return self::getGameObj()->gameExists($userID, $gameKey);
        }

        return false;
    }

    public static function action($userID, $data)
    {
        return self::getGameObj()->start($userID, $data);
    }
}