<?php

use app\grid\GridViewClean;
use app\widgets\Card;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PayrollTransactionCategory */
/* @var $dataProvider yii\data\ActiveDataProvider */

\app\assets\EditableFieldsAsset::register($this);
\app\assets\AutoListsAsset::register($this);

$this->title = Yii::$app->urlManager->getLastTitle();

$can_update = Yii::$app->user->can('app_payroll-transaction-categories_edit');
$can_create = Yii::$app->user->can('app_payroll-transaction-categories_create');
$can_delete = Yii::$app->user->can('app_payroll-transaction-categories_delete');

?>

<?php Card::begin([]); ?>

<?= $this->render('_search', ['model' => $searchModel]); ?>

<?php Pjax::begin(['id' => 'pjax']) ?>

<?= GridViewClean::widget([
    'dataProvider' => $dataProvider,
    'showPageSize' => true,
    'createRowModel' => new \app\models\PayrollTransactionCategory(),
    'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
    'filterSelector' => 'select[name="per-page"], .search-block *[name]',
    'tableOptions' => ['class' => 'table align-middle table-check table-bordered mb-0 editable-fields', 'data-base_url' => 'payroll-transaction-categories', 'id' => 'table-payroll-transaction-categories'],
    'actions' => $can_create ? '<span class="btn btn-success btn-sm btn-row-add"><span class="fa fa-plus"></span></span>' : '',
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'name',
            'format' => 'raw',
            'value' => function($model) use ($can_update) {
                return $can_update ? Html::input('text', 'name', $model->name, ['class' => 'form-control auto-list', 'data-list_id' => 1, 'required' => 1, 'autocomplete' => 'off']) : $model->name;
            },
        ],
        [
            'attribute' => 'type',
            'format' => 'raw',
            'value' => function($model) use ($can_update) {
                return $can_update ? Html::input('text', 'type', $model->type, ['class' => 'form-control auto-list', 'data-list_id' => 2, 'required' => 1, 'autocomplete' => 'off']) : $model->type;
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

