<?php

use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use yii\helpers\Html;
use app\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\EquipmentRepairSearch */
/* @var $form yii\widgets\ActiveForm */
/* @var $data array */
?>

<div class="form-search">

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
                <div class="input-group0">
                    <?= Html::submitButton('<i class="fa fa-search"></i>', ['class' => 'btn btn-primary', 'title' => 'Поиск']) ?>
                    <?= Html::submitButton('<i class="fa fa-trash"></i>', ['class' => 'btn btn-default btn-outline-dark', 'title' => 'Очистить', 'onclick' => "var form = jQuery(this.form); form.find('input[type=text]').val(''); form.find('select').val(0); return true;"]) ?>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
            <div class="form-group">
                <label for="" class="control-label">&nbsp;</label>
                <div class="input-group0 text-end">
                    <?= Html::button('<i class="fa fa-filter"></i>', [
                        'class' => 'btn btn-info',
                        'title' => 'Все фильтры',
                        'onclick' => '$("#filters").toggle("fast");',
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="collapse" id="filters">
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <?php
                    echo $form->field($model, 'equipmentUnit.category_id')->widget(Select2::classname(), [
                        'data' => $data['categories'],
                        'options' => ['placeholder' => 'Все'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label('Категория оборудования');
                ?>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <?php
                    echo $form->field($model, 'equipment_unit_id')->widget(Select2::classname(), [
                        'data' => $data['equipmentUnits'],
                        'options' => ['placeholder' => 'Все'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label('Наименование оборудования');
                ?>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <?php
                    echo $form->field($model, 'status_id')->widget(Select2::classname(), [
                        'data' => $data['statuses'],
                        'options' => ['placeholder' => 'Все'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label('Статус');
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <?php
                    echo $form->field($model, 'job_id')->widget(Select2::classname(), [
                        'data' => $data['jobs'],
                        'options' => ['placeholder' => 'Все'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label('Должность сотрудника');
                ?>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <?php
                    echo $form->field($model, 'division_id')->widget(Select2::classname(), [
                        'data' => $data['divisions'],
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
                        'data' => $data['users'],
                        'options' => ['placeholder' => 'Все'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label('Сотрудник');
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <?php
                    echo $form->field($model, 'created', [
                        'options' => [
                            'class' => 'drp-container mb-2'
                        ]
                    ])->widget(DateRangePicker::class, [
                        'language' => 'ru',
                        'presetDropdown'=>true,
                        'pluginOptions' => [
                             'locale' => [
                                 'format' => 'DD.MM.YYYY',
                             ]
                        ]
                    ])->label('Запись добавлена');
                ?>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <?php
                    echo $form->field($model, 'when_broken', [
                        'options' => [
                            'class' => 'drp-container mb-2'
                        ]
                    ])->widget(DateRangePicker::class, [
                        'language' => 'ru',
                        'presetDropdown'=>true,
                        'pluginOptions' => [
                            'locale' => [
                                'format' => 'DD.MM.YYYY',
                            ]
                        ]
                    ])->label('Дата выхода из строя');
                ?>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <?php
                    echo $form->field($model, 'when_repaired', [
                        'options' => [
                            'class' => 'drp-container mb-2'
                        ]
                    ])->widget(DateRangePicker::class, [
                        'language' => 'ru',
                        'presetDropdown'=>true,
                        'pluginOptions' => [
                            'locale' => [
                                'format' => 'DD.MM.YYYY',
                            ]
                        ]
                    ])->label('Дата устранения поломки');
                ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
    <hr/>
</div>
