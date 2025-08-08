<?php

namespace app\models\search;

use app\models\OrderWTripLink;
use app\models\Status;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Модель для поиска OrderWTripLink
 */
class OrderWTripLinkSearch extends OrderWTripLink
{
    public $search = '';

    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'car_name',
            'car_reg_number',
            'car_type',
            'length',
            'width',
            'height',
            'loading_fact_start_date',
            'loading_plan_start_date',
        ]);
    }

    public function rules()
    {
        return [
            [['order_id', 'trip_id', 'accounting_documents', 'loading_documents', 'is_accountant_confirmed', 'is_client_refused', 'is_deleted', 'creator_id', 'modifier_id'], 'integer'],
            [['area_plan', 'area_fact', 'bundles_number_plan', 'bundles_number_fact'], 'number'],
            [['content'], 'string'],
            [['created', 'modified'], 'safe'],
            [['refusal_reason'], 'string', 'max' => 255],
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
        $query = OrderWTripLinkSearch::find()
            ->select([
                'order_w_trip_link.*',
                "car.name AS car_name",
                "car.reg_number AS car_reg_number",
                'car_type.short_name AS car_type',
                'car_type.length AS length',
                'car_type.width AS width',
                'car_type.height AS height',
                'trip.loading_plan_start_date',
                'trip.loading_fact_start_date',
            ])
            ->joinWith('order')
            ->joinWith('trip')
            ->joinWith('trip.car')
            ->joinWith('trip.car.carType')
            ->joinWith('order.status')
            ->where(['!=', 'status.type', Status::STATUS_TYPE_CANCELED])
            ->andWhere('order_w_trip_link.is_deleted = 0')
            ->andWhere('trip.is_deleted = 0')
        ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => $this->sortingEnabled ? ['defaultOrder' => $this->defaultOrder] : false,
            'pagination' => $recsOnPage > 0 ? [
                'defaultPageSize' => $recsOnPage,
            ] : false,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query = $dataProvider->query;
        $this->search = trim($this->search);

        if ($this->search) {
            $or = [
                'or',
                ['like', 'order_w_trip_link.content', $this->search],
                ['like', 'order.number', $this->search],
                ['like', 'order.comments', $this->search],
                ['like', 'trip.drivers', $this->search],
                ['like', 'car.name', $this->search],
                ['like', 'car.reg_number', $this->search],
            ];
            $query->andWhere($or);
        }

        $query->andFilterWhere(['=', 'order_id', $this->getAttribute('order_id')]);
        $query->andFilterWhere(['=', 'trip_id', $this->getAttribute('trip_id')]);

        // Return data provider
        return $dataProvider;
    }
}
