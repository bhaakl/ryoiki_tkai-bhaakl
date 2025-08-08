<?php

use app\assets\EditableFieldsAsset;
use app\grid\GridViewClean;
use app\models\DeliveryType;
use app\widgets\Card;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DeliveryType */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $formModel \app\models\forms\DeliveryTypesForm */

$this->title = Yii::$app->urlManager->getLastTitle();

EditableFieldsAsset::register($this);

$can_create = Yii::$app->user->can('app_'.$this->context->id.'_create');
$can_update = Yii::$app->user->can('app_'.$this->context->id.'_edit');
$can_delete = Yii::$app->user->can('app_'.$this->context->id.'_delete');
?>

<?php Card::begin([]); ?>

<?= $this->render('_search', ['model' => $searchModel]); ?>

<?php Pjax::begin(['id' => 'pjax']) ?>

<?= GridViewClean::widget([
    'dataProvider' => $dataProvider,
    'showPageSize' => true,
    'createRowModel' => new DeliveryType(),
    'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
    'tableOptions' => ['class' => 'table align-middle table-check table-hover mb-0 editable-fields', 'data-base_url' => $this->context->id, 'id' => 'table-'.$this->context->id],
    'filterSelector' => 'select[name="per-page"], .search-block *[name]',
    'actions' => $can_create ? '<span class="btn btn-success btn-sm btn-row-add" data-base_url="salary-payment-categories"><span class="fa fa-plus"></span></span>' : '',
    'columns' => [
        [
            'attribute' => 'name',
            'format' => 'raw',
            'value' => function($model) use ($can_update) {
                $attribute = 'name';
                return $can_update ? Html::input('text', $attribute, $model->$attribute, ['class' => 'form-control', 'required' => 1]) : $model->$attribute;
            },
        ],
        [
            'attribute' => 'comments',
            'format' => 'raw',
            'value' => function($model) use ($can_update) {
                $attribute = 'comments';
                return $can_update ? Html::input('text', $attribute, $model->$attribute, ['class' => 'form-control', 'required' => 1]) : $model->$attribute;
            },
        ],
        [
            'class' => 'app\grid\ActionColumn',
            'defaultShowTitle' => false,
            'buttons' => [
                'delete' => ['icon' => 'fa fa-trash', 'class' => 'btn btn-danger btn-sm btn-row-remove', 'title' => 'Удалить', 'visible' => $can_delete],
            ],
        ],
    ],
]);
?>

<?php Pjax::end() ?>

<hr>

<?php

Card::end();

?>