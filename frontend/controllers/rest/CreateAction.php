<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace frontend\controllers\rest;

use Yii;
use common\models\scontrino\Azienda;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;
use yii\rest\CreateAction as BaseCreateAction;
/**
 * CreateAction implements the API endpoint for creating a new model from the given data.
 *
 * For more details and usage information on CreateAction, see the [guide article on rest controllers](guide:rest-controllers).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 *
 * @template T of \yii\rest\Controller = \yii\rest\Controller
 * @extends Action<T>
 */
class CreateAction extends BaseCreateAction
{
    /**
     * Creates a new model.
     * @return \yii\db\ActiveRecordInterface the model newly created
     * @throws ServerErrorHttpException if there is any error when creating the model
     */
    public function run()
    {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        /** @var \yii\db\ActiveRecord $model */
        $model = new $this->modelClass([
            'scenario' => $this->scenario,
        ]);

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        // Issued at
        $model->issued_at = date('Y-m-d H:i:s');

        // get the company data
        if (isset($model->merchant) && $model->merchant['vat_number']) {
            $azienda = Azienda::getAziendaByVatNumber($model->merchant['vat_number']);
            if ($azienda) {
                $model->merchant_id = $azienda->id;
            } else {
                $azienda = new Azienda();
                $azienda->name = $model->merchant['name'] ?? null;
                $azienda->vat_number = $model->merchant['vat_number'];
                $azienda->address = $model->merchant['address'] ?? null;
                if ($azienda->save()) {
                    $model->merchant_id = $azienda->id;
                } else {
                    throw new ServerErrorHttpException('Failed to create the merchant for unknown reason.');
                }
            }
        } else {
            throw new ServerErrorHttpException('Merchant VAT number is required.');
        }

        // $azienda = Azienda::getAziendaByVatNumber($model->merchant_vat_number);
        if ($model->save()) {
            $this->handleItems($model);
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
            $id = implode(',', $model->getPrimaryKey(true));
            $response->getHeaders()->set('Location', Url::toRoute([$this->viewAction, 'id' => $id], true));
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }

    private function handleItems($model)
    {
        // Handle items
        if (isset($model->items) && is_array($model->items)) {
            foreach ($model->items as $itemData) {
                $item = new \common\models\scontrino\Riga();
                $item->load($itemData, '');
                $item->documento_id = $model->id;
                if (!$item->save()) {
                    throw new ServerErrorHttpException('Failed to create the item for unknown reason.');
                }
            }
        }
    }
}
