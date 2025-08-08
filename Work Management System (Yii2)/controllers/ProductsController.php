<?php

namespace app\controllers;

use app\models\Product;
use app\models\search\ProductSearch;

/**
 * Products Controller
 */
class ProductsController extends AjaxController
{
    public $modelClass = Product::class;
    public $searchClass = ProductSearch::class;
}
