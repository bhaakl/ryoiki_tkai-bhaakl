<?php

use app\helpers\Date;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

?>

<div class="modal fade" id="userCardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= $model->fullname ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="btn-toolbar" role="toolbar">
                    <div class="btn-group" role="group">
                        <a class="btn btn-outline-primary">Ведомость</a>
                        <a class="btn btn-outline-primary">Начисления</a>
                        <a class="btn btn-outline-primary">Выплаты</a>
                        <a class="btn btn-outline-primary">Табель, график план/факт</a>
                        <a class="btn btn-outline-primary">Выработка по видам продукции и работ</a>
                        <a href="/tasks/index?TaskSearch%5Bperformer_id%5D=<?= $model->id ?>" target="_blank" class="btn btn-outline-primary">Задачи</a>
                    </div>
                </div>
                <?php
                echo DetailView::widget([
                    'model' => $model,
                    'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
                    'attributes' => [
                        'lastname',
                        'firstname',
                        'patronym',
                        [
                            'label' => 'Подразделение',
                            'value' => $model->job->division->name ?? '',
                        ],
                        [
                            'label' => 'Должность',
                            'value' => $model->job->name ?? '',
                        ],
                        [
                            'label' => 'Руководитель',
                            'value' => $model->superior->fio ?? '',
                        ],
                        'salary',
                        'hire_date:date',
                        'termination_date:date',
                        [
                            'label' => 'Стаж',
                            'value' => Date::monthsDaysDiffNow($model->hire_date)
                        ],
                        'phone',
                        'email',
                        'state',
                        [
                            'label' => 'Аккаунт',
                            'value' => ['Нет', 'Да'][(int)$model->have_account]
                        ],
                        [
                            'label' => 'Роль',
                            'visible' => $model->have_account,
                            'value' => $model->role->description ?? 'Нет'
                        ],
                    ],
                ]);
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

