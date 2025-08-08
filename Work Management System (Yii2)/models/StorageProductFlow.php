<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%storage_product_flow}}".
 *
 * @property int $id
 * @property int $storage_product_id
 * @property int $type_id
 * @property string $date
 * @property int|null $value
 * @property string|null $comments
 * @property int $is_deleted
 * @property string|null $created
 * @property int|null $creator_id
 * @property string|null $modified
 * @property int|null $modifier_id
 * 
 * @property StorageProduct $storageProduct
 * @property Product $product
 * @property User $creator
 * @property User $modifier
 * @property StorageProductFlowType $flowType
 */
class StorageProductFlow extends BaseActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%storage_product_flow}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['storage_product_id', 'type_id'], 'unique', 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
            [['comments'], 'string'],
            [['is_deleted', 'creator_id', 'modifier_id', 'value'], 'integer'],
            [['value'], 'default', 'value' => 0],
            [['created', 'modified', 'date'], 'safe'],
            [['storage_product_id'], 'exist', 'skipOnError' => true, 'targetClass' => StorageProduct::class, 'targetAttribute' => ['storage_product_id' => 'id']],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => StorageProductFlowType::class, 'targetAttribute' => ['type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                    => Yii::t('app', 'ID'),
            'storage_product_id'    => Yii::t('app', 'Storage Product ID'),
            'type_id'               => Yii::t('app', 'Type ID'),
            'comments'              => Yii::t('app', 'Любые комментарии'),
            'date'                  => Yii::t('app', 'Дата события'),
            'value'                 => Yii::t('app', 'Значение'),
            'is_deleted'            => Yii::t('app', 'Is Deleted'),
            'created'               => Yii::t('app', 'Created'),
            'creator_id'            => Yii::t('app', 'Creator ID'),
            'modified'              => Yii::t('app', 'Modified'),
            'modifier_id'           => Yii::t('app', 'Modifier ID'),
        ];
    }

    /**
     * @inheritDoc
     * 
     * @return StorageProductFlowQuery
     */
    public static function find(): StorageProductFlowQuery
    {
        return new StorageProductFlowQuery(get_called_class());
    }

    public function beforeSave($insert)
    {
        if($this->value)
            $this->value = max($this->value, 0);

        return parent::beforeSave($insert);
    }

    /**
     * Gets query for [[StorageProduct]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStorageProduct()
    {
        return $this->hasOne(StorageProduct::class, ['id' => 'storage_product_id']);
    }

    /**
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(StorageProduct::class, ['id' => 'storage_product_id'])
            ->via('storageProduct');
    }

    /**
     * Gets query for [[StorageProductFlowType]].
     *
     * @return \yii\db\ActiveQuery
     */
   public function getFlowType()
   {
       return $this->hasOne(StorageProductFlowType::class, ['id' => 'type_id']);
   }

    /**
     * Gets query for [[Creator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'creator_id']);
    }

    /**
     * Gets query for [[Modifier]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getModifier()
    {
        return $this->hasOne(User::class, ['id' => 'modifier_id']);
    }
    
    /**
     * Adds value of income to model
     *
     * @param int $newValue
     * 
     * @return void
     */
    public function addValue(int $newValue)
    {
        $this->value ?? 0; 
        $this->value += $newValue;
    }

    /**
     * Adds value of write-off to model
     *
     * @param int $newValue
     * 
     * @return void
     */
    public function subtractValue(int $newValue)
    {
        $this->value ?? 0; 
        $this->value -= $newValue;
    }
}
