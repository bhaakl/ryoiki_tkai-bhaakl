<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_w_production_stage_link".
 *
 * @property int $id
 * @property int $production_stage_id
 * @property int $product_id
 * @property string|null $comments Любые комментарии
 * @property int $is_deleted
 * @property string|null $created
 * @property int|null $creator_id
 * @property string|null $modified
 * @property int|null $modifier_id
 *
 * @property User $creator
 * @property User $modifier
 * @property Product $product
 * @property ProductionStage $productionStage
 */
class ProductWProductionStageLink extends BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_w_production_stage_link';
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'production_stage_name',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['comments', 'created', 'creator_id', 'modified', 'modifier_id'], 'default', 'value' => null],
            [['is_deleted'], 'default', 'value' => 0],
            [['production_stage_id', 'product_id'], 'required'],
            [['production_stage_id', 'product_id', 'is_deleted', 'creator_id', 'modifier_id'], 'integer'],
            [['comments'], 'string'],
            [['created', 'modified'], 'safe'],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['creator_id' => 'id']],
            [['modifier_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['modifier_id' => 'id']],
            [['production_stage_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductionStage::class, 'targetAttribute' => ['production_stage_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
            [['id', 'production_stage_name'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'production_stage_id' => Yii::t('app', 'Production Stage ID'),
            'product_id' => Yii::t('app', 'Product ID'),
            'comments' => Yii::t('app', 'Любые комментарии'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'created' => Yii::t('app', 'Created'),
            'creator_id' => Yii::t('app', 'Creator ID'),
            'modified' => Yii::t('app', 'Modified'),
            'modifier_id' => Yii::t('app', 'Modifier ID'),
        ];
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
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * Gets query for [[ProductionStage]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductionStage()
    {
        return $this->hasOne(ProductionStage::class, ['id' => 'production_stage_id']);
    }
}
