<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use app\models\ProductType;
use app\models\StorageProduct;
use app\models\ProductCategory;
use yii\data\ActiveDataProvider;
use app\models\StorageProductFlow;
use app\models\StorageProductFlowType;

/**
 * This is the search model class for table "{{%storage_product}}".
 */
class StorageProductSearch extends StorageProduct
{
    public $product_name;
    public $availability_status;
    public $all;
    public ?int $write_off = 0;
    public ?int $income = 0;
    public ?int $deficit = 0;
    public ?int $remains = 0;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['comments', 'product_name'], 'string'],
            [['id', 'workers_count', 'workers_norm'], 'integer'],
            [['created', 'modified', 'all'], 'safe'],
            [['income', 'write_off', 'deficit', 'remains', 'availability_status'], 'safe'], 
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'product_name'          => Yii::t('app', 'Название расходника'),
            'availability_status'   => Yii::t('app', 'Статус'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $recsOnPage = 20)
    {
        $subQuery = self::find()
            ->alias('sp')
            ->select([
                'sp.id',
                'income' => new \yii\db\Expression("SUM(CASE WHEN spft.name = :typeIncome THEN spf.value ELSE 0 END)"),
                'write_off' => new \yii\db\Expression("SUM(CASE WHEN spft.name = :typeWriteOff THEN spf.value ELSE 0 END)"),
            ])
            ->joinWith('product', true, 'RIGHT JOIN')
            ->rightJoin(ProductCategory::tableName() . ' pc', 'pc.id = product.category_id') 
            ->rightJoin(ProductType::tableName() . ' pt', 'pt.id = pc.type_id') 
            ->rightJoin(StorageProductFlow::tableName() . ' spf', 'sp.id = spf.storage_product_id') 
            ->rightJoin(StorageProductFlowType::tableName() . ' spft', 'spf.type_id = spft.id') 
            ->andWhere(['pt.name' => ProductType::TYPE_CONSUMABLE])
            ->groupBy(['sp.id'])
            ->params([':typeIncome' => StorageProductFlowType::TYPE_INCOME, ':typeWriteOff' => StorageProductFlowType::TYPE_WRITE_OFF]);


        $query = self::find()
            ->select([
                'storage_product.*',
                'product.name',
                'product.is_deleted as is_product_deleted',
                'flows.income',
                'flows.write_off',
            ])
            ->joinWith('product', true, 'RIGHT JOIN')
            ->leftJoin(ProductCategory::tableName() . ' pc', 'pc.id = product.category_id') 
            ->leftJoin(ProductType::tableName() . ' pt', 'pt.id = pc.type_id') 
            ->leftJoin(['flows' => $subQuery], 'flows.id = storage_product.id')
            ->andWhere(['pt.name' => ProductType::TYPE_CONSUMABLE]);

        $query = self::find()
            ->select([
                '*', 
                'GREATEST(income - write_off, 0) as remains', 
                'storage_product.workers_norm - GREATEST(income - write_off, 0) as deficit'
            ])
            ->from(['storage_product' => $query]);
            
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC
                ],
            ],
            'pagination' => $recsOnPage > 0 ? [
                'defaultPageSize' => $recsOnPage,
            ] : false,
        ]);

        $dataProvider->sort->attributes['product_name'] = [
            'asc'   => ['product.name' => SORT_ASC],
            'desc'  => ['product.name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['income'] = [
            'asc'   => ['income' => SORT_ASC],
            'desc'  => ['income' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['write_off'] = [
            'asc'   => ['write_off' => SORT_ASC],
            'desc'  => ['write_off' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['deficit'] = [
            'asc'   => ['deficit' => SORT_ASC],
            'desc'  => ['deficit' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['remains'] = [
            'asc'   => ['remains' => SORT_ASC],
            'desc'  => ['remains' => SORT_DESC],
        ];

        $this->load($params);

        if ($this->all === 'all')
            $dataProvider->pagination->pageSize = 10000; 
        
        if (!$this->validate())
            return $dataProvider;

        $query->andFilterWhere(['product_id' => $this->id])
            ->andFilterWhere(['ilike', 'product.name', $this->product_name]);

        if ($this->availability_status === 'in_stock') {
            $query->andHaving(['>', 'remains', 0]);
        } elseif ($this->availability_status === 'below_norm') {
            $query->andHaving(['>', 'deficit', 0]);
        } elseif ($this->availability_status === 'out_of_stock') {
            $query->andHaving(['remains' => 0]);
        }

        $query->andWhere(['is_product_deleted' => 0]);

        return $dataProvider;
    }
}
