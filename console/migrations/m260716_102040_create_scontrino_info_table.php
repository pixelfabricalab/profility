<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%scontrino_info}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%scontrino_azienda}}`
 */
class m260716_102040_create_scontrino_info_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%scontrino_info}}', [
            'id' => $this->primaryKey(),
            'receipt_id' => $this->string(36),
            'merchant_id' => $this->integer(),
            'issued_at' => $this->dateTime(),
            'cash_register_serial' => $this->string(64),
            'total_amount' => $this->decimal(10,2),
            'currency' => $this->string(3),
        ]);

        // creates index for column `merchant_id`
        $this->createIndex(
            '{{%idx-scontrino_info-merchant_id}}',
            '{{%scontrino_info}}',
            'merchant_id'
        );

        // add foreign key for table `{{%scontrino_azienda}}`
        $this->addForeignKey(
            '{{%fk-scontrino_info-merchant_id}}',
            '{{%scontrino_info}}',
            'merchant_id',
            '{{%scontrino_azienda}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%scontrino_azienda}}`
        $this->dropForeignKey(
            '{{%fk-scontrino_info-merchant_id}}',
            '{{%scontrino_info}}'
        );

        // drops index for column `merchant_id`
        $this->dropIndex(
            '{{%idx-scontrino_info-merchant_id}}',
            '{{%scontrino_info}}'
        );

        $this->dropTable('{{%scontrino_info}}');
    }
}
