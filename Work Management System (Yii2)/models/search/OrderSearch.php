<?php

namespace app\models\search;

use app\helpers\Date as DateHelper;
use app\models\Order;
use app\models\ProductWProductionStageLink;
use app\models\Status;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Модель для поиска Order
 */
class OrderSearch extends Order
{
    public $search = '';

    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'productStages',
            'customerName',
            'managerFio',
            'productName',
            'productionLocationName',
            'deliveryTypeName',
            'productCategoryName',
        ]);
    }

    public function rules()
    {
        return [
            [['specification', 'status_id', 'customer_id', 'manager_id', 'product_id', 'material_width_id', 'insulation_thickness_id', 'min_length', 'max_length', 'production_location_id', 'delivery_type_id', 'creator_id', 'modifier_id'], 'integer'],
            [['1c_date', 'production_start_date', 'release_plan_date', 'release_fact_date', 'insulation_receipt_date', 'metal_receipt_date', 'created', 'modified'], 'safe'],
            [['delivery_address', 'comments'], 'string'],
            [['search', 'id'], 'safe'],
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
            ->select(['product_id', 'GROUP_CONCAT(production_stage.short_name SEPARATOR ",") as production_stages'])
            ->joinWith('productionStage')
            ->groupBy('product_id');

        $query = self::find()
            ->select([
                'order.*',
                'stages.production_stages as productStages',
                'organization.name AS customerName',
                'user.fio AS managerFio',
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
            ->leftJoin(['stages' => $subQuery], 'product.id = stages.product_id')
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
                ['like', 'order.prepayment', $this->search],
                ['like', 'order.delivery_address', $this->search],
            ];
            $query->andWhere($or);
        }

        $query->andFilterWhere(['=', 'order.id', $this->getAttribute('id')]);
        $query->andFilterWhere(['=', 'customer_id', $this->getAttribute('customer_id')]);
        $query->andFilterWhere(['=', 'manager_id', $this->getAttribute('manager_id')]);
        $query->andFilterWhere(['=', 'order.product_id', $this->getAttribute('product_id')]);
        $query->andFilterWhere(['=', 'material_width_id', $this->getAttribute('material_width_id')]);
        $query->andFilterWhere(['=', 'insulation_thickness_id', $this->getAttribute('insulation_thickness_id')]);
        $query->andFilterWhere(['=', 'production_location_id', $this->getAttribute('production_location_id')]);
        $query->andFilterWhere(['=', 'delivery_type_id', $this->getAttribute('delivery_type_id')]);

        if (isset($this->specification) && (int)$this->specification >= 0) {
            $query->andWhere(['=', 'specification', $this->getAttribute('specification')]);
        }

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

    static public function getById($id)
    {
        $subQuery = ProductWProductionStageLink::find()
            ->select(['product_id', 'GROUP_CONCAT(production_stage.short_name SEPARATOR ",") as production_stages'])
            ->joinWith('productionStage')
            ->groupBy('product_id');

        return self::find()
            ->select([
                'order.*',
                'stages.production_stages as productStages',
                'organization.name AS customerName',
                'user.fio AS managerFio',
                'product.name AS productName',
            ])
            ->joinWith('customer')
            ->joinWith('manager')
            ->joinWith('product')
            ->joinWith('materialWidth')
            ->joinWith('insulationThickness')
            ->joinWith('productionLocation')
            ->joinWith('deliveryType')
            ->joinWith('status')
            ->leftJoin(['stages' => $subQuery], 'product.id = stages.product_id')
            ->where(['order.id' => $id])
            ->one()
        ;
    }
}
