<?php

namespace app\controllers;

use app\models\search\WorkerReportCardSearch;

class WorkerReportCardController extends AjaxController
{
    public $modelClass = WorkerReportCardSearch::class;
}
