<?php

namespace app\controllers;

use app\models\forms\DivisionsForm;
use app\models\Division;

/**
 * Divisions Controller
 */
class DivisionsController extends AjaxController
{
    public $modelClass = Division::class;
}
