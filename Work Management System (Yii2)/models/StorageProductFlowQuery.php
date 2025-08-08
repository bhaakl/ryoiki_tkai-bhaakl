<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[StorageProductFlow]].
 */
class StorageProductFlowQuery extends \yii\db\ActiveQuery
{    
    /**
     * Returns income value query from storage
     * 
     * @return self
     */
    public function income(): self
    {
        return parent::addSelect(['SUM(value) as income'])
            ->leftJoin(StorageProductFlowType::tableName(), StorageProductFlowType::tableName().'.id = '.StorageProductFlow::tableName().'.type_id')
            ->andWhere([StorageProductFlowType::tableName().'.name' => StorageProductFlowType::TYPE_INCOME]);
    }

    /**
     * Returns write-off value query wrom storage
     * 
     * @return self
     */
    public function writeOff(): self
    {
        return parent::addSelect(['SUM(value) as write-off'])
            ->leftJoin(StorageProductFlowType::tableName(), StorageProductFlowType::tableName().'.id = '.StorageProductFlow::tableName().'.type_id')
            ->andWhere([StorageProductFlowType::tableName().'.name' => StorageProductFlowType::TYPE_WRITE_OFF]);
    }
}
