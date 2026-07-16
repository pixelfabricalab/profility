<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%scontrino_riga}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%scontrino_info}}`
 */
class m260716_102536_create_scontrino_riga_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%scontrino_riga}}', [
            'id' => $this->primaryKey(),
            'documento_id' => $this->integer(),
            'description' => $this->string(128),
            'quantity' => $this->integer(),
            'unit_price' => $this->decimal(10,2),
            'total_price' => $this->decimal(10,2),
        ]);

        // creates index for column `documento_id`
        $this->createIndex(
            '{{%idx-scontrino_riga-documento_id}}',
            '{{%scontrino_riga}}',
            'documento_id'
        );

        // add foreign key for table `{{%scontrino_info}}`
        $this->addForeignKey(
            '{{%fk-scontrino_riga-documento_id}}',
            '{{%scontrino_riga}}',
            'documento_id',
            '{{%scontrino_info}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%scontrino_info}}`
        $this->dropForeignKey(
            '{{%fk-scontrino_riga-documento_id}}',
            '{{%scontrino_riga}}'
        );

        // drops index for column `documento_id`
        $this->dropIndex(
            '{{%idx-scontrino_riga-documento_id}}',
            '{{%scontrino_riga}}'
        );

        $this->dropTable('{{%scontrino_riga}}');
    }
}
