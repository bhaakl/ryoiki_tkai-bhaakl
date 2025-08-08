<?php

use app\models\WorkerReportCard;
use app\grid\GridView;
use app\widgets\Card;

/* @var $this yii\web\View */
/* @var $searchModel \app\models\Product */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $daysArray array */
/* @var $productList array */
/* @var $unitList array */
/* @var $monthYear string */

\app\assets\EditableTableAsset::register($this);

$this->title = Yii::$app->urlManager->getLastTitle();

$can_update = Yii::$app->user->can('app_salary-payment-categories_edit');
$can_create = Yii::$app->user->can('app_salary-payment-categories_create');
$can_delete = Yii::$app->user->can('app_salary-payment-categories_delete');

?>

<?php Card::begin([]); ?>

<?=
$this->render('_search', [
    'model' => $searchModel,
    'productList' => $productList,
    'unitList' => $unitList,
    'monthYear' => $monthYear
]);
?>

<?php \yii\widgets\Pjax::begin(['id' => 'pjax','timeout' => 0]) ?>
<?php
$columns = [
    ['class' => 'yii\grid\SerialColumn'],
    [
        'attribute' => 'name',
        'label' => 'Виды работ',

    ],
    [
        'attribute' => 'unit',
        'label' => 'ЕИ',
    ],
    [
        'attribute' => 'count_sum',
        'label' => 'Итого',
    ],
];
foreach ($daysArray as $day) {
    $dayNumber = date("d", strtotime($day));
    $columns[] = [
        'attribute' => 'day_' . $dayNumber,
        'label' => $dayNumber,
    ];
}
?>

<?= GridView::widget([
    'showFooter' => false,
    'tableOptions' => [
        'class' => 'table align-middle table-check table-striped mb-0 editable-fields',
        'id' => 'editable-table',
        'data-module-name' => 'worker-report-card',
        'data-module-select-attributes' => '',
        'data-module-input-attributes' => '',
    ],
    'dataProvider' => $dataProvider,
    'showPageSummary' => true,
    'filterSelector' => 'select[name="per-page"], .search-block *[name]',
    'showPageSize' => true,
    'columns' => $columns
]); ?>

<?php \yii\widgets\Pjax::end();

Card::end();
