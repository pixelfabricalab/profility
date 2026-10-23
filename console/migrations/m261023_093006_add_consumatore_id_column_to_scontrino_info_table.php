<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%scontrino_info}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%profilo}}`
 */
class m261023_093006_add_consumatore_id_column_to_scontrino_info_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%scontrino_info}}', 'consumatore_id', $this->integer());

        // creates index for column `consumatore_id`
        $this->createIndex(
            '{{%idx-scontrino_info-consumatore_id}}',
            '{{%scontrino_info}}',
            'consumatore_id'
        );

        // add foreign key for table `{{%profilo}}`
        $this->addForeignKey(
            '{{%fk-scontrino_info-consumatore_id}}',
            '{{%scontrino_info}}',
            'consumatore_id',
            '{{%profilo}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%profilo}}`
        $this->dropForeignKey(
            '{{%fk-scontrino_info-consumatore_id}}',
            '{{%scontrino_info}}'
        );

        // drops index for column `consumatore_id`
        $this->dropIndex(
            '{{%idx-scontrino_info-consumatore_id}}',
            '{{%scontrino_info}}'
        );

        $this->dropColumn('{{%scontrino_info}}', 'consumatore_id');
    }
}
