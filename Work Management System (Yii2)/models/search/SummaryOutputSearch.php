<?php
namespace app\models\search;

use app\models\Product;
use app\models\MeasureUnit;

use DateInterval;
use DatePeriod;
use DateTime;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class SummaryOutputSearch extends Product
{
    public $search_name, $search_type;

    public function rules()
    {
        return [
            [['search_name', 'search_type'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function search($params, $recsOnPage = 20)
    {
        $monthYear = $params['month_year'] ?? date("m-Y");
        $month = explode('-', $monthYear)[0];
        $year = explode('-', $monthYear)[1];

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $unitSql =<<<SQL
            (SELECT 
                `mu`.`short_name` 
             FROM measure_unit mu 
             INNER JOIN product_measure_unit pmu ON pmu.measure_unit_id = mu.id 
             WHERE pmu.product_id = p.id
             ) AS unit
SQL;

        $selects = [
            'p.id as id',
            'p.name as name',
            $unitSql,
        ];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayStr = sprintf("%02d", $day);
            $selects["day_{$dayStr}"] = "SUM(CASE WHEN DATE_FORMAT(c.work_date, '%d') = '{$dayStr}' THEN w.hours_fact ELSE 0 END)";
        }

        $selects['count_sum'] = 'SUM(w.hours_fact)';

        $query = (new Query())
            ->select($selects)
            ->from(['p' => 'product'])
            ->innerJoin(['m' => 'product_measure_unit'], 'm.product_id = p.id')
            ->innerJoin(['u' => 'measure_unit'], 'u.id = m.measure_unit_id')
            ->leftJoin(['c' => 'worker_report_card'], 'c.product_id = p.id')
            ->leftJoin(['w' => 'worker_report_card_worker'], 'w.card_id = c.id')
            ->where(['YEAR(c.work_date)' => $year])
            ->andWhere(['MONTH(c.work_date)' => $month])
            ->groupBy(['p.id', 'p.name']);

        if (!Yii::$app->user->can('Developer')){
            $query->andWhere(['w.user_id' => Yii::$app->user->id ]);
        }

        if(key_exists('per-page', $params)) {
            $recsOnPage = (int)$params['per-page'];
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => $this->sortingEnabled ? ['defaultOrder' => $this->defaultOrder] : false,
            'pagination' => $recsOnPage > 0 ? [
                'defaultPageSize' => $recsOnPage,
            ] : false,
        ]);

        $sorting = [
            'id' => [
                'asc' => ['id' => SORT_ASC],
                'desc' => ['id' => SORT_DESC],
            ],
            'name' => [
                'asc' => ['name' => SORT_ASC],
                'desc' => ['name' => SORT_DESC],
            ],
            'unit' => [
                'asc' => ['unit' => SORT_ASC],
                'desc' => ['unit' => SORT_DESC],
            ],
            'count_sum' => [
                'asc' => ['count_sum' => SORT_ASC],
                'desc' => ['count_sum' => SORT_DESC],
            ],
        ];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayStr = sprintf("%02d", $day);
            $sorting["day_{$dayStr}"] = [
                'asc' => ["day_{$dayStr}" => SORT_ASC],
                'desc' => ["day_{$dayStr}" => SORT_DESC],
            ];
        }

        $dataProvider->sort->attributes = $sorting;

        if (!$this->load($params)) {
            return $dataProvider;
        }

        if ($this->search_name) {
            $query->andWhere(['like', 'p.name', trim($this->search_name)]);
        }

        if ($this->search_type) {
            $query->andWhere(['=', 'u.short_name', trim($this->search_type)]);
        }

        return $dataProvider;
    }

    public function getProductList(): array
    {
        $list = [];
        $stages = Product::find()->where(['is_deleted' => 0])->all();
        foreach ($stages as $stage){
            $list[$stage->name] = $stage->name;
        }

        return $list;
    }

    public function getUnitList(): array
    {
        return ArrayHelper::map(MeasureUnit::find()->where(['is_deleted' => 0])->all(),'short_name','unitFullName');
    }

    public function getDaysInMonth(string $monthYear): array
    {
        $firstDay = new DateTime("01-$monthYear");

        $lastDay = clone $firstDay;
        $lastDay->modify('last day of this month');

        $daysWithLeadingZero = [];

        $interval = new DateInterval('P1D');
        $period = new DatePeriod($firstDay, $interval, $lastDay->modify('+1 day'));

        foreach ($period as $date) {
            $daysWithLeadingZero[] = $date->format('Y-m-d');
        }

        return $daysWithLeadingZero;
    }

}
