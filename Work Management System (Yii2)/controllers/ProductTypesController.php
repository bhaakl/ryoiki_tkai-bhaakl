<?php

namespace app\controllers;

use app\models\forms\ProductTypesForm;
use app\models\ProductType;

/**
 * ProductTypes Controller
 */
class ProductTypesController extends AjaxController
{
    public $modelClass = ProductType::class;
}
