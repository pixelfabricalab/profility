<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%scontrino_info}}`.
 */
class m261111_151608_add_document_number_column_to_scontrino_info_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%scontrino_info}}', 'document_number', $this->string(128)->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%scontrino_info}}', 'document_number');
    }
}
