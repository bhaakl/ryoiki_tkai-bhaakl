<?php

use app\models\User;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use yii\helpers\Html;
use app\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\PayrollStatementSearch */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="form-search">

    <?php
    $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'id' => 'editable-table-search'
    ]); ?>

    <div class="row search-block">



        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?php
            echo $form->field($model, 'job_id')->widget(Select2::classname(), [
                'data' => \app\models\Job::getList(),
                'options' => ['placeholder' => 'Все'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Должность');
            ?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?php
            echo $form->field($model, 'division_id')->widget(Select2::classname(), [
                'data' => \app\models\Division::getList(),
                'options' => ['placeholder' => 'Все'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Подразделение');
            ?>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?php
            echo $form->field($model, 'user_id')->widget(Select2::classname(), [
                'data' => \app\models\search\PayrollStatementSearch::getUsersList(),
                'options' => ['placeholder' => 'Все'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Сотрудник');
            ?>
        </div>


        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?php
            echo $form->field($model, 'state_id')->widget(Select2::class, [
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
            echo $form->field($model, 'range')->widget(DateRangePicker::class, [
                'pluginOptions' => [
                    'allowClear' => true,
                    'locale' => ['format' => 'YYYY-MM-DD'],
                ],
            ])->label('Период');
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