<?php

namespace app\controllers;

use app\models\forms\ProductionStagesForm;
use app\models\ProductionStage;

/**
 * ProductionStages Controller
 */
class ProductionStagesController extends AjaxController
{
    public $modelClass = ProductionStage::class;
}
