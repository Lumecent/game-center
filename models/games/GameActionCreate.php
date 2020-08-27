<?php

namespace app\models\games;

use app\models\essence\GameTimeAction;

class GameActionCreate
{
    public static function createAction($game, $gameName, $gameAction, $wait)
    {
        $newAction = new GameTimeAction();

        $newAction->player_id = $game->player_active;
        $newAction->game = $gameName;
        $newAction->security_key = $game->security_key;
        $newAction->action_time = time() + $wait;
        $newAction->action_type = $gameAction;

        $newAction->save();
    }

    public static function deleteGameAction($userID, $gameName, $gameKey, $gameAction)
    {
        $action = GameTimeAction::find()->where('player_id = "' . $userID . '" AND game = "' . $gameName . '" AND security_key = "' . $gameKey . '" AND action_type = "' . $gameAction . '"')->one();

        if ($action) $action->delete();
    }
}