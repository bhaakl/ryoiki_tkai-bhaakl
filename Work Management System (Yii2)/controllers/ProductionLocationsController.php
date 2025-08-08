<?php

namespace app\controllers;

use app\helpers\ClassHelper;
use app\models\ProductionLocationCategory;
use app\models\forms\ProductionLocationsForm;
use app\models\ProductionLocation;
use himiklab\sortablegrid\SortableGridAction;
use Yii;

use yii\helpers\ArrayHelper;

/**
 * RawMaterial Controller
 */
class ProductionLocationsController extends AjaxController
{
    public $modelClass = ProductionLocation::class;

    public $accessRules = [
        [
            'actions' => ['sort'],
            'allow' => true,
            'roles' => ['app_production-locations_index'],
        ]
    ];

    public function actions()
    {
        $actions = parent::actions();
        $actions['sort'] = [
            'class' => SortableGridAction::class,
            'modelName' => $this->modelClass,
        ];

        return $actions;
    }
}
