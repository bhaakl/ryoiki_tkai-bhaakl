<?php

namespace app\models\search;

use app\models\OrderWProductionStageLink;
use app\models\ProductionStage;
use app\models\Status;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * Модель для поиска Order
 */
class OrderWithSandwichSearch extends OrderSearch
{
    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'stage_production_start_date',
            'stage_release_plan_date',
            'stage_release_fact_date',
            'inner_type',
            'outer_type',
            'inner_color',
            'outer_color',
            'inner_thickness',
            'outer_thickness',
            'sheet_ticket_positions',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function search($params, $recsOnPage = 20)
    {
        $subQuery1 = OrderWProductionStageLink::find()
            ->select([
                'order_id',
                'production_stage_id',
                'production_start_date as stage_production_start_date',
                'release_plan_date as stage_release_plan_date',
                'release_fact_date as stage_release_fact_date',
                'inner_type',
                'outer_type',
                'inner_color',
                'outer_color',
                'inner_thickness',
                'outer_thickness',
            ])
            ->joinWith('productionStage')
            ->where([
                'production_stage.code' => ProductionStage::STAGE_SANDWICH
            ]);

        $subQuery2 = (new Query())
            ->select([
                'ticket_positions',
            ])
            ->from(OrderWProductionStageLink::tableName())
            ->leftJoin(ProductionStage::tableName(), 'production_stage.id = order_w_production_stage_link.production_stage_id')
            ->where([
                'production_stage.code' => ProductionStage::STAGE_SHEET
            ])
            ->andWhere('order_w_production_stage_link.order_id = order.id')
            ->limit(1);

        $query = self::find()
            ->select([
                'order.*',
                'organization.name AS customerName',
                'user.fio AS managerFio',
                'product.name AS productName',
                'stage_production_start_date',
                'stage_release_plan_date',
                'stage_release_fact_date',
                'inner_type',
                'outer_type',
                'inner_color',
                'outer_color',
                'inner_thickness',
                'outer_thickness',
                'sheet_ticket_positions' => $subQuery2
            ])
            ->joinWith('customer')
            ->joinWith('manager')
            ->joinWith('product')
            ->joinWith('materialWidth')
            ->joinWith('insulationThickness')
            ->joinWith('productionLocation')
            ->joinWith('deliveryType')
            ->joinWith('status')
            ->leftJoin(['stages' => $subQuery1], 'order.id = stages.order_id')
            ->where('production_start_date IS NOT NULL')
            ->andWhere('stages.production_stage_id IS NOT NULL')
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
