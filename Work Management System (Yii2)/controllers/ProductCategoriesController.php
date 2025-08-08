<?php

namespace app\controllers;

use app\models\forms\ProductCategoriesForm;
use app\models\ProductCategory;

/**
 * ProductCategories Controller
 */
class ProductCategoriesController extends AjaxController
{
    public $modelClass = ProductCategory::class;
}
