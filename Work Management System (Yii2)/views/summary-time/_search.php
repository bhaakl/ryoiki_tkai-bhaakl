<?php

use app\models\search\SummaryTimeSearch;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\Html;
use app\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model SummaryTimeSearch */
/* @var $form yii\widgets\ActiveForm */
/* @var $productionStageNameList array */
/* @var $monthYear string */

?>

<div class="form-search search-block">

    <?php
    $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row search-block">

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?php
            echo $form->field($model, 'product_id')->widget(Select2::class, [
                'data' => \app\models\Product::listAll('id', 'name'),
                'options' => ['placeholder' => 'Все'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Продукт');
            ?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?= $form->field($model, 'work_month')->widget(DatePicker::class, [
                'value' => $model->work_month,
                'removeButton' => false,
                'readonly' => true,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm', // или 'M yyyy' для текстового отображения месяца
                    'minViewMode' => 'months',
                    'viewMode' => 'months',
                    'startView' => 'year',
                ]
            ]);
            ?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?= $form->field($model, 'type')->dropDownList(SummaryTimeSearch::TYPES)->label('Тип'); ?>
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