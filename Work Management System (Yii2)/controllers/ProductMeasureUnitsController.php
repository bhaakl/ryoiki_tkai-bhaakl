<?php

namespace app\controllers;

use app\models\ProductMeasureUnit;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
 * ProductMeasureUnitsController Controller
 */
class ProductMeasureUnitsController extends AjaxController
{
    public $modelClass = ProductMeasureUnit::class;
    public $accessRules = [
        [
            'actions' => ['index'],
            'allow' => true,
            'roles' => ['app_products_index']
        ],
        [
            'actions' => ['create', 'update', 'delete'],
            'allow' => true,
            'roles' => ['app_products_edit']
        ]
    ];

    public function actionIndex($product_id = null)
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $params = Yii::$app->request->queryParams;
            $searchModel = new $this->modelClass();
            $searchModel->product_id = $product_id;
            $dataProvider = $searchModel->search($params, -1);
            return $this->renderPartial('index', compact('dataProvider', 'searchModel'));
        }
        throw new BadRequestHttpException();
    }
}
