<?php

use app\assets\EditableFieldsAsset;
use app\grid\GridViewClean;
use app\helpers\Date;
use app\models\Division;
use app\models\Job;
use app\models\User;
use app\widgets\Card;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\User */
/* @var $dataProvider yii\data\ActiveDataProvider */

$airdateConfig = '"autoClose": true, "timepicker": false, "selectedDates": [], "minView": "days", "view": "days", "dateFormat": "dd.MM.yyyy", "buttons": ["clear"]';

$this->title = Yii::$app->urlManager->getLastTitle();
$this->registerJs( "$(document).ready(function() { $('.dropdown-toggle').dropdown(); });", View::POS_END, 'ecommerceProductJs' );

EditableFieldsAsset::register($this);

$can_create = Yii::$app->user->can('app_people_create');
$can_update = Yii::$app->user->can('app_people_edit');
$can_delete = Yii::$app->user->can('app_people_delete');

?>

<?php Card::begin([]); ?>

<?= $this->render('_search', ['model' => $searchModel]); ?>

<?php Pjax::begin(['id' => 'pjax']) ?>

<?= GridViewClean::widget([
    'id' => 'people-grid',
	'showPageSize' => true,
	'createRowModel' => new User(),
	'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
	'tableOptions' => ['class' => 'table align-middle table-check table-hover mb-0 editable-fields', 'data-base_url' => 'people', 'id' => 'table-people'],
	'filterSelector' => 'select[name="per-page"], .search-block *[name]',
	'actions' => $can_create ? '<span class="btn btn-success btn-sm btn-row-add" data-base_url="salary-payment-categories"><span class="fa fa-plus"></span></span>' : '',
    'dataProvider' => $dataProvider,
    'columns' => [
        ['attribute' => 'id'],
		[
			'attribute' => 'lastname',
			'format' => 'raw',
			'value' => function($model) use ($can_update) {
				$attribute = 'lastname';
				return $can_update ? Html::input('text', $attribute, $model->$attribute, ['class' => 'form-control', 'required' => 1]) : $model->$attribute;
			},
		],
		[
			'attribute' => 'firstname',
			'format' => 'raw',
			'value' => function($model) use ($can_update) {
				$attribute = 'firstname';
				return $can_update ? Html::input('text', $attribute, $model->$attribute, ['class' => 'form-control', 'required' => 1]) : $model->$attribute;
			},
		],
		[
			'attribute' => 'patronym',
			'format' => 'raw',
			'value' => function($model) use ($can_update) {
				$attribute = 'patronym';
				return $can_update ? Html::input('text', $attribute, $model->$attribute, ['class' => 'form-control', 'required' => 1]) : $model->$attribute;
			},
		],
		[
			'header' => 'Подразделение',
			'format' => 'raw',
			'value' => function($model) use ($can_update) {
				return $can_update ? Html::dropDownList(null, $model->job->division_id ?? null,
					Division::listAll('id', 'name'), ['class' => 'form-control form-select linked-list select2', 'data-linked-child' => 'job_id', 'prompt' => 'Выберите']) : $model->job->division->name ?? 'Нет';
			},
		],
		[
			'attribute' => 'job_id',
			'format' => 'raw',
			'value' => function($model) use ($can_update) {
				$attribute = 'job_id';
				return $can_update ? Html::dropDownList($attribute, $model->$attribute,
					Job::groupedListAll('id', 'name', 'division', 'name', 'division'), ['class' => 'form-control form-select select2', 'data-linked-id' => $attribute, 'prompt' => 'Нет']) : $model->job->name ?? 'Нет';
			},
		],
		[
			'attribute' => 'superior_id',
			'format' => 'raw',
			'value' => function($model) use ($can_update) {
				$attribute = 'superior_id';
				return $can_update ? Html::dropDownList($attribute, $model->$attribute,
					User::listAll('id', 'fio'), ['class' => 'form-control form-select select2', 'prompt' => 'Нет']) : $model->superior->fio ?? 'Нет';
			},
		],
		[
			'attribute' => 'salary',
			'format' => 'raw',
			'value' => function($model) use ($can_update) {
				$attribute = 'salary';
				return $can_update ? Html::input('text', $attribute, $model->$attribute, ['type' => 'number', 'step' => 1, 'class' => 'form-control', 'required' => 1]) : $model->$attribute;
			},
		],
		[
			'attribute' => 'hire_date',
			'format' => 'raw',
			'value' => function($model) use ($can_update, $airdateConfig) {
				$attribute = 'hire_date';
				$date = $model->$attribute ? date('d.m.Y', strtotime($model->$attribute)) : '';
				return $can_update ? Html::input('text', $attribute, $date, ['class' => 'form-control air-date', 'data-air-config' => $airdateConfig, 'autocomplete' => 'off']) : $date;
			},
		],
		[
			'attribute' => 'termination_date',
			'format' => 'raw',
			'value' => function($model) use ($can_update, $airdateConfig) {
				$attribute = 'termination_date';
				$date = $model->$attribute ? date('d.m.Y', strtotime($model->$attribute)) : '';
				return $can_update ? Html::input('text', $attribute, $date, ['class' => 'form-control air-date', 'data-air-config' => $airdateConfig, 'autocomplete' => 'off']) : $date;
			},
		],
		[
			'header' => 'Стаж',
			'format' => 'raw',
			'value' => function($model) {
				return Date::monthsDaysDiffNow($model->hire_date);
			},
		],
		[
			'attribute' => 'phone',
			'format' => 'raw',
			'value' => function($model) use ($can_update) {
				$attribute = 'phone';
				return $can_update ? Html::input('text', $attribute, $model->$attribute, ['class' => 'form-control', 'required' => 1]) : $model->$attribute;
			},
		],
		[
			'attribute' => 'email',
			'format' => 'raw',
			'value' => function($model) use ($can_update) {
				$attribute = 'email';
				return $can_update ? Html::input('text', $attribute, $model->$attribute, ['class' => 'form-control', 'required' => 1]) : $model->$attribute;
			},
		],
		[
			'attribute' => 'state_id',
			'format' => 'raw',
			'value' => function($model) use ($can_update) {
				$attribute = 'state_id';
				return $can_update ? Html::dropDownList($attribute, $model->$attribute,
					User::STATE_NAMES, ['class' => 'form-control form-select']) : $model->state;
			},
		],
		[
			'attribute' => 'have_account',
			'format' => 'raw',
			'value' => function($model) use ($can_update) {
				$attribute = 'have_account';
				return $can_update ? Html::dropDownList($attribute, $model->$attribute,
					['Нет', 'Да'], ['class' => 'form-control form-select']) : ['Нет', 'Да'][(int)$model->have_account];
			},
		],
        [
            'class' => 'app\grid\ActionColumn',
            'defaultShowTitle' => false,
            'buttons' => [
                'view' => ['icon' => 'fa fa-eye', 'class' => 'btn btn-success btn-sm btn-view-card', 'title' => 'Карточка'],
				'edit' => ['icon' => 'fa fa-user', 'class' => 'btn btn-primary btn-sm btn-edit-account', 'title' => 'Аккаунт', 'visible' => function($model) use ($can_update) {return $can_update && $model->have_account;}],
				'delete' => ['icon' => 'fa fa-trash', 'class' => 'btn btn-danger btn-sm btn-row-remove', 'title' => 'Удалить', 'visible' => $can_delete],
			],
        ],
    ],
]); ?>

<?php Pjax::end() ?>

<?php Card::end(); ?>

<div id="modalContainer"></div>
<div class="modal fade" id="editAccountModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRoleTitle">Редактирование аккаунта</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="role_update_form" class="needs-validation">
                    <div class="mb-3">
                        <label for="roleName" class="form-label">Роль пользователя</label>
                        <select name="roleName" class="form-control form-select select2" id="roleName">
                            <?= Html::renderSelectOptions('null', \app\helpers\UIHelper::getUserRoles()) ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">Новый пароль</label>
                        <input type="password" name="password_new" class="form-control" id="newPassword">
                    </div>
                    <div class="mb-3">
                        <label for="repeatPassword" class="form-label">Повтор пароля</label>
                        <input type="password" name="password_repeat" class="form-control" id="repeatPassword">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary btn-edit-account-submit">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.onload = function() {
        let editModalContainer = document.getElementById('editAccountModal');
        let editModal = new bootstrap.Modal(editModalContainer);
        document.addEventListener('click', (e) => {
            let viewSender = e.target.closest('.btn-view-card');
            if(viewSender) {
                event.preventDefault();
                let tr = viewSender.closest('tr');
                $.ajax({
                    url: 'people/view?id='+tr.dataset.key,
                    method: 'GET',
                    success: function(data) {
                        let modalContainer = document.getElementById('modalContainer');
                        modalContainer.innerHTML = data;
                        (new bootstrap.Modal(modalContainer.firstElementChild)).show();
                    }
                });
                return;
            }
            let editSender = e.target.closest('.btn-edit-account');
            if(editSender) {
                event.preventDefault();
                let tr = editSender.closest('tr');
                $.ajax({
                    url: 'people/edit?id='+tr.dataset.key,
                    dataType: 'json',
                    method: 'GET',
                    success: function(data) {
                        editModalContainer.querySelectorAll('[name]').forEach((el) => {
                            if(data[el.name]) {
                                el.value = data[el.name];
                            } else {
                                el.value = null;
                            }
                        });
                        editModalContainer.querySelector('.btn-edit-account-submit').dataset.key = tr.dataset.key;
                        editModal.show();
                    }
                });
                return;
            }
            let editSubmitSender = e.target.closest('.btn-edit-account-submit');
            if(editSubmitSender) {
                let inputs = editModalContainer.querySelectorAll('*[name]')
                let data = {};
                inputs.forEach((element) => {
                    data[element.name] = element.value;
                })
                $.ajax({
                    url: 'people/edit?id='+editSubmitSender.dataset.key+'&is_account=1',
                    dataType: 'json',
                    data: data,
                    method: 'POST',
                    success: function() {
                        editModal.hide();
                    }
                });
            }
        })
    }

</script>
