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
 * This is the search model class for table "{{%storage_product_flow}}".
 * 
 */
class StorageProductFlowSearch extends StorageProductFlow
{
    public $product_name;
    public ?int $write_off = 0;
    public ?int $income = 0;
    public $all;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['date'], 'string'],
            [['created', 'modified', 'write_off', 'income', 'all'], 'safe'],
        ];
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
        $this->load($params);
        $this->date = $this->date ?? date('Y');

        $subQuery = StorageProductSearch::find()
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
            ->andFilterWhere(['YEAR(date)' => $this->date])
            ->groupBy(['sp.id'])
            ->params([':typeIncome' => StorageProductFlowType::TYPE_INCOME, ':typeWriteOff' => StorageProductFlowType::TYPE_WRITE_OFF]);
            

        $query = StorageProductSearch::find()
            ->select([
                'storage_product.*',
                'product.name',
                'product.is_deleted as is_product_deleted',
                'flows.income',
                'flows.write_off'
            ])
            ->joinWith('product', true, 'RIGHT JOIN')
            ->rightJoin(ProductCategory::tableName() . ' pc', 'pc.id = product.category_id') 
            ->rightJoin(ProductType::tableName() . ' pt', 'pt.id = pc.type_id')
            ->leftJoin(['flows' => $subQuery], 'flows.id = storage_product.id')
            ->andWhere(['pt.name' => ProductType::TYPE_CONSUMABLE]);
            
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
            'asc'   => ['p.name' => SORT_ASC],
            'desc'  => ['p.name' => SORT_DESC],
        ];

        if ($this->all === 'all')
            $dataProvider->pagination->pageSize = 10000; 
        
        if (!$this->validate())
            return $dataProvider;

        $query->andFilterWhere(['product_id' => $this->id])
            ->andFilterWhere(['ilike', 'product.name', $this->product_name]);

        return $dataProvider;
    }
}
