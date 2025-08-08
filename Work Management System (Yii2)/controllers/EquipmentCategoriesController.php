<?php

namespace app\controllers;

use app\models\forms\EquipmentCategoriesForm;
use app\models\EquipmentCategory;

/**
 * EquipmentCategories Controller
 */
class EquipmentCategoriesController extends AjaxController
{
    public $modelClass = EquipmentCategory::class;
}
