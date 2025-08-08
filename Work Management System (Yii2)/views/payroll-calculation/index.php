<?php

use app\grid\GridViewClean;
use app\models\CashFlowTransactionCategory;
use app\models\PayrollCalculation;
use app\models\User;
use app\widgets\Card;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel PayrollCalculation */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $page integer */

$this->title = Yii::$app->urlManager->getLastTitle();

$airdateConfig = '"autoClose": true, "timepicker": false, "selectedDates": [], "minView": "days", "view": "days", "dateFormat": "dd.MM.yyyy", "buttons": ["clear"]';

\app\assets\EditableFieldsAsset::register($this);

$can_update = Yii::$app->user->can('app_payroll-calculation_edit');
$can_create = Yii::$app->user->can('app_payroll-calculation_create');
$can_delete = Yii::$app->user->can('app_payroll-calculation_delete');
?>

<?php Card::begin([]); ?>

<?=
$this->render('_search', ['model' => $searchModel]);
?>

<?php Pjax::begin(['id' => 'pjax']) ?>

<?= GridViewClean::widget([
    'dataProvider' => $dataProvider,
    'showPageSize' => true,
    'createRowModel' => new PayrollCalculation(),
    'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
    'tableOptions' => ['class' => 'table align-middle table-check table-bordered mb-0 editable-fields', 'data-base_url' => 'payroll-calculation', 'id' => 'table-payroll-calculation'],
    'filterSelector' => 'select[name="per-page"], .search-block *[name]',
    'actions' => $can_create ? '<span class="btn btn-success btn-sm btn-row-add" data-base_url="salary-payment-categories"><span class="fa fa-plus"></span></span>' : '',
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'header' => '№ п/п'
        ],
        [
            'attribute' => 'created',
            'format' => 'raw',
            'value' => function ($model) use ($can_update, $airdateConfig) {
                return $can_update ? Html::input('text', 'created',
                    $model->created ? date('d.m.Y', strtotime($model->created)) : date('d.m.Y'), ['class' => 'form-control air-date', 'data-air-config' => $airdateConfig]) : $model->created;
            },
        ],
        [
            'attribute' => 'salary_category_id',
            'format' => 'raw',
            'value' => function ($model) use ($can_update) {
                return $can_update ? Html::dropDownList('salary_category_id', $model->salary_category_id,
                CashFlowTransactionCategory::listAll('id', 'name'), ['class' => 'form-control form-select select2', 'style' => 'width: 100%']) : $model->cashFlowTransactionCategory->name ?? '';
            },
        ],
        [
            'attribute' => 'type',
            'format' => 'raw',
            'label' => 'Тип операции',
            'value' => function($model) {
                return $model->cashFlowTransactionCategory->type->name ?? '';
            },
        ],
        [
            'attribute' => 'account_type_id',
            'format' => 'raw',
            'value' => function ($model) use ($can_update) {
                return $can_update ? Html::dropDownList('account_type_id', $model->account_type_id,
                    PayrollCalculation::getAccountTypes(),
                    ['class' => 'form-control form-select', 'style' => 'width: 100%']) : PayrollCalculation::getAccountTypes()[$model->account_type_id];
            },
        ],
        [
            'attribute' => 'comments',
            'format' => 'raw',
            'value' => function($model) use ($can_update) {
                return $can_update ? Html::input('text', 'comments', $model->comments, ['class' => 'form-control']) : $model->comments ?? '';
            },
        ],
        [
            'attribute' => 'user_id',
            'format' => 'raw',
            'value' => function ($model) use ($can_update) {
                return $can_update ? Html::dropDownList('user_id', $model->user_id,
                    User::listAll('id', 'fio'), ['class' => 'form-control form-select select2', 'style' => 'width: 100%']) : $model->user->fio;
            },
        ],
        [
            'label' => 'Статус',
            'format' => 'raw',
            'value' => function ($model)  {
                return $model->user->state ?? '';
            },
        ],
        [
            'attribute' => 'amount',
            'format' => 'raw',
            'value' => function($model) use ($can_update) {
                return $can_update ? Html::input('text', 'amount', $model->amount, ['class' => 'form-control']) : $model->amount;
            },
        ],
        [
            'class' => 'app\grid\ActionColumn',
            'defaultShowTitle' => false,
            'visible' => $can_delete,
            'buttons' => [
                'delete' => ['icon' => 'fa fa-trash', 'class' => 'btn btn-danger btn-sm', 'title' =>'Удаление', 'confirm' => 'Удалить ?', 'isPost' => true],
            ],
        ],
    ],
]); ?>

<?php Pjax::end();

Card::end();
