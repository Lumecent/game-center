<?php

use yii\db\Migration;

/**
 * Class m200816_200551_game_pair
 */
class m200816_200551_game_pair extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('game_pair', [
            'id' => $this->primaryKey(),
            'player_one' => $this->integer(),
            'player_two' => $this->integer(),
            'game_box' => $this->text(),
            'sum_pair' => $this->integer(),
            'found_pair' => $this->integer()->defaultValue(0),
            'player_active' => $this->integer(),
            'time_active' => $this->integer(),
            'hide_active_cell' => $this->integer(),
            'cell_active_one' => $this->integer(),
            'cell_active_two' => $this->integer(),
            'cell_player_one' => $this->integer()->defaultValue(0),
            'cell_player_two' => $this->integer()->defaultValue(0),
            'winner' => $this->integer(),
            'status' => $this->string(),
            'type' => $this->string(),
            'message' => $this->string(),
            'ai_memory' => $this->text(),
            'security_key' => $this->string(),
            'date_start' => 'datetime DEFAULT CURRENT_TIMESTAMP()',
            'date_end' => $this->dateTime()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('game_pair');
    }
}
