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

    <?php if ($model->informazioni) : ?>
        <h3>Informazioni elaborate</h3>
        <?php foreach ($model->informazioni as $info) : ?>
            <p>Receipt ID: <?= Html::encode($info->receipt_id) ?></p>
            <p>RT: <?= Html::encode($info->cash_register_serial) ?></p>
        <?php endforeach; ?>
    <?php endif; ?>

</div>
