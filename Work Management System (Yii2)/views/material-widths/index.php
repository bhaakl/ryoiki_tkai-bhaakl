<?php

use app\assets\EditableFieldsAsset;
use app\grid\GridViewClean;
use app\models\MaterialWidth;
use kartik\editable\Editable;
use app\grid\GridView;
use app\widgets\Card;
use yii\helpers\Html;
use app\widgets\MultipleInput;
use app\widgets\ActiveForm;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MaterialWidth */
/* @var $dataProvider yii\data\ActiveDataProvider */

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
    'createRowModel' => new MaterialWidth(),
    'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
    'tableOptions' => ['class' => 'table align-middle table-check table-hover mb-0 editable-fields', 'data-base_url' => $this->context->id, 'id' => 'table-'.$this->context->id],
    'filterSelector' => 'select[name="per-page"], .search-block *[name]',
    'actions' => $can_create ? '<span class="btn btn-success btn-sm btn-row-add" data-base_url="salary-payment-categories"><span class="fa fa-plus"></span></span>' : '',
    'columns' => [
        [
            'attribute' => 'id',
            'format' => 'raw',
            'value' => function($model) use ($can_update) {
                $attribute = 'id';
                return $can_update ? Html::input('number', $attribute, $model->$attribute, ['class' => 'form-control', 'required' => 1]) : $model->$attribute;
            },
        ],
        [
            'attribute' => 'corrected_value',
            'format' => 'raw',
            'value' => function($model) use ($can_update) {
                $attribute = 'corrected_value';
                return $can_update ? Html::input('number', $attribute, $model->$attribute, ['class' => 'form-control', 'required' => 1]) : $model->$attribute;
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