<?php
namespace app\controllers;

use app\models\BaseActiveRecord;
use app\models\search\SummaryOutputSearch;

use Yii;
use yii\data\ActiveDataProvider;

class SummaryOutputController extends AjaxController
{
    public $modelClass = SummaryOutputSearch::class;

    public function actionIndex()
    {
        $params = Yii::$app->request->queryParams;

        $monthYear = $params['month_year'] ?? date("m-Y");
        $monthYear = $monthYear === "" ? date("m-Y") : $monthYear;

        if ($this->searchClass) {
            $searchModel = new $this->searchClass();
            $searchModel->scenario = BaseActiveRecord::SCENARIO_SEARCH;
        } else {
            $searchModel = new $this->modelClass();
            $searchModel->scenario = BaseActiveRecord::SCENARIO_SEARCH;
        }

        if (isset($params['page'])) {
            $this->recsOnPage = $params['page'] > 0 ? $this->recsOnPage : false;
        }

        /** @var ActiveDataProvider $dataProvider */
        $dataProvider = $searchModel->search($params, $this->recsOnPage);

        return $this->render($this->indexTemplate, [
            'title'        => $this->sysTitle(),
            'searchModel'  => $searchModel,
            'model'        => $searchModel,
            'dataProvider' => $dataProvider,
            'page' => $params['page'] ?? 0,
            'daysArray' => $searchModel->getDaysInMonth($monthYear),
            'productList' => $searchModel->getProductList(),
            'unitList' => $searchModel->getUnitList(),
            'monthYear' => $monthYear,
        ]);
    }

}
