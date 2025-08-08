<?php

namespace app\models;

use Yii;
use app\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%product}}".
 *
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string|null $comments Любые комментарии
 * @property int $is_deleted
 * @property string|null $created
 * @property int|null $creator_id
 * @property string|null $modified
 * @property int|null $modifier_id
 *
 * @property ProductCategory $category
 * @property User $creator
 * @property User $modifier
 * @property ProductionStage[] $productionStages
 * @property StorageProduct $storageProduct
 * @property ProductMeasureUnit[] $productMeasureUnits
 */
class Product extends BaseActiveRecord
{
    private $_productionStages = null;

    public const INSULATION_HEIGHT = 100;

    public const SHEET_TYPE_INTERNAL = 'internal';
    public const SHEET_TYPE_EXTERNAL = 'external';

    public const MM35_TYPES = [
        '35',
        'КРОВЕЛЬНОГО ММ35'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%product}}';
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->checkConsumables($insert);

        parent::afterSave($insert, $changedAttributes);

        if($this->_productionStages && is_array($this->_productionStages)) {
            foreach ($this->productionStages as $productionStage) {
                if($index = array_search($productionStage->id, $this->_productionStages)) {
                    unset($this->_productionStages[$index]);
                } else {
                    ProductWProductionStageLink::deleteAll(['production_stage_id' => $productionStage->id, 'product_id' => $this->id]);
                }
            }
            foreach ($this->_productionStages as $id) {
                if((int)$id > 0) {
                    (new ProductWProductionStageLink(['production_stage_id' => $id, 'product_id' => $this->id]))->save();
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'name'], 'required', 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
            [['category_id', 'is_deleted', 'creator_id', 'modifier_id'], 'integer'],
            [['comments'], 'string'],
            [['created', 'modified', 'productionStages'], 'safe'],
            [['name'], 'string', 'max' => 256],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductCategory::class, 'targetAttribute' => ['category_id' => 'id']],
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
            'id' => Yii::t('app', 'ID'),
            'category_id' => Yii::t('app', 'Категория'),
            'name' => Yii::t('app', 'Название'),
            'comments' => Yii::t('app', 'Комментарии'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'created' => Yii::t('app', 'Created'),
            'creator_id' => Yii::t('app', 'Creator ID'),
            'modified' => Yii::t('app', 'Modified'),
            'modifier_id' => Yii::t('app', 'Modifier ID'),
        ];
    }

    /**
     * @inheritDoc
     *
     * @return ProductQuery
     */
    public static function find(): ProductQuery
    {
        return new ProductQuery(get_called_class());
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(ProductCategory::class, ['id' => 'category_id'])->inverseOf('products');
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUnit()
    {
        return $this->hasOne(ProductMeasureUnit::class, ['product_id' => 'id']);
    }

    /**
     * Gets query for [[Creator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'creator_id'])->inverseOf('products');
    }

    /**
     * Gets query for [[Modifier]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getModifier()
    {
        return $this->hasOne(User::class, ['id' => 'modifier_id'])->inverseOf('products0');
    }

    /**
     * Gets query for [[ProductionStages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductionStages()
    {
        return $this->hasMany(ProductionStage::class, ['id' => 'production_stage_id'])->viaTable('product_w_production_stage_link', ['product_id' => 'id']);
    }

    public function setProductionStages($value) {
        $this->_productionStages = $value;
    }

    public function getProductMeasureUnits() {
        return $this->hasMany(ProductMeasureUnit::class, ['product_id' => 'id']);
    }

    /**
     * Gets query for [[StorageProduct]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStorageProduct()
    {
        return $this->hasOne(StorageProduct::class, ['product_id' => 'id']);
    }

    /**
     * Checks consumable products and relates them with storage or removes from it
     *
     * @param bool $insert
     *
     * @return void
     */
    private function checkConsumables(bool $insert)
    {
        $consumableCategoryIds = ProductCategory::find()
            ->select(['product_category.id'])
            ->joinWith('type', true, 'RIGHT JOIN')
            ->where(['product_type.name' => ProductType::TYPE_CONSUMABLE])
            ->column();

        $oldCategoryId = ArrayHelper::getValue($this->oldAttributes, 'category_id');

        $isCategoryChanged = $oldCategoryId != $this->category_id;
        $isConsumable = $this->category?->type?->name === ProductType::TYPE_CONSUMABLE;
        $isOldConsumable = in_array($oldCategoryId, $consumableCategoryIds);

        $becameConsumable = ($isCategoryChanged  || $insert) && $isConsumable;
        $wasConsumable = $isCategoryChanged && $isOldConsumable;

        $storageProduct = $this->storageProduct;

        if($becameConsumable) {
            if(!$storageProduct)
                $storageProduct = new StorageProduct(['product_id' => $this->id]);

            $storageProduct->is_deleted = 0;
            $storageProduct->save();

            return;
        } elseif($wasConsumable && $storageProduct) {
            $storageProduct->delete();

            return;
        }
    }
}
