<?php

namespace app\controllers;

use app\models\Warehouse;

/**
 * Warehouses Controller
 */
class WarehousesController extends AjaxController
{
    public $modelClass = Warehouse::class;
}
