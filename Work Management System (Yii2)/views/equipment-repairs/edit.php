<?php

use app\helpers\NumberHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\Html;
use app\widgets\ActiveForm;
use app\widgets\Card;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $data array */

$action = ($model->isNewRecord ? 'Новая запись' : 'Редактирование');
$this->title = Html::encode(Yii::$app->urlManager->getLastTitle() . ' - ' . $action .
    ($model->isNewRecord ? '' : ' #' . $model->id ));

if (!$model->isNewRecord) $this->params['breadcrumbs'][] = (string)$model;
$this->params['breadcrumbs'][] = $action;

\app\assets\Select2aAsset::register($this);

?>

<?php Card::begin([]); ?>

<div class="edit-form">

    <?php
        $form = ActiveForm::begin([
            'id' => 'equipment-repairs-form',
            'enableAjaxValidation'      => true,
            'enableClientValidation'    => false,
            'validateOnChange'          => false,
            'validateOnSubmit'          => true,
            'validateOnBlur'            => false,
            'options' => [
                'autocomplete' => 'off',
                'enctype' => 'multipart/form-data'
            ]
        ]);
    ?>

    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6">
            <?= $form->field($model, 'equipment_unit_id')->widget(Select2::classname(), [
                'data' => $data['equipmentUnits'],
                'options' => ['placeholder' => 'Все'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Наименование оборудования'); ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3">
            <?= $form->field($model, 'status_id')->widget(Select2::classname(), [
                'data' => $data['statuses'],
                'options' => ['placeholder' => 'Все'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Статус'); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <?php echo $form->field($model, 'defect')->textarea() ?>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <?php echo $form->field($model, 'reason')->textarea() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-3">
            <?php
                echo $form->field($model, 'when_broken', [
                    'options' => [
                        'class' => 'drp-container mb-2'
                    ]
                ])->widget(DatePicker::class, [
                    'language' => 'ru',
                    'options' => [
                        'value' => !empty($model->when_broken) ? Yii::$app->formatter->asDate($model->when_broken, 'php:d.m.Y') : null,
                    ],
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd.mm.yyyy',
                        'todayHighlight' => true,
                    ]
                ])->label('Дата выхода из строя');
            ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3">
            <?php
                echo $form->field($model, 'when_repaired', [
                    'options' => [
                        'class' => 'drp-container mb-2'
                    ]
                ])->widget(DatePicker::class, [
                    'language' => 'ru',
                    'options' => [
                        'value' => !empty($model->when_repaired) ? Yii::$app->formatter->asDate($model->when_repaired, 'php:d.m.Y') : null,
                    ],
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd.mm.yyyy',
                        'todayHighlight' => true,
                    ]
                ])->label('Дата устранения поломки');
            ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3">
            <?= $form->field($model, 'downtime')->textInput()->label('Общее время простоя, ч'); ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3">
            <?= $form->field($model, 'repair_cost')->widget(MaskedInput::class, [
                'name' => 'repair_cost',
                'clientOptions' => NumberHelper::CURRENCY_INPUT_CONFIG,
            ])->label('Стоимость ремонта'); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6">
            <?= $form->field($model, 'repairers')->widget(Select2::classname(), [
                'data' => $data['users'],
                'options' => ['placeholder' => 'Не выбрано', 'multiple' => true, 'autocomplete' => 'off'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Кто устранял'); ?>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <?php echo $form->field($model, 'comments')->textarea() ?>
        </div>
    </div>

    <div>
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a('Отмена', $this->context->getReferrer(), ['class' => 'btn btn-soft-dark']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php Card::end() ?>

