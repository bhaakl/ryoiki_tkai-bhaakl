<?php

use app\models\ProductType;
use yii\helpers\Html;
use app\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\ProductSearch */
/* @var $form yii\widgets\ActiveForm */
/* @var $data array */

\app\assets\Select2aAsset::register($this);
?>

<div class="form-search search-block">

    <?php
    $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <?= $form->field($model, 'search')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
            <div class="form-group">
                <label for="" class="control-label">&nbsp;</label>
                <div class="input-group">
                    <?= Html::submitButton('<i class="fa fa-trash"></i>', ['class' => 'btn btn-default btn-outline-dark', 'title' => 'Очистить', 'onclick' => "var form = jQuery(this.form); form.find('input[type=text]').val(''); form.find('select').val(0); return true;"]) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?= $form->field($model, 'type_id')->dropDownList(ProductType::listAll('id', 'name'), ['class' => 'form-control select2', 'prompt' => 'Все']) ?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?= $form->field($model, 'category_id')->dropDownList(\app\models\ProductCategory::listAll('id', 'name'), ['class' => 'form-control select2', 'prompt' => 'Все']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
    <hr/>
</div>

<script>
    window.onload = () => {
        $('.search-block .select2').select2();
    }
</script>
