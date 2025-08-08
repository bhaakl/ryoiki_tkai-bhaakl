<?php

use app\assets\EditableFieldsAsset;
use app\grid\GridViewClean;
use app\widgets\Card;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductionStage */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->urlManager->getLastTitle();

EditableFieldsAsset::register($this);

$can_update = Yii::$app->user->can('app_'.$this->context->id.'_edit');
?>

<?php Card::begin([]); ?>

<?php Pjax::begin(['id' => 'pjax']) ?>

<?= GridViewClean::widget([
    'dataProvider' => $dataProvider,
    'tableOptions' => ['class' => 'table align-middle table-check table-bordered mb-0 editable-fields', 'data-base_url' => $this->context->id, 'id' => 'table-'.$this->context->id],
    'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
    'id' => 'table0',
    'columns' => [
        [
            'attribute' => 'id',
        ],
        [
            'attribute' => 'name',
        ],
        [
            'attribute' => 'short_name',
            'format' => 'raw',
            'value' => function($model) use ($can_update) {
                $attribute = 'short_name';
                return $can_update ? Html::input('text', $attribute, $model->$attribute, ['class' => 'form-control', 'required' => 1]) : $model->$attribute;
            },
        ],
    ],
]);

Pjax::end();

Card::end();

?>
