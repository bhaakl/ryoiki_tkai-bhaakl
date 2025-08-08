<?php

use app\models\search\WorkerReportCardSearch;
use kartik\select2\Select2;
use kartik\date\DatePicker;
use yii\helpers\Html;
use app\widgets\ActiveForm;
use app\models\User;
use app\models\Job;
use app\models\Division;

/* @var $this yii\web\View */
/* @var $model WorkerReportCardSearch */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="form-search">

    <?php
    $form = ActiveForm::begin(); ?>

    <div class="row search-block">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?= $form->field($model, 'worker_id')->widget(Select2::class, [
                'data' => User::listAll('id', 'fio'),
                'options' => ['placeholder' => 'Все'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Сотрудник'); ?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?= $form->field($model, 'worker_job_id')->widget(Select2::class, [
                'data' => Job::listAll('id', 'name'),
                'options' => ['placeholder' => 'Все'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Должность'); ?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?php
            echo $form->field($model, 'worker_division_id')->widget(Select2::class, [
                'data' => Division::listAll('id', 'name'),
                'options' => ['placeholder' => 'Все'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Подразделение');
            ?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?= $form->field($model, 'type')->dropDownList(WorkerReportCardSearch::TYPES)->label('Тип'); ?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?= $form->field($model, 'work_date')->widget(DatePicker::class, [
                'removeButton' => false,
                'readonly' => true,
                'pluginOptions' => [
                    'autoclose' => true,
                    'allowClear' => false,
                    'format' => 'yyyy-mm-dd',
                ],
            ])->label('Дата'); ?>
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