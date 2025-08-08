<?php

use kartik\editable\Editable;
use app\grid\GridViewClean;
//use app\grid\GridViewTwoLevel;
//use \kartik\grid\GridView;
use app\widgets\Card;
use yii\helpers\Html;
use app\widgets\MultipleInput;
use app\widgets\ActiveForm;
use yii\helpers\Url;

use app\widgets\ActionButtons;

use app\models\CashFlowTransactionCategory;
use app\models\PayrollCalculation;
use app\models\WorkerReportCard;
use app\models\SalaryPayments;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\PayrollStatementSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $page integer */

//\app\assets\EditableFieldsAsset::register($this);
\app\assets\ExtendableColumnsAsset::register($this);

$this->title = Yii::$app->urlManager->getLastTitle();

$cashFlowTransactionCategoryComingList = CashFlowTransactionCategory::find()->where(['type_id' => 1])->all();
$cashFlowTransactionCategoryExpenditureList = CashFlowTransactionCategory::find()->where(['type_id' => 2])->all();


/*$model->loadDateRange();
$searchModel->loadDateRange();*/

$startDate = $searchModel->dateFrom;
$endDate = $searchModel->dateTo;
?>


<?php Card::begin([]); ?>

<?= $this->render('_search', ['model' => $searchModel]); ?>

<?php Pjax::begin(['id' => 'pjax']) ?>
<?php
$columns = [
    ['class' => 'yii\grid\SerialColumn'],
    [
        'attribute' => 'fio',
    ],
    [
        'attribute' => 'job',
        'value' => function ($model) {
            return $model->job->name ?? "Not set";
        }
    ],
    [
        'attribute' => 'division',
        'value' => function ($model) {
            return $model->job->division->name ?? "Not set";
        }
    ],
    'state',
    'salary',
    [
        'label' => 'Часы',
        'headerOptions' => ['title' => 'Количество отработанных часов'],
        'value' => function ($model) use($startDate, $endDate) {
            return $model->getHoursSpent($startDate, $endDate);
        }
    ],
    [
        'label' => 'Сумма',
        'headerOptions' => ['title' => 'Сумма заработанного, руб.'],
        'value' => function ($model) use($startDate, $endDate) {
            return $model->salary * $model->getHoursSpent($startDate, $endDate);
        }
    ],
    [
        'attribute' => 'payroll_calculations',
        'headerOptions' => [
         'class'=>'extendable-column', 'data-key' => 'payroll_calculations', 'style' => ['background-color' => '#bbffbb']
        ],
        'label' => 'Начисления',
        'value' => function ($model) use($startDate, $endDate){
            return PayrollCalculation::getPayrollSumByUserAndDate($model->id, 1, $startDate, $endDate);
        }
    ],
];

$begin = new DateTime($startDate);
$end = new DateTime($endDate);

$interval = DateInterval::createFromDateString('1 day');
$period = new DatePeriod($begin, $interval, $end);

$i = 0;
$payroll_calculations_count = 0;
foreach ($cashFlowTransactionCategoryComingList as $item){
    /* @var $item CashFlowTransactionCategory */
    $columns[] = [
        'attribute' => 'payroll_calculations_category_' . $i,
        'headerOptions' => ['data-parent' => 'payroll_calculations', 'style' => ['display' => 'none', 'background-color' => '#bbffbb']],
        'contentOptions' => ['data-parent' => 'payroll_calculations', 'style' => ['display' => 'none']],
        'label' => $item->name,
        'value' => function ($model) use ($item, $startDate, $endDate){
            return PayrollCalculation::getPayrollByUserAndCategoryAndDate($model->id, $item->id, $startDate, $endDate);
        }
    ];
    $payroll_calculations_count++;
}

$columns[] = [
    'attribute' => 'salary_payment_by_period',
    'label' => 'Аванс насчитан',
    'encodeLabel' => false,
    'value' => function ($model) use($startDate, $endDate){
        return '';
    }
];
$columns[] = [
    'attribute' => 'salary_payment_by_period',
    'headerOptions' => [
        'class'=>'extendable-column',
        'data-key' => 'salary_payment_by_period',
        'title' => 'за период c '.date('d.m.y', strtotime($startDate)).' по '. date('d.m.y', strtotime($endDate))
    ],
    'label' => 'Аванс выдан',
    'encodeLabel' => false,
    'value' => function ($model) use($startDate, $endDate){
        return SalaryPayments::getByUserAndDate($model->id, $startDate, $endDate);
    }
];

$count_days = 0;
foreach ($period as $dt){
    $columns[] = [
        'attribute' => 'salary_payment_by_period' . $i,
        'headerOptions' => ['data-parent' => 'salary_payment_by_period', 'style' => ['display' => 'none']],
        'contentOptions' => ['data-parent' => 'salary_payment_by_period', 'style' => ['display' => 'none']],
        'label' => $dt->format("d.m.Y"),
        'value' => function ($model) use ($dt){
            return SalaryPayments::getByUserAndOneDate($model->id, $dt->format("Y-m-d"));
        }
    ];
    $count_days++;
}


$columns[] = [
    'attribute' => 'payroll_calculations_expenditure',
    'headerOptions' => [
     'class'=>'extendable-column', 'data-key' => 'payroll_calculations_expenditure', 'style' => ['background-color' => '#ffcccc']
    ],
    'label' => 'Списания',
    'value' => function ($model) use($startDate, $endDate){
        return PayrollCalculation::getPayrollSumByUserAndDate($model->id, 2, $startDate, $endDate);
    }
];

$i = 0;
$payroll_calculations_expenditure_count = 0;
foreach ($cashFlowTransactionCategoryExpenditureList as $item){
    /* @var $item CashFlowTransactionCategory */
    $columns[] = [
        'attribute' => 'payroll_calculations_category_' . $i,
        'headerOptions' => ['data-parent' => 'payroll_calculations_expenditure', 'style' => ['display' => 'none', 'background-color' => '#ffcccc']],
        'contentOptions' => ['data-parent' => 'payroll_calculations_expenditure', 'style' => ['display' => 'none']],
        'label' => $item->name,
        'value' => function ($model) use ($item, $startDate, $endDate){
            return PayrollCalculation::getPayrollByUserAndCategoryAndDate($model->id, $item->id, $startDate, $endDate);
        }
    ];
    $payroll_calculations_expenditure_count++;
}

$columns[] = [
    'attribute' => 'salary_total',
    'label' => 'ЗП насчитана',
    'value' => function ($model) use($startDate, $endDate) {
        return PayrollCalculation::getPayrollSumByUserAndDate($model->id, 1, $startDate, $endDate) - PayrollCalculation::getPayrollSumByUserAndDate($model->id, 2, $startDate, $endDate);
    }
];

$columns[] = [
    'attribute' => 'salary_by_period',
    'label' => 'ЗП выдана',
    'encodeLabel' => false,
    'headerOptions' => [
        'class'=>'extendable-column',
        'data-key' => 'salary_by_period',
        'title' => 'за период c '.date('d.m.y', strtotime($startDate)).' по '. date('d.m.y', strtotime($endDate))
    ],
    'value' => function ($model) use ($startDate, $endDate){
        return SalaryPayments::getByUserAndDate($model->id, $startDate, $endDate);
    }
];


foreach ($period as $dt){
    $columns[] = [
        'attribute' => 'salary_by_period' . $i,
        'headerOptions' => ['data-parent' => 'salary_by_period', 'style' => 'display: none;'],
        'contentOptions' => ['data-parent' => 'salary_by_period', 'style' => 'display: none;'],
        'label' => $dt->format("d.m.Y"),
        'value' => function ($model) use ($dt){
            return SalaryPayments::getByUserAndOneDate($model->id, $dt->format("Y-m-d"));
        }
    ];
}




/*Two Level Header in table*/
/*$beforeHeader = [ [ 'columns' => [ ['content' => '', 'options' => ['colspan' => 9] ] ]]];

for ($i=0; $i < $payroll_calculations_count; $i++) {

    $beforeHeader[0]['columns'][] = [
        'options' => [ 'data-parent' => 'payroll_calculations' ],
        'label' => '',
    ];
}

$beforeHeader[0]['columns'][] = ['content' => 'Аванс выданный', 'options' => [ 'class' => 'text-center', 'colspan' => 1, 'data-colspans' => $count_days+1, 'data-header-id' => 'salary_payment_by_period']] ;

//$last_step = 9+$payroll_calculations_count+$count_days;


$beforeHeader[0]['columns'][] = [ [ 'columns' => [ ['content' => ''] ]]];
for ($i=0; $i < $payroll_calculations_expenditure_count; $i++) {

    $beforeHeader[0]['columns'][] = [
        'options' => [ 'data-parent' => 'payroll_calculations_expenditure' ],
        'label' => '',
    ];
}


//$last_step = $last_step+$payroll_calculations_expenditure_count+1;

$beforeHeader[0]['columns'][] = [ [ 'columns' => [ ['content' => ''] ]]];
$beforeHeader[0]['columns'][] = ['content' => 'ЗП выдана', 'options' => [ 'class' => 'text-center', 'colspan' => 1, 'data-colspans' => $count_days+1, 'data-header-id' => 'salary_by_period']] ;
*/
/*Two Level Header in table*/

?>

<?= GridViewClean::widget([
    'showHeader' => true,
    'showFooter' => false,
    'tableOptions' => [
        'class' => 'table align-middle table-check table-striped mb-0 editable-fields',
        'id' => 'editable-table',
    ],
    'dataProvider' => $dataProvider,
    'filterSelector' => 'select[name="per-page"], .search-block *[name]',
    'showPageSize' => true,
    'columns' => $columns
]); ?>

<?php Pjax::end();

Card::end();
