<?php
namespace backend\controllers;

use backend\controllers\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class StatisticheController extends Controller {
    /**
    * @inheritDoc
    */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
           ]
        );
    }
    
    public function actionGenerali() {
        return $this->render('index');
    }

    public function actionPartecipanti() {
        return $this->render('partecipanti');
    }
    
    public function actionCustomerSatisfaction() {
        return $this->render('customersatisfaction');
    }
    
    public function actionQuadrantAnalysis() {
        return $this->render('quadrantanalysis');
    }
    
    public function actionProdotti() {
        return $this->render('prodotti');
    }

    public function actionRegimiSpesa() {
        return $this->render('regimispesa');
    }

    public function actionPositioningAnalysis() {
        return $this->render('positioninganalysis');
    }
}