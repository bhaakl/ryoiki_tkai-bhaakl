<?php

namespace app\models\search;

use app\helpers\Date as DateHelper;
use app\models\Order;
use app\models\OrderWProductionStageLink;
use app\models\ProductWProductionStageLink;
use app\models\Status;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;

/**
 * Модель для поиска Order
 */
class OrderPlanningSearch extends Order
{
    public $search = '';

    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'productStages',
            'customerName',
            'managerFio',
            'productName',
            'orderPlanDates',
            'orderFactDates',
            'maxReleaseFactDate',
            'weight',
            'productStagesIds',
        ]);
    }

    public function rules()
    {
        return [
            [['specification', 'status_id', 'customer_id', 'manager_id', 'product_id', 'material_width_id', 'insulation_thickness_id', 'min_length', 'max_length', 'bundles_number', 'production_location_id', 'delivery_type_id', 'creator_id', 'modifier_id'], 'integer'],
            [['1c_date', 'production_start_date', 'release_plan_date', 'release_fact_date', 'insulation_receipt_date', 'metal_receipt_date', 'created', 'modified'], 'safe'],
            [['cars', 'delivery_address', 'comments'], 'string'],
            [['search'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'search' => Yii::t('app', 'Поиск'),
        ]);
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * {@inheritdoc}
     */
    public function search($params, $recsOnPage = 20)
    {
        $subQuery = ProductWProductionStageLink::find()
            ->select([
                'product_id',
                'GROUP_CONCAT(production_stage.short_name SEPARATOR ",") as production_stages'
            ])
            ->joinWith('productionStage')
            ->groupBy('product_id');

        $subQuery2 = OrderWProductionStageLink::find()
            ->select([
                'order_id',
                'GROUP_CONCAT(production_stage_id SEPARATOR ",") as production_stages_ids',
                'GROUP_CONCAT(CONCAT(production_stage_id, "=", release_plan_date) SEPARATOR ",") as production_plan_dates',
                'GROUP_CONCAT(CONCAT(production_stage_id, "=", release_fact_date) SEPARATOR ",") as production_fact_dates',
            ])
            ->groupBy('order_id');

        $subQuery3 = (new Query())
            ->select([
                'order_id',
                'max_release_fact_date' => new Expression("
                    CASE
                        WHEN EXISTS (SELECT 1 FROM order_w_production_stage_link t2 WHERE t2.order_id = t1.order_id AND t2.release_fact_date IS NULL) THEN NULL
                        ELSE MAX(t1.release_fact_date)
                    END
                "),
            ])
            ->from('order_w_production_stage_link t1')
            ->groupBy('order_id')
        ;

        $query = OrderPlanningSearch::find()
            ->select([
                'order.*',
                'prod_stages.production_stages as productStages',
                'organization.name AS customerName',
                'user.fio AS managerFio',
                'product.name AS productName',
                'order_stages.production_plan_dates AS orderPlanDates',
                'order_stages.production_fact_dates AS orderFactDates',
                'maxReleaseFactDate' => new Expression('COALESCE(release_fact_date, max_fact_dates.max_release_fact_date)'),
                'order_stages.production_stages_ids AS productStagesIds',
                // TODO bind params
                'weight' => new Expression("CASE 
                    WHEN product_category.weight_formula_type = 1 THEN order.area * 0.5 * 7.85
                    WHEN product_category.weight_formula_type = 2 THEN order.area * 0.5 * 7.85 + order.area * 90 * COALESCE(max_length, min_length, 0) / 1000
                END"),
            ])
            ->joinWith('customer')
            ->joinWith('manager')
            ->joinWith('product')
            ->joinWith('materialWidth')
            ->joinWith('insulationThickness')
            ->joinWith('productionLocation')
            ->joinWith('deliveryType')
            ->joinWith('status')
            ->leftJoin(['prod_stages' => $subQuery], 'product.id = prod_stages.product_id')
            ->leftJoin(['order_stages' => $subQuery2], 'order.id = order_stages.order_id')
            ->leftJoin(['max_fact_dates' => $subQuery3], 'order.id = max_fact_dates.order_id')
            ->leftJoin('product_category', 'product_category.id = product.category_id')
        ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => $this->sortingEnabled ? ['defaultOrder' => $this->defaultOrder] : false,
            'pagination' => $recsOnPage > 0 ? [
                'defaultPageSize' => $recsOnPage,
            ] : false,
        ]);

        if (!($this->load($params) && $this->validate())) {
            $query->andWhere(['in', 'status.type', Status::ACTIVE_STATUS_TYPES]);

            return $dataProvider;
        }

        $query = $dataProvider->query;
        $this->search = trim($this->search);

        if ($this->search) {
            $or = [
                'or',
                ['like', 'order.number', $this->search],
                ['like', 'order.comments', $this->search],
                ['like', 'organization.name', $this->search],
                ['like', 'user.fio', $this->search],
                ['like', 'product.name', $this->search],
            ];
            $query->andWhere($or);
        }

        $query->andFilterWhere(['=', 'customer_id', $this->getAttribute('customer_id')]);
        $query->andFilterWhere(['=', 'manager_id', $this->getAttribute('manager_id')]);
        $query->andFilterWhere(['=', 'order.product_id', $this->getAttribute('product_id')]);
        $query->andFilterWhere(['=', 'material_width_id', $this->getAttribute('material_width_id')]);
        $query->andFilterWhere(['=', 'insulation_thickness_id', $this->getAttribute('insulation_thickness_id')]);
        $query->andFilterWhere(['=', 'production_location_id', $this->getAttribute('production_location_id')]);

        if (empty($this->getAttribute('status_id'))) {
            $query->andWhere(['in', 'status.type', Status::ACTIVE_STATUS_TYPES]);
        } else if ((int)$this->getAttribute('status_id') > 0) {
            $query->andWhere(['=', 'status_id', $this->getAttribute('status_id')]);
        }

        if ($this->getAttribute('1c_date') && !empty($dateRange = DateHelper::parseDateRange($this->getAttribute('1c_date')))) {
            $query->andWhere(['between', '1c_date', $dateRange['from'], $dateRange['to']]);
        }
        if ($this->production_start_date && !empty($dateRange = DateHelper::parseDateRange($this->production_start_date))) {
            $query->andWhere(['between', 'production_start_date', $dateRange['from'], $dateRange['to']]);
        }
        if ($this->release_plan_date && !empty($dateRange = DateHelper::parseDateRange($this->release_plan_date))) {
            $query->andWhere(['between', 'release_plan_date', $dateRange['from'], $dateRange['to']]);
        }
        if ($this->insulation_receipt_date && !empty($dateRange = DateHelper::parseDateRange($this->insulation_receipt_date))) {
            $query->andWhere(['between', 'insulation_receipt_date', $dateRange['from'], $dateRange['to']]);
        }
        if ($this->metal_receipt_date && !empty($dateRange = DateHelper::parseDateRange($this->metal_receipt_date))) {
            $query->andWhere(['between', 'metal_receipt_date', $dateRange['from'], $dateRange['to']]);
        }

        // Return data provider
        return $dataProvider;
    }
}
