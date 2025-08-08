<?php

namespace app\controllers;

use app\helpers\ClassHelper;
use app\models\Division;
use app\models\forms\JobsForm;
use app\models\Job;
use himiklab\sortablegrid\SortableGridAction;
use Yii;

use yii\helpers\ArrayHelper;

/**
 * RawMaterial Controller
 */
class JobsController extends AjaxController
{
    public $modelClass = Job::class;
}
