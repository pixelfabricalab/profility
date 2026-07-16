<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%scontrino_azienda}}`.
 */
class m260716_101815_create_scontrino_azienda_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%scontrino_azienda}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
            'vat_number' => $this->string(32),
            'address' => $this->string(128),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%scontrino_azienda}}');
    }
}
