<?php
namespace frontend\controllers;

use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\VerbFilter;

class ScontrinoController extends ActiveController
{
    public $modelClass = 'common\models\scontrino\Scontrino';

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

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        $actions = parent::actions();
        
        $createAction = $actions['create'];
        $createAction['class'] = 'frontend\controllers\rest\CreateAction';
        $actions['create'] = $createAction;

        return $actions;
    }
}
