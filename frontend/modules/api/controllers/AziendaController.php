<?php
namespace frontend\modules\api\controllers;

use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;

class AziendaController extends ActiveController
{
    public $modelClass = 'common\models\scontrino\Azienda';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];

        /*
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'index'  => ['GET', 'HEAD'],
                'view'   => ['GET', 'HEAD'],
                'create' => ['POST'],
                'update' => [],
                'delete' => [],
            ],
        ];
        */

        return $behaviors;
    }

}
