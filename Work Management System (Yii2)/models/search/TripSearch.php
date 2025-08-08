<?php

namespace app\models\search;

use app\models\Trip;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Модель для поиска Trip
 */
class TripSearch extends Trip
{
    public $search = '';

    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'car_name',
            'short_name',
            'length',
            'width',
            'height',
        ]);
    }

    public function rules()
    {
        return [
            [['car_id', 'is_call_required', 'is_deleted', 'creator_id', 'modifier_id'], 'integer'],
            [['id', 'loading_plan_start_date', 'loading_fact_start_date', 'loading_fact_end_date', 'created', 'modified'], 'safe'],
            [['comments'], 'string'],
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
        $query = TripSearch::find()
            ->select([
                'trip.*',
                "CONCAT(car.name, ', ', car.reg_number) AS car_name",
                'carType.short_name AS short_name',
                'carType.length AS length',
                'carType.width AS width',
                'carType.height AS height',
            ])
            ->joinWith('car')
            ->joinWith('car.carType as carType')
            ->where('trip.is_deleted = 0')
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
                ['like', 'trip.drivers', $this->search],
                ['like', 'car.reg_number', $this->search],
            ];
            $query->andWhere($or);
        }

        $query->andFilterWhere(['=', 'trip.id', $this->getAttribute('id')]);

        // Return data provider
        return $dataProvider;
    }
}
