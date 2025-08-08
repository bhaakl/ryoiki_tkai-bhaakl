<?php

use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\Html;
use app\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Product */
/* @var $form yii\widgets\ActiveForm */
/* @var $productList array */
/* @var $unitList array */
/* @var $monthYear string */

?>

<div class="form-search">

    <?php
    $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'id' => 'editable-table-search'
    ]); ?>

    <div class="row">

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?php
            echo $form->field($model, 'search_name')->widget(Select2::class, [
                'data' => $productList,
                'options' => ['placeholder' => 'Все'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Виды работ');
            ?>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?php
            echo $form->field($model, 'search_type')->widget(Select2::class, [
                'data' => $unitList,
                'options' => ['placeholder' => 'Все'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('ЕИ');
            ?>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?php
            echo Html::label("Месяц");
            echo DatePicker::widget([
                'name' => 'month_year',
                'options' => ['placeholder' => 'Выберите месяц и год...'],
                'value' => $monthYear,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'mm-yyyy', // или 'M yyyy' для текстового отображения месяца
                    'minViewMode' => 'months',
                    'viewMode' => 'months',
                    'startView' => 'year',
                ]
            ]);
            ?>
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