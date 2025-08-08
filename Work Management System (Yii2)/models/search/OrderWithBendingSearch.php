<?php

namespace app\models\search;

use app\models\OrderWProductionStageLink;
use app\models\ProductionStage;
use app\models\Status;
use yii\data\ActiveDataProvider;

/**
 * Модель для поиска Order
 */
class OrderWithBendingSearch extends OrderSearch
{
    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'stage_production_start_date',
            'stage_release_plan_date',
            'stage_release_fact_date',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function search($params, $recsOnPage = 20)
    {
        $subQuery = OrderWProductionStageLink::find()
            ->select([
                'order_id',
                'production_stage_id',
                'production_start_date as stage_production_start_date',
                'release_plan_date as stage_release_plan_date',
                'release_fact_date as stage_release_fact_date'
            ])
            ->joinWith('productionStage')
            ->where([
                'production_stage.code' => ProductionStage::STAGE_BENDING
            ]);

        $query = self::find()
            ->select([
                'order.*',
                'organization.name AS customerName',
                'user.fio AS managerFio',
                'product.name AS productName',
                'stage_production_start_date',
                'stage_release_plan_date',
                'stage_release_fact_date'
            ])
            ->joinWith('customer')
            ->joinWith('manager')
            ->joinWith('product')
            ->joinWith('materialWidth')
            ->joinWith('insulationThickness')
            ->joinWith('productionLocation')
            ->joinWith('deliveryType')
            ->joinWith('status')
            ->leftJoin(['stages' => $subQuery], 'order.id = stages.order_id')
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
