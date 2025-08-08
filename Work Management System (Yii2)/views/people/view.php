<?php

use app\widgets\ActionButtons;
use yii\helpers\Url;
use app\models\WorkSection;
use unclead\multipleinput\MultipleInputColumn;
use app\models\Specification;
use app\widgets\MultipleInput;
use yii\helpers\Html;
use yii\widgets\Pjax;
use app\widgets\ActiveForm;
use app\widgets\Card;
use app\models\Product;
use app\grid\GridView;
use yii\data\ActiveDataProvider;
use kartik\editable\Editable;
use app\assets\Select2aAsset;

/* @var $this yii\web\View */
/* @var $model app\models\Product */

$action = 'Просмотр';
$this->title = 'Человек:  ' . $model->fullname;

Select2aAsset::register($this);

?>

<?php Card::begin([]); ?>

<?= $this->render('_view', ['model' => $model]) ?>

<?php Card::end() ?>
