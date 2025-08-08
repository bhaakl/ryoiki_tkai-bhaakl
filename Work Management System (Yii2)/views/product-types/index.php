<?php

use app\grid\GridViewClean;
use app\models\ProductType;
use app\widgets\Card;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductType */
/* @var $dataProvider yii\data\ActiveDataProvider */

\app\assets\EditableFieldsAsset::register($this);

$can_update = Yii::$app->user->can('app_product-types_edit');
$can_create = Yii::$app->user->can('app_product-types_create');
$can_delete = Yii::$app->user->can('app_product-types_delete');

$this->title = Yii::$app->urlManager->getLastTitle();
?>

<?php Card::begin([]); ?>

<?php \yii\widgets\Pjax::begin(['id' => 'pjax']) ?>

<?= GridViewClean::widget([
    'dataProvider' => $dataProvider,
    'showPageSize' => true,
    'createRowModel' => new ProductType(),
    'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
    'filterSelector' => 'select[name="per-page"], .search-block *[name]',
    'tableOptions' => ['class' => 'table align-middle table-check table-bordered mb-0 editable-fields', 'data-base_url' => 'product-types', 'id' => 'table-product-types'],
    'actions' => $can_create ? '<span class="btn btn-success btn-sm btn-row-add"><span class="fa fa-plus"></span></span>' : '',
    'columns' => [
        [
            'attribute' => 'name',
            'format' => 'raw',
            'value' => function($model) use ($can_update) {
                return $can_update ? Html::input('text', 'name', $model->name, ['class' => 'form-control', 'required' => 1]) : $model->name;
            },
        ],
		[
            'attribute' => 'short_name',
            'format' => 'raw',
            'value' => function($model) use ($can_update) {
                return $can_update ? Html::input('text', 'short_name', $model->short_name, ['class' => 'form-control', 'required' => 1]) : $model->short_name;
            },
        ],
        [
            'attribute' => 'has_production_stages',
            'format' => 'raw',
            'value' => function($model) use ($can_update) {
                return Html::checkbox('has_production_stages', $model->has_production_stages, $can_update ? [] : ['disabled' => 1]);
            },
        ],
		[
            'attribute' => 'comments',
            'format' => 'raw',
            'value' => function($model) use ($can_update) {
                return $can_update ? Html::input('text', 'comments', $model->comments, ['class' => 'form-control']) : $model->comments;
            },
        ],
        [
            'class' => 'app\grid\ActionColumn',
            'defaultShowTitle' => false,
            'visible' => $can_delete,
            'buttons' => [
                'delete' => ['icon' => 'fa fa-trash', 'class' => 'btn btn-danger btn-sm btn-row-remove', 'title' => 'Удаление'],
            ]
        ],
    ],
]);
?>

<?php \yii\widgets\Pjax::end() ?>

<hr>

<?php

Card::end();

?>