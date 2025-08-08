<?php

use app\grid\GridViewClean;
use app\models\MeasureUnit;
use app\widgets\ActiveForm;
use app\widgets\Card;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->urlManager->getLastTitle();
?>
<div class="row justify-content-center">
    <div class="col-xl-5 col-sm-8">
        <?php Card::begin([]); ?>
            <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
            <?php foreach ($dataProvider->models as $model): ?>
                <li class="nav-item" role="presentation">
                    <a class="nav-link<?= $model->id === 1 ? ' active' : '' ?>" data-bs-toggle="tab" href="#notification<?= $model->id ?>" role="tab">
                        <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                        <span class="d-none d-sm-block"><?= $model->name ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
            </ul>
        <div class="tab-content p-3 text-muted">
            <?php foreach ($dataProvider->models as $model): ?>
            <div class="tab-pane<?= $model->id === 1 ? ' active show' : '' ?>" id="notification<?= $model->id ?>" role="tabpanel">
                <?php
                $form = ActiveForm::begin(); ?>
                <?= Html::hiddenInput('id', $model->id) ?>
                <div class="mb-3">
                    <?= $form->field($model, 'description')->textarea(['disabled' => 1, 'rows' => 6]) ?>
                </div>
                <div class="mb-3">
                    <?= $form->field($model, 'template')->textarea(['rows' => 6]) ?>
                </div>
                <div class="mb-3">
                    <?= $form->field($model, 'comments')->textInput() ?>
                </div>
                <div class="input-group0">
                    <?= Html::submitButton('<i class="fa fa-save"> Сохранить</i>', ['class' => 'btn btn-default btn-outline-primary', 'title' => 'Сохранить']) ?>
                    <?= Html::button('<i class="fa fa-trash"> Отменить изменения</i>', ['class' => 'btn btn-default btn-outline-dark', 'title' => 'Отменить изменения', 'onclick' => "location.reload()"]) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php Card::end(); ?>
    </div>
</div>