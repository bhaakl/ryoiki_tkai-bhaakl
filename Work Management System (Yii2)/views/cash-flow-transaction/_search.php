<?php

use app\models\CashFlowTransaction;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use yii\helpers\Html;
use app\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CashFlowTransaction */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="form-search">

    <?php
    $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => ['data-pjax' => true],
        'id' => 'search-form',
    ]); ?>

    <div class="row search-block">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?php
            echo $form->field($model, 'cash_flow_transaction_category_id')->widget(Select2::class, [
                'data' => CashFlowTransaction::getCashFlowTransactionCategoryList(),
                'options' => ['placeholder' => 'Все'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Категория ДДС');
            ?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?php
            echo $form->field($model, 'type')->widget(Select2::class, [
                'data' => CashFlowTransaction::getTransactionTypes(),
                'options' => ['placeholder' => 'Все'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Тип');
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
            <?php
            echo $form->field($model, 'account_type_id')->widget(Select2::class, [
                'data' => CashFlowTransaction::getAccountTypes(),
                'options' => ['placeholder' => 'Все'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Тип счета');
            ?>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                <label for="" class="control-label">&nbsp;</label>
                <div class="input-group0">
                    <?= Html::submitButton('<i class="fa fa-trash"></i>', ['class' => 'btn btn-default btn-outline-dark', 'title' => 'Очистить', 'onclick' => "var form = jQuery(this.form); form.find('input[type=text]').val(''); form.find('select').val(''); return true;"]) ?>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
    <hr/>
</div>

<?php
$this->registerJs(<<<JS
    $(document).ready(function() {
        // Сохраняем выбранное значение в sessionStorage
        $('#search-form').on('change', 'select[name="CashFlowTransactionSearch[type]"]', function() {
            var selectedValue = $(this).val();
            sessionStorage.setItem('selectedType', selectedValue);
        });

        // Устанавливаем сохраненное значение после загрузки страницы
        var selectedType = sessionStorage.getItem('selectedType');
        if (selectedType) {
            $('select[name="CashFlowTransactionSearch[type]"]').val(selectedType).trigger('change');
        }

        // Обновляем форму при изменении любого поля
        $('#search-form').on('change', 'input, select', function() {
            $('#search-form').submit();
        });
    });
JS
);
?>
