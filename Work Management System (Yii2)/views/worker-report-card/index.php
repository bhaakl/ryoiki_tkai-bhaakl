<?php

use app\grid\GridView;
use app\grid\GridViewClean;
use app\models\search\WorkerReportCardSearch;
use app\widgets\Card;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel WorkerReportCardSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->urlManager->getLastTitle();

\app\assets\EditableFieldsAsset::register($this);

$can_update = Yii::$app->user->can('app_worker-report-card_edit');
$can_create = Yii::$app->user->can('app_worker-report-card_create');
$can_delete = Yii::$app->user->can('app_worker-report-card_delete');

$measureUnits = \app\models\MeasureUnit::listAll('id', 'short_name');
?>

<style>
    hr {
        margin: 0;
        border: 0;
        border-top: 1px solid #c6c6c6;
    }

    .vertical {
        width:2%;
        writing-mode: sideways-lr;
        padding: 12px 5px;
    }

    .mini-input {
        padding: 0;
        text-align: center;
    }

    input:invalid {
        border-radius: 0;
        background-color: lightpink;
    }
</style>

<?php Card::begin([]); ?>

<?=
$this->render('_search', ['model' => $searchModel]);
?>

<?php \yii\widgets\Pjax::begin(['id' => 'pjax','timeout' => 0]) ?>
<?php
$columns = [
    [
        'class' => 'yii\grid\SerialColumn',
        'footerOptions' => [
            'style' => 'border-left: 1px solid #c6c6c6;',
        ],
    ],
    [
        'attribute' => 'product.name',
        'label' => 'Виды работ',
        'footer' => 'Итого'
    ],
    'totalHours',
    [
        'attribute' => 'totalMade',
        'format' => 'raw',
        'value' => function($model) use ($measureUnits) {
            $result = [];
            foreach ($model->totalMade as $measure_unit_id => $count) {
                $result[] = $count . ' ' . $measureUnits[$measure_unit_id] . '.';
            }
            return implode('<hr>', $result);
        }
    ],
    [
        'attribute' => 'madeInMinute',
        'format' => 'raw',
        'value' => function($model) {
            return implode('<hr>', $model->madeInMinute);
        }
    ],
];

foreach ($searchModel->getAllWorkers() as $user) {
    $columns[] = [
        'attribute' => 'userHours',
        'label' => $user->fio,
        'headerOptions' => ['class' => 'vertical'],
        'format' => 'raw',
        'value' => function ($model) use ($user, $searchModel) {
            $worker = $model->getWorkerByUserId($user->id);
            return $worker ? Html::input('text', "worker_hours[$worker->id][$searchModel->type]",
                $worker->{$searchModel->hoursAttribute},
                [
                    'class' => 'form-control mini-input',
                    'inputmode' => 'numeric',
                    'pattern' => '\d*'
                ]) : '-';
        },
    ];
    $columns[] = [
        'attribute' => 'userUnits',
        'label' => 'к-во сделанного',
        'headerOptions' => ['class' => 'vertical'],
        'format' => 'raw',
        'value' => function ($model) use ($user, $searchModel) {
            $worker = $model->getWorkerByUserId($user->id);
            if($worker) {
                $result = [];
                foreach ($worker->units as $unit) {
                    $result[] = Html::input('text', "worker_units[$worker->id][{$unit->id}][$searchModel->type]",
                        $unit->{$searchModel->valueAttribute},
                        [
                            'class' => 'form-control mini-input',
                            'inputmode' => 'numeric',
                            'pattern' => '\d*'
                        ]);
                }
                return implode('<hr>', $result);
            } else {
                return '-';
            }
        },
    ];
}
?>

<?= GridViewClean::widget([
    'tableOptions' => ['class' => 'table align-middle table-check table-striped mb-0 editable-fields',
        'data-base_url' => 'worker-report-card',
        'data-direct' => 'true',
        'id' => 'table-worker-report-card'
    ],
    'showFooter' => true,
    'footerRowOptions' => [
        'class' => 'table-light',
        'style' => 'font-weight: bold;',
    ],
    'dataProvider' => $dataProvider,
    // 'rowOptions' => function ($model) {
    //     return $model['production_stage'] === 'Всего часов' ? ['class' => 'bg-warning'] : [];
    // },
    'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
    'filterSelector' => 'select[name="per-page"], .search-block *[name]',
    'columns' => $columns]); ?>

<?php \yii\widgets\Pjax::end();

Card::end();
