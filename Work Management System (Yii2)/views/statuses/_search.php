<?php

use app\models\SalaryPaymentCategory;
use app\models\Status;
use app\models\StatusScope;
use yii\helpers\Html;
use app\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Warehouse */
/* @var $form yii\widgets\ActiveForm */

\app\assets\Select2aAsset::register($this);
$js = <<< JS
    $('#pages-selector').select2();
JS;
$this->registerJs($js);

?>

<div class="form-search">

    <?php
    $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row search-block">

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?= $form->field($model, 'scope_id')->dropDownList(StatusScope::listAll('id', 'name'), ['prompt' => 'Все']) ?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?= $form->field($model, 'pages')->dropDownList(Status::PAGE_NAMES, ['prompt' => 'Все', 'class' => 'form-select', 'id' => 'pages-selector', 'multiple' => 1]) ?>
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
