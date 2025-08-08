<?php

use app\grid\GridViewClean;
use app\models\search\SummaryTimeSearch;
use app\widgets\Card;
use yii\widgets\Pjax;


/* @var $this yii\web\View */
/* @var $searchModel SummaryTimeSearch */
/* @var $dataProvider yii\data\ArrayDataProvider */


$this->title = Yii::$app->urlManager->getLastTitle();
?>

<?php Card::begin([]); ?>

<?=
$this->render('_search', ['model' => $searchModel]);
?>

<?php

$total_products = [];
$total_days = [];
foreach ($dataProvider->allModels as $product_id => $model) {
    $total_products[$product_id] = array_sum($model);
    foreach ($model as $day => $hours) {
        $total_days[$day] = key_exists($day, $total_days) ? $total_days[$day] + $hours : $hours;
    }
}
ksort($total_days);

$products = \app\models\Product::listAll('id', 'name');

$columns = [
    ['class' => 'yii\grid\SerialColumn'],
    [
        'attribute' => 'product_id',
        'label' => 'Виды работ',
        'footer' => 'Итого:',
        'footerOptions' => ['style' => 'font-weight: bold; '],
        'value' => function ($model, $key) use ($products) {
            return $products[$key];
        }
    ],
    [
        'attribute' => 'count_sum',
        'label' => 'Итого',
        'format' => 'raw',
        'value' => function ($model, $key) use ($total_products) {
            return $total_products[$key] ?? 0;
        },
        'footer' => array_sum($total_products),
        'footerOptions' => ['style' => 'font-weight: bold; '],
    ],
];

foreach ($total_days as $day => $hours) {
    $columns[] = [
        'attribute' => 'day_' . $day,
        'label' => $day,
        'format' => 'raw',
        'value' => function ($model) use ($day) {
            return $model[$day] ?? 0;
        },
        'footer' => $hours,
        'footerOptions' => ['style' => 'font-weight: bold; '],
    ];
}

?>

<?php Pjax::begin(['id' => 'pjax']) ?>

<?= GridViewClean::widget([
    'showFooter' => true,
    'tableOptions' => [
        'class' => 'table align-middle table-check table-striped mb-0',
    ],
    //'rowOptions' => ['class' => 'reward-item'],
    'dataProvider' => $dataProvider,
    'filterSelector' => 'select[name="per-page"], .search-block *[name]',
    'columns' => $columns
]); ?>

<?php Pjax::end();

Card::end();
