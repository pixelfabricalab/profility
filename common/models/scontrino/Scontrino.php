<?php

namespace common\models\scontrino;

use Yii;

/**
 * This is the model class for table "scontrino_info".
 *
 * @property int $id
 * @property string|null $receipt_id
 * @property int|null $merchant_id
 * @property string|null $issued_at
 * @property string|null $cash_register_serial
 * @property float|null $total_amount
 * @property string|null $currency
 *
 * @property Azienda $merchant
 * @property Riga[] $scontrinoRigas
 */
class Scontrino extends \yii\db\ActiveRecord
{

    public $merchant;
    public $items;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'scontrino_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['receipt_id', 'merchant_id', 'issued_at', 'cash_register_serial', 'total_amount', 'currency'], 'default', 'value' => null],
            [['merchant_id'], 'integer'],
            [['issued_at', 'merchant', 'items'], 'safe'],
            [['total_amount'], 'number'],
            [['receipt_id'], 'string', 'max' => 36],
            [['cash_register_serial'], 'string', 'max' => 64],
            [['currency'], 'string', 'max' => 3],
            [['merchant_id'], 'exist', 'skipOnError' => true, 'targetClass' => Azienda::class, 'targetAttribute' => ['merchant_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'receipt_id' => 'Receipt ID',
            'merchant_id' => 'Merchant ID',
            'issued_at' => 'Issued At',
            'cash_register_serial' => 'Cash Register Serial',
            'total_amount' => 'Total Amount',
            'currency' => 'Currency',
        ];
    }

    /**
     * Gets query for [[Merchant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMerchant()
    {
        return $this->hasOne(Azienda::class, ['id' => 'merchant_id']);
    }

    /**
     * Gets query for [[ScontrinoRigas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScontrinoRigas()
    {
        return $this->hasMany(Riga::class, ['documento_id' => 'id']);
    }

}
