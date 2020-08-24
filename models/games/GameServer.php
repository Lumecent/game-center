<?php

namespace app\models\games;

use app\models\essence\User;
use Workerman\Worker;

class GameServer
{
    protected $users = [];

    public function setUserGameConnect($game, $gameKey, $connect)
    {
        $this->users[$game] = [$gameKey => [$connect]];
    }

    public function getUserGameConnect($game, $gameKey)
    {
        return $this->users[$game][$gameKey] ?? null;
    }

    public function server()
    {
        $ws_worker = new Worker('websocket://0.0.0.0:2346');

        $ws_worker->count = 1;

        $ws_worker->onWorkerStart = function() use (&$users)
        {
            $inner_tcp_worker = new Worker("tcp://192.168.83.137:1234");

            $inner_tcp_worker->onMessage = function($connection, $data){
                $data = json_decode($data);

                $userID = $data->user_id ?? null;
                $userKey = $userID;
                $gameKey = $data->game_key ?? null;
                $game = $data->game ?? null;

                $this->verifyUser($userKey, $gameKey, $game, $userID, $data);
            };

            $inner_tcp_worker->listen();
        };

        $ws_worker->onConnect = function ($connection){
            $connection->onWebSocketConnect = function($connection){
                $userKey = $_GET['user_key'] ?? null;
                $gameKey = $_GET['game_key'] ?? null;
                $game = $_GET['game'] ?? null;

                $this->setUserGameConnect($game, $gameKey, $connection);

                $this->verifyUser($userKey, $gameKey, $game);
            };
        };

        $ws_worker->onMessage = function ($connection, $data) {
            $data = json_decode($data);

            $userKey = $data->user_key ?? null;
            $gameKey = $data->game_key ?? null;
            $game = $data->game ?? null;

            $this->verifyUser($userKey, $gameKey, $game, null, $data);
        };
    }

    public function verifyUser($userKey, $gameKey, $game, $userID = null, $data = null)
    {
        if ($userKey && $gameKey && $game){
            if (is_null($userID)){
                $userID = User::findOne(['security_key' => $userKey])->id;
            }

            if ($userID && GameCenter::gameExists($game, $userID, $gameKey)){
                $message = GameCenter::action($userID, $data);

                $this->sendMessage($game, $gameKey, $message);
            }
        }
    }

    public function sendMessage($game, $gameKey, $message)
    {
        $connections = $this->getUserGameConnect($game, $gameKey);

        if ($connections){
            foreach ($connections as $connection){
                $connection->send(json_encode($message));
            }
        }
    }
}