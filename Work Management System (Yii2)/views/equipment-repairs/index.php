<?php

use app\widgets\ActionButtons;
use app\grid\GridView;
use app\widgets\Card;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\EquipmentRepairSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $formModel \app\models\forms\EquipmentRepairsForm */
/* @var $data array */

$this->title = Yii::$app->urlManager->getLastTitle();
$actionEditPartial = ['equipment-repairs/edit-partial'];
?>

<?php Card::begin([]); ?>

<?php
    echo $this->render('_search', [
        'model' => $searchModel,
        'data' => [
            'categories' => $data['categories'],
            'equipmentUnits' => $data['equipmentUnits'],
            'jobs' => $data['jobs'],
            'divisions' => $data['divisions'],
            'statuses' => $data['statuses'],
            'users' => $data['users'],
        ]
    ]);
?>

<?= GridView::widget([
    'id' => 'equipment-repair-grid',
    'actions' => Yii::$app->user->can('app_equipment-repairs_create') ?
        ActionButtons::widget(['defaultShowTitle' => false, 'defaultAccess' => '$', 'items' => [
            ['name' => 'create', 'options' => ['class' => 'btn btn-success btn-sm'], 'title' => 'Новая запись', 'iconClass' => 'fa fa-plus', 'model' => $searchModel],
        ]]) : '',
    'dataProvider' => $dataProvider,
    'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
    'containerOptions' => ['class' => 'table-with-search-container'],
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'created',
            'label' => 'Дата добавления записи',
            'format' => ['date', 'dd.MM.yyyy'],
        ],
        [
            'attribute' => 'equipment_unit_id',
            'label' => 'Наименование оборудования',
            'value' => 'equipmentUnit.name',
        ],
        [
            'attribute' => 'defect',
        ],
        [
            'attribute' => 'when_broken',
            'label' => 'Дата поломки',
            'format' => ['date', 'dd.MM.yyyy'],
        ],
        [
            'attribute' => 'reason',
        ],
        [
            'label' => 'Кто устранил',
            'format' => 'raw',
            'value' => function ($data) {
                $result = [];
                foreach ($data->repairers as $item) {
                    if ($item->is_deleted == 0) {
                        $result[] = Html::a($item->fio, ['people/view', 'id' => $item->id], ['target' => '_blank']);
                    }
                }
                return implode(', ', $result);
            },
        ],
        [
            'attribute' => 'when_repaired',
            'label' => 'Дата устранения',
            'format' => ['date', 'dd.MM.yyyy'],
        ],
        [
            'attribute' => 'downtime',
            'label' => 'Общее время простоя, ч',
            'format' => 'text',
        ],
        [
            'attribute' => 'repair_cost',
            'label' => 'Стоимость ремонта',
            'format' => 'text',
        ],
        [
            'attribute' => 'comments',
        ],
        [
            'attribute' => 'status_id',
            'label' => 'Статус',
            'format' => 'text',
            'value' => function ($model) use ($data) {
                return $data['statuses'][$model->status_id] ?? '';
            }
        ],
        [
            'class' => 'app\grid\ActionColumn',
            'defaultShowTitle' => false,
            'visible' => Yii::$app->user->can('app_equipment-repairs_edit') || Yii::$app->user->can('app_equipment-repairs_delete'),
            'buttons' => [
                'edit' => function ($url, $id) {
                    return Html::a('<span class="fa fa-edit" title="Редактировать"></span>', ['equipment-repairs/edit','id' => $id], [
                        'data-pjax' => 0,
                        'class' => 'btn btn-primary btn-sm edit-button',
                    ]);
                },
                'delete' => ['icon' => 'fa fa-trash', 'class' => 'btn btn-danger btn-sm', 'title' =>'Удаление', 'confirm' => 'Удалить?', 'isPost' => true],
            ],
        ],
    ],
]);

Card::end();

?>