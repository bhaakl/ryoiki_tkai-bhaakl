<?php

namespace app\controllers;

use app\models\PayrollTransactionType;
use app\models\PayrollTransactionCategory;
use himiklab\sortablegrid\SortableGridAction;

use yii\helpers\ArrayHelper;

/**
 * RawMaterial Controller
 */
class PayrollTransactionCategoriesController extends AjaxController
{
    public $modelClass = PayrollTransactionCategory::class;

    public $indexTemplate  = 'index';


    public function actions()
    {
        $actions = parent::actions();
        $actions['sort'] = [
            'class' => SortableGridAction::class,
            'modelName' => $this->modelClass,
        ];

        return $actions;
    }

    public function actionIndex()
    {
		$this->data['cft_types'] = ArrayHelper::map(
			PayrollTransactionType::find()
				->where(['is_deleted' => 0])
				->all(),
			'id',
			'name'
		);
        return parent::actionIndex();
    }

}
