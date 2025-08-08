<?php

namespace app\controllers;

use app\models\search\PayrollStatementSearch;

class PayrollStatementController extends AjaxController
{
    public $modelClass = PayrollStatementSearch::class;
}
