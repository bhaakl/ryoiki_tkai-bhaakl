<?php

namespace app\controllers;

use app\models\CarType;

/**
 * CarTypes Controller
 */
class CarTypesController extends AjaxController
{
    public $modelClass = CarType::class;
}
