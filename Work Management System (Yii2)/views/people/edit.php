<?php

use app\helpers\UIHelper;
use yii\helpers\Html;
use app\widgets\ActiveForm;
use app\widgets\Card;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$action = ($model->isNewRecord ? 'Новый человек' : 'Редактирование');
$this->title = Html::encode(Yii::$app->urlManager->getLastTitle() . ' - ' . $action .
    ($model->isNewRecord ? '' : ' «' . (string)$model . '»' ));

if (!$model->isNewRecord) $this->params['breadcrumbs'][] = (string)$model;
$this->params['breadcrumbs'][] = $action;

\app\assets\Select2aAsset::register($this);

?>

<?php Card::begin([]); ?>

<div class="edit-form">

    <?php $form = ActiveForm::begin(['options' => ['autocomplete' => 'off', 'enctype' => 'multipart/form-data']]); ?>

    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-3">
            <?= $form->field($model, 'lastname')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3">
            <?= $form->field($model, 'firstname')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3">
            <?= $form->field($model, 'patronym')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-3">
            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3">
            <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3">
            <?= $form->field($model, 'roleName')->dropDownList(UIHelper::getUserRoles(), ['prompt' => 'Выберите роль']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-3">
            <?= $form->field($model, 'user_status_id')->dropDownList(UIHelper::getUserStatuses(), ['prompt' => 'Выберите статус']) ?>
        </div>
    </div>

    <?php if ($model->isNewRecord) { ?>
    <hr />

    <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?= $form->field($model, 'password_new')->passwordInput(['maxlength' => true, 'autocomplete' => 'off']) ?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?= $form->field($model, 'password_repeat')->passwordInput(['maxlength' => true, 'autocomplete' => 'off']) ?>
        </div>
    </div>
    <?php } ?>


    <div>
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a('Отмена', $this->context->getReferrer(), ['class' => 'btn btn-soft-dark']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php Card::end() ?>
