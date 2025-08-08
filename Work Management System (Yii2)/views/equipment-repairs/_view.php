<?php

use app\helpers\NumberHelper;
use app\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\widgets\MaskedInput;
use yii\widgets\Pjax;

/* @var $model \app\models\EquipmentRepair */
/* @var $data array */

Pjax::begin(['id' => 'edit-form-container']);

$form = ActiveForm::begin([
    'enableClientValidation' => true,
    'enableAjaxValidation' => true,
    'validateOnSubmit' => true,
    'options' => [
        'id' => 'edit-form',
        'data' => ['pjax' => true],
        'class' => 'disable-double-sending',
    ]]);
?>

    <div class="form-edit">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <?= $form->field($model, 'equipment_unit_id')->dropDownList($data['equipmentUnits'])->label('Наименование оборудования'); ?>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <?= $form->field($model, 'status_id')->dropDownList($data['statuses'])->label('Статус'); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <?php echo $form->field($model, 'defect')->textarea() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <?php echo $form->field($model, 'reason')->textarea() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <?php
                echo $form->field($model, 'when_broken', [
                    'options' => [
                        'class' => 'drp-container mb-2'
                    ]
                ])->widget(DatePicker::class, [
                    'language' => 'ru',
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'DD.MM.YYYY'
                    ]
                ])->label('Дата выхода из строя');
                ?>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <?php
                echo $form->field($model, 'when_repaired', [
                    'options' => [
                        'class' => 'drp-container mb-2'
                    ]
                ])->widget(DatePicker::class, [
                    'language' => 'ru',
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'DD.MM.YYYY'
                    ]
                ])->label('Дата устранения поломки');
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <?= $form->field($model, 'downtime')->textInput()->label('Общее время простоя, ч'); ?>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <?= $form->field($model, 'repair_cost')->widget(MaskedInput::class, [
                    'name' => 'repair_cost',
                    'clientOptions' => NumberHelper::CURRENCY_INPUT_CONFIG,
                ])->label('Стоимость ремонта'); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <?php echo $form->field($model, 'comments')->textarea() ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('Вход', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

<?php
ActiveForm::end();

Pjax::end();
?>

