<?php

namespace app\controllers;

use app\models\Car;
use app\models\CarType;
use app\models\forms\CarsForm;
use yii\helpers\ArrayHelper;

class CarsController extends AjaxController
{
    public $modelClass = Car::class;
}
