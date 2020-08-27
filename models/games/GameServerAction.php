<?php

namespace app\models\games;

use app\models\essence\GameTimeAction;

class GameServerAction
{
    protected $stopServer = false;
    protected $pidFile = '/tmp/server_games_action.pid';

    public function stopServer()
    {
        $this->stopServer = true;
    }

    public function daemonAlive()
    {
        $child_pid = pcntl_fork();

        if ($child_pid){
            exit;
        }

        posix_setsid();
    }

    public function daemonIsAlive()
    {
        if (is_file($this->pidFile)){
            $pid = file_get_contents($this->pidFile);

            if (posix_kill($pid,0)){
                return true;
            }else{
                if (!unlink($this->pidFile)){
                    exit(-1);
                }
            }
        }

        return false;
    }

    public function server()
    {
        $this->daemonAlive();

        pcntl_signal(SIGTERM, [$this, 'stopServer']);
        pcntl_signal_dispatch();

        if ($this->daemonIsAlive()) {
            echo 'Server game action already active';

            exit;
        }

        file_put_contents($this->pidFile, getmypid());

        $this->start();
    }

    public function start()
    {
        while (!$this->stopServer){
            $actions = GameTimeAction::find()->where('action_time <= ' . time() . '')->all();

            if ($actions){
                foreach ($actions as $key => $action){
                    $message = [
                        'user_id' => $action->player_id,
                        'game_key' => $action->security_key,
                        'game' => $action->game,
                        'game_action' => $action->action_type
                    ];

                    $instance = stream_socket_client('tcp://192.168.83.137:1234');

                    fwrite($instance, json_encode($message)  . "\n");

                    $action->delete();
                }
            }
        }
    }
}