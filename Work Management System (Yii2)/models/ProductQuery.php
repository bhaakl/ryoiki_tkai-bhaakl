<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[product]].
 */
class ProductQuery extends \yii\db\ActiveQuery
{    
    /**
     * Returns products, which has type CONSUMABLE
     * 
     * @return self
     */
    public function onlyConsumables(): self
    {
        return parent::rightJoin(ProductCategory::tableName() . ' pc', 'pc.id = '.Product::tableName().'.category_id') 
            ->rightJoin(ProductType::tableName() . ' pt', 'pt.id = pc.type_id') 
            ->andWhere(['pt.name' => ProductType::TYPE_CONSUMABLE]);
    }

    /**
     * Returns products, which are not deleted
     * 
     * @return self
     */
    public function notDeleted(): self
    {
        return parent::andWhere(['product.is_deleted' => 0]);
    }
}
