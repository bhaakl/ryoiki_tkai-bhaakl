<?php

namespace app\controllers;

use app\models\forms\ProductionLocationCategoriesForm;
use app\models\ProductionLocationCategory;

/**
 * ProductionLocationCategories Controller
 */
class ProductionLocationCategoriesController extends AjaxController
{
    public $modelClass = ProductionLocationCategory::class;
}
