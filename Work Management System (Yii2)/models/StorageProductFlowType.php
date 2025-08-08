<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%storage_product_flow_type}}".
 *
 * @property int $id
 * @property string $name
 * @property string|null $comments
 * @property int $is_deleted
 * @property string|null $created
 * @property int|null $creator_id
 * @property string|null $modified
 * @property int|null $modifier_id
 * 
 * @property StorageProductFlow[] $storageProductFlows
 * @property User $creator
 * @property User $modifier
 */
class StorageProductFlowType extends BaseActiveRecord
{

    public const TYPE_INCOME = 'Поступления';
    public const TYPE_WRITE_OFF = 'Списания';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%storage_product_flow_type}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'unique', 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
            [['comments', 'name'], 'string'],
            [['is_deleted', 'creator_id', 'modifier_id'], 'integer'],
            [['created', 'modified'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                    => Yii::t('app', 'ID'),
            'name'                  => Yii::t('app', 'Name'),
            'comments'              => Yii::t('app', 'Любые комментарии'),
            'is_deleted'            => Yii::t('app', 'Is Deleted'),
            'created'               => Yii::t('app', 'Created'),
            'creator_id'            => Yii::t('app', 'Creator ID'),
            'modified'              => Yii::t('app', 'Modified'),
            'modifier_id'           => Yii::t('app', 'Modifier ID'),
        ];
    }

    /**
     * Gets query for [[StorageProductFlow]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStorageProductFlow()
    {
        return $this->hasMany(StorageProductFlow::class, ['id' => 'storage_product_flow_id']);
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
     * Returns flow type INCOME
     *
     * @return StorageProductFlowType|null
     */
    public static function getIncomeType()
    {
        return self::findOne(['name' => SELF::TYPE_INCOME]);
    }

    /**
     * Returns flow type WRITE_OFF
     *
     * @return StorageProductFlowType|null
     */
    public static function getWriteOffType()
    {
        return self::findOne(['name' => SELF::TYPE_WRITE_OFF]);
    }
}
