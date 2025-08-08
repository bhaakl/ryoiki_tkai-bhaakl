<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[StorageProduct]].
 */
class StorageProductQuery extends \yii\db\ActiveQuery
{    
    /**
     * Returns StorageProducts, which are not deleted
     * 
     * @return self
     */
    public function notDeleted(): self
    {
        return parent::andWhere(['storage_product.is_deleted' => 0]);
    }
}
