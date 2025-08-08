<?php

namespace app\models\search;

use app\models\Product;
use app\models\ProductCategory;
use app\models\ProductWProductionStageLink;
use app\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Модель для поиска Product
 */
class ProductSearch extends Product
{
    public $search = '';

    public $type_id;

    public function rules()
    {
        return [
            [['category_id', 'is_deleted', 'creator_id', 'modifier_id', 'type_id'], 'integer'],
            [['comments'], 'string'],
            [['created', 'modified'], 'safe'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductCategory::class, 'targetAttribute' => ['category_id' => 'id']],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['creator_id' => 'id']],
            [['modifier_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['modifier_id' => 'id']],
            [['category.type_id', 'productType.name', 'productCategory.short_name', 'productStages', 'search'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'search' => Yii::t('app', 'Поиск'),
            'type_id' => Yii::t('app', 'Тип'),
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
        if(key_exists('per-page', $params)) {
            $recsOnPage = (int)$params['per-page'];
        }
        $subQuery = ProductWProductionStageLink::find()
            ->select(['product_id', 'GROUP_CONCAT(production_stage.name SEPARATOR ",") as production_stages'])
            ->joinWith('productionStage')
            ->groupBy('product_id');

        $query = ProductSearch::find()
            ->select(['product.*', 'stages.production_stages as productStages'])
            ->joinWith('category productCategory')
            ->joinWith('category.type as productType')
            ->leftJoin(['stages' => $subQuery], 'product.id = stages.product_id')
            ->where('product.is_deleted = 0')
            //->andWhere(['productCategory.is_deleted' => 0])
            //->andWhere(['productType.is_deleted' => 0])
        ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => $this->sortingEnabled ? ['defaultOrder' => $this->defaultOrder] : false,
            'pagination' => $recsOnPage > 0 ? [
                'defaultPageSize' => $recsOnPage,
            ] : false,
        ]);

        $dataProvider->sort->attributes['category.type_id'] = [
            'asc' => ['productType.name' => SORT_ASC],
            'desc' => ['productType.name' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query = $dataProvider->query;
        $this->search = trim($this->search);

        if ($this->search) {
            $or = [
                'or',
                ['like', 'product.name', $this->search],
                ['like', 'product.comments', $this->search],
                ['like', 'productCategory.name', $this->search],
//                ['like', 'stages.production_stages', $this->search],
            ];
            $query->andWhere($or);
        }

        $query->andFilterWhere(['=', 'productCategory.type_id', $this->type_id]);
        $query->andFilterWhere(['=', 'category_id', $this->category_id]);

        // Return data provider
        return $dataProvider;
    }
}
