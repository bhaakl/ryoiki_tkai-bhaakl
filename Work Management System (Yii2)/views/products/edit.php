<?php

use app\models\forms\ProductWProductionStagesForm;
use app\widgets\MultipleInput;
use kartik\select2\Select2;
use yii\helpers\Html;
use app\widgets\ActiveForm;
use app\widgets\Card;

/* @var $this yii\web\View */
/* @var $model app\models\Product */
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
            'id' => 'products-form',
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
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3">
            <?= $form->field($model, 'category_id')->widget(Select2::classname(), [
                'data' => $data['categories'],
                'options' => ['placeholder' => 'Выберите'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Категория'); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                <?php $productionStagesForm = new ProductWProductionStagesForm(); ?>
                <?= $form->field(
                    $productionStagesForm,
                    $productionStagesForm->key
                )->widget(MultipleInput::class, [
                    'data' => $data['productProductionStages'],
                    'min' => 0,
                    'addButtonPosition' => MultipleInput::POS_FOOTER,
                    'columns' => [
                        [
                            'name' => 'id',
                            'type'  => 'hiddenInput',
                            'enableError' => true,
                        ],
                        [
                            'name' => 'product_id',
                            'type'  => 'hiddenInput',
                            'enableError' => true,
                        ],
                        [
                            'title' => 'Участок',
                            'type'  => 'dropDownList',
                            'name' => 'production_stage_id',
                            'enableError' => true,
                            'items' => $data['productionStages'],
                            'options' => ['prompt' => 'Выберите...'],
                        ],
                        [
                            'title' => 'Комментарии',
                            'name' => 'comments',
                            'enableError' => true,
                        ],
                    ]
                ])->label('Этапы производства'); ?>
            </div>
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

