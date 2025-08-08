<?php

use app\grid\GridViewClean;
use app\models\ProductCategory;
use app\models\ProductType;
use app\widgets\Card;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductCategory */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->urlManager->getLastTitle();

\app\assets\EditableFieldsAsset::register($this);

$can_create = Yii::$app->user->can('app_product-categories_create');
$can_update = Yii::$app->user->can('app_product-categories_edit');
$can_delete = Yii::$app->user->can('app_product-categories_delete');

$weightFormulaTypes = [
    ProductCategory::WEIGHT_FORMULA_TYPE_NONE => 'Нет',
    ProductCategory::WEIGHT_FORMULA_TYPE_FLAT => 'Для плоских изделий',
    ProductCategory::WEIGHT_FORMULA_TYPE_SANDWICH => 'Для СП',
];
?>

<?php Card::begin([]); ?>

<?= $this->render('_search', ['model' => $searchModel]); ?>

<?php \yii\widgets\Pjax::begin(['id' => 'pjax']) ?>

<?= GridViewClean::widget([
    'dataProvider' => $dataProvider,
    'showPageSize' => true,
    'createRowModel' => new \app\models\ProductCategory(),
    'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
    'tableOptions' => ['class' => 'table align-middle table-check table-bordered mb-0 editable-fields', 'data-base_url' => 'product-categories', 'id' => 'table-product-categories'],
    'filterSelector' => 'select[name="per-page"], .search-block *[name]',
    'actions' => $can_create ? '<span class="btn btn-success btn-sm btn-row-add" data-base_url="salary-payment-categories"><span class="fa fa-plus"></span></span>' : '',
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
            'attribute' => 'type_id',
            'format' => 'raw',
            'value' => function($model) use ($can_update, $weightFormulaTypes) {
                return $can_update ? Html::dropDownList('type_id', $model->type_id,
                    ProductType::listAll('id', 'name'), ['class' => 'form-control form-select', 'style' => 'width: 100%']) : $model->type->name ?? '';
            },
        ],
		[
            'attribute' => 'weight_formula_type',
            'format' => 'raw',
            'value' => function($model) use ($can_update, $weightFormulaTypes) {
                return $can_update ? Html::dropDownList('weight_formula_type', $model->weight_formula_type,
                    $weightFormulaTypes, ['class' => 'form-control form-select', 'style' => 'width: 100%']) : $weightFormulaTypes[$model->weight_formula_type] ?? '';
            },

        ],
        [
            'attribute' => 'comments',
            'format' => 'raw',
            'value' => function($model) use ($can_update) {
                $attribute = 'comments';
                return $can_update ? Html::input('text', $attribute, $model->$attribute, ['class' => 'form-control']) : $model->$attribute;
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