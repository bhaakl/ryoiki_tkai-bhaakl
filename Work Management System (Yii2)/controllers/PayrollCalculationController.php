<?php

namespace app\controllers;

use app\models\BaseActiveRecord;
use app\models\PayrollCalculation;
use Yii;
use yii\data\ActiveDataProvider;

class PayrollCalculationController extends AjaxController
{
    public $modelClass = PayrollCalculation::class;
    //public $multipleFormClass = SalaryPaymentsForm::class;
}
