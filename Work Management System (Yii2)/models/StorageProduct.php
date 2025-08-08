<?php

namespace app\models;

use Yii;
use app\models\Product;

/**
 * This is the model class for table "{{%storage_product}}".
 *
 * @property int $id
 * @property int $product_id
 * @property int|null $workers_norm
 * @property string|null $measure_units
 * @property string|null $product_norm
 * @property string|null $periodicity
 * @property string|null $package
 * @property int|null $workers_count
 * @property string|null $comments
 * @property int $is_deleted
 * @property string|null $created
 * @property int|null $creator_id
 * @property string|null $modified
 * @property int|null $modifier_id
 * 
 * @property-read int|null $workersCountPerMonth
 * 
 * @property Product $product
 * @property User $creator
 * @property User $modifier
 */
class StorageProduct extends BaseActiveRecord
{

    public const WORKING_DAYS = 26;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%storage_product}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id'], 'unique', 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
            [['comments'], 'string'],
            [['is_deleted', 'creator_id', 'modifier_id', 'workers_count', 'workers_norm'], 'integer'],
            [['created', 'modified'], 'safe'],
            [['periodicity', 'measure_units', 'package', 'product_norm'], 'string', 'max' => 127],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['creator_id' => 'id']],
            [['modifier_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['modifier_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                => Yii::t('app', 'ID'),
            'product_id'        => Yii::t('app', 'Product ID'),
            'comments'          => Yii::t('app', 'Любые комментарии'),
            'measure_units'     => Yii::t('app', 'ЕИ'),
            'product_norm'      => Yii::t('app', 'Норма'),
            'periodicity'       => Yii::t('app', 'Периодичность'),
            'workers_norm'      => Yii::t('app', 'Норма'),
            'workers_count'     => Yii::t('app', 'Рабочие'),
            'package'           => Yii::t('app', 'Упаковка'),
            'is_deleted'        => Yii::t('app', 'Is Deleted'),
            'created'           => Yii::t('app', 'Created'),
            'creator_id'        => Yii::t('app', 'Creator ID'),
            'modified'          => Yii::t('app', 'Modified'),
            'modifier_id'       => Yii::t('app', 'Modifier ID'),
        ];
    }

    /**
     * @inheritDoc
     * 
     * @return StorageProductQuery
     */
    public static function find(): StorageProductQuery
    {
        return new StorageProductQuery(get_called_class());
    }

    /**
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
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
     * Returns `workers_count` field multiplied by 28
     *
     * @return void
     */
    public function getWorkersCountPerMonth()
    {
        return isset($this->workers_count)
            ? $this->workers_count * self::WORKING_DAYS
            : null; 
    }
    
    /**
     * @param int $value 
     * 
     * @return bool
     */
    public function addIncome(int $value): bool
    {
        $typeIncome = StorageProductFlowType::findOne(['name' => StorageProductFlowType::TYPE_INCOME]);
        if(!$storageProductFlow = StorageProductFlow::findOne(['type_id' => $typeIncome->id, 'storage_product_id' => $this->id]))
            $storageProductFlow = new StorageProductFlow([
                'type_id'               => $typeIncome->id,
                'storage_product_id'    => $this->id
            ]);

        $storageProductFlow->addValue($value);

        return $storageProductFlow->save();
    }

    /**
     * @param int $value 
     * 
     * @return bool
     */
    public function addWriteOff(int $value): bool
    {
        $typeWriteOff = StorageProductFlowType::findOne(['name' => StorageProductFlowType::TYPE_WRITE_OFF]);
        if(!$storageProductFlow = StorageProductFlow::findOne(['type_id' => $typeWriteOff->id, 'storage_product_id' => $this->id]))
            $storageProductFlow = new StorageProductFlow([
                'type_id'               => $typeWriteOff->id,
                'storage_product_id'    => $this->id
            ]);

        $storageProductFlow->addValue($value);

        return $storageProductFlow->save();
    }
    
}
