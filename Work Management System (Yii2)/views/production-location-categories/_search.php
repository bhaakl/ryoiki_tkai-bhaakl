<?php

use yii\helpers\Html;
use app\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductionLocationCategory */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="form-search">

    <?php
    $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row search-block">

        <!--<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?= $form->field($model, 'search')->textInput(['maxlength' => true]) ?>
        </div>-->
		<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
		<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?= $form->field($model, 'comments')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                <label for="" class="control-label">&nbsp;</label>
                <div class="input-group0">
                    <?= Html::submitButton('<i class="fa fa-trash"></i>', ['class' => 'btn btn-default btn-outline-dark', 'title' => 'Очистить', 'onclick' => "var form = jQuery(this.form); form.find('input[type=text]').val(''); form.find('select').val(0); return true;"]) ?>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
    <hr/>
</div>
