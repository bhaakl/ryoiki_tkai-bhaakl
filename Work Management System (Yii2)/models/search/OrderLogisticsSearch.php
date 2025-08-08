<?php

namespace app\models\search;

use app\models\Order;
use app\models\Status;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;

/**
 * Модель для поиска Order
 */
class OrderLogisticsSearch extends Order
{
    public $search = '';

    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'fact_area_left',
            'fact_area_gone',
            'plan_area_to_go',
            'awaiting_area_to_go',
            'customerName',
            'productName',
            'productionLocationName',
            'deliveryTypeName',
        ]);
    }

    public function rules()
    {
        return [
            [['specification', 'status_id', 'customer_id', 'manager_id', 'product_id', 'material_width_id', 'insulation_thickness_id', 'min_length', 'max_length', 'bundles_number', 'production_location_id', 'delivery_type_id', 'creator_id', 'modifier_id'], 'integer'],
            [['1c_date', 'production_start_date', 'release_plan_date', 'release_fact_date', 'insulation_receipt_date', 'metal_receipt_date', 'created', 'modified'], 'safe'],
            [['delivery_address', 'comments'], 'string'],
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
        $subQuery1 = (new Query())
            ->select('SUM(area_fact)')
            ->from('order_w_trip_link')
            ->leftJoin('trip', 'order_w_trip_link.trip_id = trip.id')
            ->where(['order_w_trip_link.is_deleted' => 0])
            ->andWhere('order_w_trip_link.order_id = order.id')
            ->andWhere('trip.loading_fact_end_date IS NOT NULL')
            ->andWhere('trip.loading_fact_end_date < NOW()')
        ;

        $subQuery2 = (new Query())
            ->select('SUM(area_fact)')
            ->from('order_w_trip_link')
            ->leftJoin('trip', 'order_w_trip_link.trip_id = trip.id')
            ->where(['order_w_trip_link.is_deleted' => 0])
            ->andWhere('order_w_trip_link.order_id = order.id')
            ->andWhere('trip.loading_plan_start_date IS NULL OR trip.loading_plan_start_date > NOW()')
        ;

        $subQuery = (new Query())
            ->select([
                'order.id AS order_id',
                'fact_area_gone' => $subQuery1,
                'plan_area_to_go' => $subQuery2,
            ])
            ->from('order')
            ->groupBy('order.id')
        ;

        $query = OrderLogisticsSearch::find()
            ->select([
                'order.*',
                'orderStats.fact_area_gone',
                'orderStats.plan_area_to_go',
                new Expression('GREATEST(area - area_finished, 0) AS fact_area_left'),
                new Expression('GREATEST(area_finished - IFNULL(orderStats.fact_area_gone, 0) - IFNULL(orderStats.plan_area_to_go, 0), 0) AS awaiting_area_to_go'),
                'organization.name AS customerName',
                'product.name AS productName',
                'production_location.name AS productionLocationName',
                'delivery_type.name AS deliveryTypeName',
            ])
            ->joinWith('customer')
            ->joinWith('manager')
            ->joinWith('product')
            ->joinWith('materialWidth')
            ->joinWith('insulationThickness')
            ->joinWith('productionLocation')
            ->joinWith('deliveryType')
            ->joinWith('status')
            ->leftJoin(['orderStats' => $subQuery], 'order.id = orderStats.order_id')
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
        $query->andFilterWhere(['=', 'delivery_type_id', $this->getAttribute('delivery_type_id')]);

        if (empty($this->getAttribute('status_id'))) {
            $query->andWhere(['in', 'status.type', Status::ACTIVE_STATUS_TYPES]);
        } else if ((int)$this->getAttribute('status_id') > 0) {
            $query->andWhere(['=', 'status_id', $this->getAttribute('status_id')]);
        }

        // Return data provider
        return $dataProvider;
    }
}
