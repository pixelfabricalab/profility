<?php

namespace common\models\scontrino;

use Yii;

/**
 * This is the model class for table "scontrino_riga".
 *
 * @property int $id
 * @property int|null $documento_id
 * @property string|null $description
 * @property int|null $quantity
 * @property float|null $unit_price
 * @property float|null $total_price
 *
 * @property Scontrino $documento
 */
class Riga extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'scontrino_riga';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['documento_id', 'description', 'quantity', 'unit_price', 'total_price'], 'default', 'value' => null],
            [['documento_id', 'quantity'], 'integer'],
            [['unit_price', 'total_price'], 'number'],
            [['description'], 'string', 'max' => 128],
            [['documento_id'], 'exist', 'skipOnError' => true, 'targetClass' => Scontrino::class, 'targetAttribute' => ['documento_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'documento_id' => 'Documento ID',
            'description' => 'Description',
            'quantity' => 'Quantity',
            'unit_price' => 'Unit Price',
            'total_price' => 'Total Price',
        ];
    }

    /**
     * Gets query for [[Documento]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumento()
    {
        return $this->hasOne(Scontrino::class, ['id' => 'documento_id']);
    }

}
