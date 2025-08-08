<?php

use app\assets\EditableFieldsAsset;
use app\models\Status;
use app\models\StatusScope;
use app\grid\GridViewClean;
use app\widgets\Card;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Status */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->urlManager->getLastTitle();

EditableFieldsAsset::register($this);

$can_create = Yii::$app->user->can('app_statuses_create');
$can_update = Yii::$app->user->can('app_statuses_edit');
$can_delete = Yii::$app->user->can('app_statuses_delete');

$columns = [
    [
        'attribute' => 'name',
        'format' => 'raw',
        'value' => function($model) use ($can_update) {
            return $can_update ? Html::input('text', 'name', $model->name, ['class' => 'form-control', 'required' => 1]) : $model->name;
        },
    ],
    [
        'attribute' => 'scope_id',
        'format' => 'raw',
        'value' => function($model) use ($can_update) {
            return $can_update ? Html::dropDownList('scope_id', $model->scope_id,
                StatusScope::listAll('id', 'name'), ['class' => 'form-control form-select', 'style' => 'width: 100%']) : $model->scope->name ?? '';
        },
    ],
    [
        'attribute' => 'bg_color',
        'format' => 'raw',
        'value' => function($model) use ($can_update) {
            return Html::input('color', 'bg_color', $model->bg_color, ['class' => 'form-control form-control-color', 'required' => 1] + ($can_update ? [] : ['disabled' => 1]));
        },
    ],
];

$pagesSource = is_array($searchModel->pages) && !empty($searchModel->pages) ? $searchModel->pages : array_keys(Status::PAGE_NAMES);
foreach ($pagesSource as $pageAttribute) {
    $columns[] =
        [
            'attribute' => $pageAttribute,
            'format' => 'raw',
            'value' => function($model) use ($can_update, $pageAttribute) {
                return Html::checkbox($pageAttribute, $model->$pageAttribute, $can_update ? [] : ['disabled' => 1]);
            },
        ];
}

$columns[] = [
    'class' => 'app\grid\ActionColumn',
    'defaultShowTitle' => false,
    'visible' => $can_delete,
    'buttons' => [
        'delete' => ['icon' => 'fa fa-trash', 'class' => 'btn btn-danger btn-sm btn-row-remove', 'title' => 'Удаление'],
    ],
];

?>

<?php Card::begin([]); ?>

<?= $this->render('_search', ['model' => $searchModel]); ?>

<?php \yii\widgets\Pjax::begin(['id' => 'pjax']) ?>

<?= GridViewClean::widget([
    'dataProvider' => $dataProvider,
    'showPageSize' => true,
    'createRowModel' => new Status(),
    'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
    'tableOptions' => ['class' => 'table align-middle table-check table-bordered mb-0 editable-fields', 'data-base_url' => 'statuses', 'id' => 'table-statuses'],
    'filterSelector' => 'select[name="per-page"], .search-block *[name]',
    'actions' => $can_create ? '<span class="btn btn-success btn-sm btn-row-add" data-base_url="salary-payment-categories"><span class="fa fa-plus"></span></span>' : '',
    'columns' => $columns
]);
?>

<?php \yii\widgets\Pjax::end() ?>

<hr>

<?php Card::end(); ?>