<?php

namespace app\controllers;

use app\helpers\ClassHelper;
use app\models\StatusScope;
use app\models\forms\StatusesForm;
use app\models\Status;
use himiklab\sortablegrid\SortableGridAction;
use Yii;

use yii\helpers\ArrayHelper;

/**
 * RawMaterial Controller
 */
class StatusesController extends AjaxController
{
    public $modelClass = Status::class;
}
