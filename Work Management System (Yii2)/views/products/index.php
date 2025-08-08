<?php

use app\grid\GridViewClean;
use app\helpers\ArrayHelper;
use app\models\Product;
use app\widgets\Card;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $data array */

$this->title = Yii::$app->urlManager->getLastTitle();

\app\assets\EditableFieldsAsset::register($this);

$can_update = Yii::$app->user->can('app_products_edit');
$can_create = Yii::$app->user->can('app_products_create');
$can_delete = Yii::$app->user->can('app_products_delete');
?>

<?php Card::begin([]); ?>

<?php
echo $this->render('_search', [
    'model' => $searchModel,
]);

$columns =  [
    'id',
    [
        'attribute' => 'productType.name',
        'label' => 'Тип',
        'value' => 'category.type.name',
    ],
    [
        'attribute' => 'category_id',
        'format' => 'raw',
        'value' => function($model) use ($can_update) {
            return $can_update ? \yii\helpers\Html::dropDownList('category_id', $model->category_id,
                \app\models\ProductCategory::listAll('id', 'name'),
                ['class' => 'form-control form-select', 'style' => 'width: 100%']) : $model->category->name ?? '';
        },
    ],
    [
        'attribute' => 'name',
        'format' => 'raw',
        'value' => function($model) use ($can_update) {
            return $can_update ? Html::input('text', 'name', $model->name, ['class' => 'form-control', 'required' => 1]) : $model->name;
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
        'label' => 'ЕИ',
        'format' => 'raw',
        'contentOptions' => $can_update ? ['class' => 'measure-units'] : [],
        'value' => function($model) use ($can_update) {
            return implode(', ', ArrayHelper::getColumn($model->productMeasureUnits, 'measureUnit.short_name'));
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
];

for($i = 0; $i <= 6; $i++) {
    $columns[] = [
        'attribute' => 'productStages',
        'label' => 'Участок ' . ($i + 1),
        'format' => 'raw',
        'value' => function($model) use ($can_update, $i) {
            return $can_update ? \yii\helpers\Html::dropDownList("productionStages[$i]", $model->productionStages[$i]->id ?? 0,
                [0 => '---'] + \app\models\ProductionStage::listAll('id', 'name'),
                ['class' => 'form-control', 'style' => 'width: 100%']) : $model->productionStages[$i]->name ?? '';
        },
    ];
}

?>

<?php \yii\widgets\Pjax::begin(['id' => 'pjax']) ?>

<?= GridViewClean::widget([
    'dataProvider' => $dataProvider,
    'showPageSize' => true,
    'createRowModel' => new Product(),
    'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
    'filterSelector' => 'select[name="per-page"], .search-block *[name]',
    'tableOptions' => ['class' => 'table align-middle table-check table-bordered mb-0 editable-fields', 'data-base_url' => 'products', 'id' => 'table-products'],
    'actions' => $can_create ? '<span class="btn btn-success btn-sm btn-row-add"><span class="fa fa-plus"></span></span>' : '',
    'columns' => $columns,
]);

\yii\widgets\Pjax::end();

Card::end();

?>

<div id="modalContainer"></div>

<script>
    window.onload = function() {
        $(document).on("pjax:complete", "#pjax-product-measure-units", function () {
            initEditableEvents('#table-product-measure-units');
        });
        document.addEventListener('click', (e) => {
            let viewSender = e.target.closest('.measure-units');
            if(viewSender) {
                event.preventDefault();
                let tr = viewSender.closest('tr');
                let url = 'product-measure-units/index?product_id='+tr.dataset.key;
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(data) {
                        let modalContainer = document.getElementById('modalContainer');
                        modalContainer.innerHTML = data;
                        initEditableEvents('#table-product-measure-units');
                        (new bootstrap.Modal(modalContainer.firstElementChild)).show();
                    }
                });
            }
        })
    }
</script>
