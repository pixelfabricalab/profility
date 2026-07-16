<?php

namespace common\models\scontrino;

use Yii;

/**
 * This is the model class for table "scontrino_azienda".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $vat_number
 * @property string|null $address
 *
 * @property ScontrinoInfo[] $scontrinoInfos
 */
class Azienda extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'scontrino_azienda';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'vat_number', 'address'], 'default', 'value' => null],
            [['name', 'address'], 'string', 'max' => 128],
            [['vat_number'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'vat_number' => 'Vat Number',
            'address' => 'Address',
        ];
    }

    /**
     * Gets query for [[ScontrinoInfos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScontrinoInfos()
    {
        return $this->hasMany(Scontrino::class, ['merchant_id' => 'id']);
    }

    /**
     * Get Azienda by VAT number.
     *
     * @param string $vat_number
     * @return Azienda|null
     */
    public static function getAziendaByVatNumber($vat_number)
    {
        return self::findOne(['vat_number' => $vat_number]);
    }

}
