<?php

use app\models\CashFlowTransactionCategory;
use app\models\PayrollCalculation;
use app\models\User;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use yii\helpers\Html;
use app\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PayrollCalculation */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="form-search">

    <?php
    $form = ActiveForm::begin(); ?>

    <div class="row search-block">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?php
            echo $form->field($model, 'user_state_id')->widget(Select2::class, [
                'data' => User::STATE_NAMES,
                'options' => ['placeholder' => 'Все'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Статус');
            ?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?php
            echo $form->field($model, 'salary_category_id')->widget(Select2::class, [
                'data' => CashFlowTransactionCategory::listAll('id', 'name'),
                'options' => ['placeholder' => 'Все'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Категория');
            ?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?php
            echo $form->field($model, 'account_type_id')->widget(Select2::class, [
                'data' => PayrollCalculation::getAccountTypes(),
                'options' => ['placeholder' => 'Все'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?php
            echo $form->field($model, 'user_id')->widget(Select2::class, [
                'data' => User::listAll('id', 'fio'),
                'options' => ['placeholder' => 'Все'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Сотрудник');
            ?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?php
            echo $form->field($model, 'created')->widget(DateRangePicker::class, [
                'pluginOptions' => [
                    'allowClear' => true,
                    'locale' => ['format' => 'YYYY-MM-DD'],
                ],
            ])->label('Дата');
            ?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?= $form->field($model, 'comments')->textInput(['maxlength' => true, 'id' => 'period', 'autocomplete' => 'off']) ?>
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