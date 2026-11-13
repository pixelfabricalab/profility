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

        // Default pagination caps per-page at 50: raise the limit.
        $actions['index']['prepareDataProvider'] = function ($action) {
            $modelClass = $action->modelClass;
            $requestParams = \Yii::$app->getRequest()->getBodyParams();
            if (empty($requestParams)) {
                $requestParams = \Yii::$app->getRequest()->getQueryParams();
            }

            return \Yii::createObject([
                'class' => \yii\data\ActiveDataProvider::class,
                'query' => $modelClass::find(),
                'pagination' => [
                    'params' => $requestParams,
                    'defaultPageSize' => 20,
                    'pageSizeLimit' => [1, 1000],
                ],
                'sort' => ['params' => $requestParams],
            ]);
        };

        return $actions;
    }
}
