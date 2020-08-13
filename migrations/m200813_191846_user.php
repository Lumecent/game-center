<?php

use yii\db\Migration;

/**
 * Class m200813_191846_user
 */
class m200813_191846_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'login' => $this->string(),
            'email' => $this->string(),
            'password' => $this->string(),
            'auth_key' => $this->string(),
            'security_key' => $this->string(),
            'logo' => $this->string(),
            'date_reg' => 'datetime DEFAULT CURRENT_TIMESTAMP()'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('user');
    }
}
