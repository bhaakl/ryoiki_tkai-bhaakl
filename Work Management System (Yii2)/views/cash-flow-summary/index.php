<?php

use app\widgets\Card;
use yii\widgets\Pjax;
use app\grid\GridViewClean;

/* @var $this yii\web\View */
/* @var $model app\models\CashFlowSummary */
/* @var $searchModel app\models\SearchModel */
/* @var $months array */

$this->title = Yii::$app->urlManager->getLastTitle();

$months = [
    1 => 'Январь', 2 => 'Февраль', 3 => 'Март', 4 => 'Апрель',
    5 => 'Май', 6 => 'Июнь', 7 => 'Июль', 8 => 'Август',
    9 => 'Сентябрь', 10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь'
];
?>

<?php Card::begin(); ?>

<div class="cash-flow-summary-index">

    <?php Pjax::begin(['id' => 'cash-flow-summary']) ?>

    <div style="width: 10vw;">
        <?= $this->render('_search', ['model' => $searchModel]); ?>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <?= GridViewClean::widget([
                'dataProvider' => $dataProvider,
                'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
                'filterSelector' => 'select[name="per-page"], .search-block *[name]',
                'tableOptions' => [
                    'class' => 'table table-bordered'
                ],
                'columns' => [
                    [
                        'attribute' => 'rowNumber',
                        'header' => '№ ПП',
                        'headerOptions' => ['style' => 'width: 5%; position: sticky; top: 0; background: #f0f0f0; z-index: 10;'],
                        'value' => function ($data) {
                            return isset($data['isCategory']) ? Yii::$app->formatter->asInteger($data['rowNumber']) : '';
                        },
                        'contentOptions' => function ($data) {
                            return isset($data['isHeader']) || isset($data['isDivider']) ? ['style' => 'display: none;'] : [];
                        },
                    ],
                    [
                        'attribute' => 'label',
                        'header' => 'Категория ДДС',
                        'headerOptions' => ['style' => 'width: 20%; position: sticky; top: 0; background: #f0f0f0; z-index: 10;'],
                        'value' => function ($data) {
                            return isset($data['isHeader']) ? $data['label'] : (isset($data['category']) ? $data['category'] : '');
                        },
                        'contentOptions' => function ($data) {
                            return isset($data['isHeader']) ? ['colspan' => 2] : [];
                        },
                    ],
                    [
                        'attribute' => 'total',
                        'header' => 'Итого',
                        'headerOptions' => ['style' => 'width: 10%; position: sticky; top: 0; background: #f0f0f0; z-index: 10;'],
                        'value' => function ($data) {
                            return isset($data['total']) ? Yii::$app->formatter->asDecimal($data['total'], 2) : '';
                        },
                    ],
                    ...array_map(function ($monthNumber, $monthName) {
                        return [
                            'attribute' => 'months.' . $monthNumber,
                            'header' => $monthName,
                            'headerOptions' => ['style' => 'width: 7.5%; position: sticky; top: 0; background: #f0f0f0; z-index: 10;'],
                            'value' => function ($data) use ($monthNumber) {
                                return isset($data['months'][$monthNumber]) ? Yii::$app->formatter->asDecimal($data['months'][$monthNumber], 2) : '';
                            },
                        ];
                    }, array_keys($months), $months),
                ],
                'summary' => false,
                'rowOptions' => function ($model, $key, $index, $grid) {
                    if (isset($model['isHeader'])) {
                        return ['class' => 'header-row'];
                    } elseif (isset($model['isDivider'])) {
                        return ['class' => 'divider-row', 'style' => 'border-top: 2px solid #000;'];
                    } elseif (isset($model['isSaldo'])) {
                        return ['class' => 'saldo-row'];
                    }
                    return [];
                },
            ]); ?>
        </div>
    </div>

    <?php Pjax::end() ?>
</div>

<?php
Card::end();
?>

<style>
    .header-row {
        background-color: #f0f0f0; /* Светлый серый фон */
        font-weight: bold; /* Жирный шрифт */
        text-align: center; /* Центрирование текста */
        font-size: 14px; /* Размер шрифта */
    }

    .divider-row {
        border-bottom: 2px solid #000; /* Граница снизу */
    }

    .saldo-row {
        background-color: #e0e0e0; /* Светлый серый фон для сальдо */
        font-weight: bold; /* Жирный шрифт */
        font-size: 14px; /* Размер шрифта */
    }

    table tbody tr td {
        font-size: 12px; /* Размер шрифта для обычных строк */
    }
</style>
