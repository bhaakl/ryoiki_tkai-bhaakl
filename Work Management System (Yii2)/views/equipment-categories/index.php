<?php

use kartik\editable\Editable;
use app\grid\GridView;
use app\widgets\Card;
use yii\helpers\Html;
use app\widgets\MultipleInput;
use app\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EquipmentCategory */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->urlManager->getLastTitle();

\app\assets\EditableFieldsAsset::register($this);

$can_create = Yii::$app->user->can('app_equipment-categories_create');
$can_update = Yii::$app->user->can('app_equipment-categories_edit');
$can_delete = Yii::$app->user->can('app_equipment-categories_delete');
?>

<?php Card::begin([]); ?>

<?= $this->render('_search', ['model' => $searchModel]); ?>

<?php \yii\widgets\Pjax::begin(['id' => 'pjax']) ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'showPageSize' => true,
    'createRowModel' => new \app\models\EquipmentCategory(),
    'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
    'tableOptions' => ['class' => 'table align-middle table-check table-bordered mb-0 editable-fields', 'data-base_url' => 'equipment-categories', 'id' => 'table-equipment-categories'],
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
            'attribute' => 'comments',
            'format' => 'raw',
            'value' => function($model) use ($can_update) {
                return $can_update ? Html::input('text', 'comments', $model->comments, ['class' => 'form-control', 'required' => 1]) : $model->comments;
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