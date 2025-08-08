<?php

namespace app\controllers;

use app\models\search\SummaryTimeSearch;

class SummaryTimeController extends AjaxController
{
    public $modelClass = SummaryTimeSearch::class;
}
