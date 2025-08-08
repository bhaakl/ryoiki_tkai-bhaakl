<?php

namespace app\controllers;

use app\models\forms\MeasureUnitsForm;
use app\models\MeasureUnit;

/**
 * MeasureUnits Controller
 */
class MeasureUnitsController extends AjaxController
{
    public $modelClass = MeasureUnit::class;
}
