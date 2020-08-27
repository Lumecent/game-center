<?php

use yii\db\Migration;

/**
 * Class m200823_165848_game_time_action
 */
class m200823_165848_game_time_action extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('game_time_action', [
            'id' => $this->primaryKey(),
            'game' => $this->string(),
            'player_id' => $this->integer(),
            'security_key' => $this->string(),
            'action_time' => $this->integer(),
            'action_type' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('game_time_action');
    }
}
