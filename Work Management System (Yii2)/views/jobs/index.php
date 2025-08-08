<?php

use app\assets\EditableFieldsAsset;
use app\models\Division;
use kartik\editable\Editable;
use app\grid\GridViewClean;
use app\widgets\Card;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $searchModel app\models\Job */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->urlManager->getLastTitle();

EditableFieldsAsset::register($this);

$can_create = Yii::$app->user->can('app_jobs_create');
$can_update = Yii::$app->user->can('app_jobs_edit');
$can_delete = Yii::$app->user->can('app_jobs_delete');
?>

<?php Card::begin([]); ?>

<?= $this->render('_search', ['model' => $searchModel]); ?>

<?= GridViewClean::widget([
    'dataProvider' => $dataProvider,
    'showPageSize' => true,
    'createRowModel' => new \app\models\Job(),
    'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
    'tableOptions' => ['class' => 'table align-middle table-check table-bordered mb-0 editable-fields', 'data-base_url' => 'jobs', 'id' => 'table-jobs'],
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
            'attribute' => 'division_id',
            'format' => 'raw',
            'value' => function($model) use ($can_update) {
                return $can_update ? Html::dropDownList('division_id', $model->division_id,
                    Division::listAll('id', 'name'), ['class' => 'form-control form-select', 'style' => 'width: 100%']) : $model->division->name;
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
            ],
        ],
    ],
]);
?>

<hr>

<?php

Card::end();

?>