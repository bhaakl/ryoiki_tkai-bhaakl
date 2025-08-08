<?php

use app\grid\GridViewClean;
use app\models\MeasureUnit;
use app\models\ProductMeasureUnit;
use app\widgets\Card;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MeasureUnit */
/* @var $dataProvider yii\data\ActiveDataProvider */

$can_update = Yii::$app->user->can('app_products_edit');
$can_create = Yii::$app->user->can('app_products_edit');
$can_delete = Yii::$app->user->can('app_products_edit');

//$availableUnits = MeasureUnit::listAll('id', 'name', filter: ['not in', 'id', \app\helpers\ArrayHelper::getColumn($dataProvider->getModels(), 'measure_unit_id')]);
?>

<div class="modal fade" id="userCardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= $searchModel->product->name.' - единицы измерения' ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <?php Card::begin([]); ?>

                <?php \yii\widgets\Pjax::begin(['id' => 'pjax-product-measure-units', 'options' => ['data-url' => 'product-measure-units/index?product_id='.$searchModel->product_id]]) ?>

                <?= GridViewClean::widget([
                    'dataProvider' => $dataProvider,
                    'createRowModel' => new ProductMeasureUnit(),
                    'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
                    'filterSelector' => 'select[name="per-page"], .search-block *[name]',
                    'tableOptions' => ['class' => 'table align-middle table-check table-bordered mb-0 editable-fields', 'data-base_url' => 'product-measure-units', 'id' => 'table-product-measure-units', 'data-pjax-id' => 'pjax-product-measure-units'],
                    'actions' => $can_create ? '<span class="btn btn-success btn-sm btn-row-add" data-for="table-product-measure-units"><span class="fa fa-plus"></span></span>' : '',
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'attribute' => 'product_id',
                            'headerOptions' => ['hidden' => '1'],
                            'contentOptions' => ['hidden' => '1'],
                            'format' => 'raw',
                            'value' => function($model) use($searchModel) {
                                return Html::hiddenInput('product_id', $searchModel->product_id);
                            },
                        ],
                        [
                            'attribute' => 'measure_unit_id',
                            'format' => 'raw',
                            'value' => function($model) use ($can_update) {
                                return $can_update ? Html::dropDownList('measure_unit_id', $model->measure_unit_id ?? null,
                                    MeasureUnit::listAll('id', 'name'), ['class' => 'form-control form-select', 'prompt' => 'Выберите']) : $model->measureUnit->name ?? 'Нет';
                            },
                        ],
                        [
                            'attribute' => 'description',
                            'format' => 'raw',
                            'value' => function($model) use ($can_update) {
                                return $can_update ? Html::input('text', 'description', $model->description, ['class' => 'form-control']) : $model->description;
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
