<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Scontrino $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Scontrini', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="scontrino-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'profilo_id',
            'content:ntext',
            'creato_il',
            'modificato_il',
        ],
    ]) ?>

</div>
