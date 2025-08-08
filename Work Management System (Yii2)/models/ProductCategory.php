<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%product_category}}".
 *
 * @property int $id
 * @property int $type_id
 * @property string $name
 * @property string $short_name
 * @property string|null $comments Любые комментарии
 * @property int $is_deleted
 * @property string|null $created
 * @property int|null $creator_id
 * @property string|null $modified
 * @property int|null $modifier_id
 *
 * @property User $creator
 * @property User $modifier
 * @property Product[] $products
 * @property ProductType $type
 */
class ProductCategory extends BaseActiveRecord
{
    public const WEIGHT_FORMULA_TYPE_NONE = 0;
    public const WEIGHT_FORMULA_TYPE_FLAT = 1;
    public const WEIGHT_FORMULA_TYPE_SANDWICH = 2;

	public $search = '';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%product_category}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type_id', 'name', 'short_name'], 'required', 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
            [['type_id', 'weight_formula_type', 'is_deleted', 'creator_id', 'modifier_id'], 'integer'],
            [['comments'], 'string'],
            [['created', 'modified'], 'safe'],
            [['name'], 'string', 'max' => 256],
            [['short_name'], 'string', 'max' => 64],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['creator_id' => 'id']],
            [['modifier_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['modifier_id' => 'id']],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductType::class, 'targetAttribute' => ['type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'weight_formula_type' => Yii::t('app', 'Формула расчета веса'),
            'type_id' => Yii::t('app', 'Тип'),
            'name' => Yii::t('app', 'Наименование'),
            'short_name' => Yii::t('app', 'Сокращение'),
            'comments' => Yii::t('app', 'Комментарий'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'created' => Yii::t('app', 'Created'),
            'creator_id' => Yii::t('app', 'Creator ID'),
            'modified' => Yii::t('app', 'Modified'),
            'modifier_id' => Yii::t('app', 'Modifier ID'),

			'search' => Yii::t('app', 'Поиск'),
        ];
    }

    /**
     * Gets query for [[Creator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'creator_id'])->inverseOf('productCategories');
    }

    /**
     * Gets query for [[Modifier]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getModifier()
    {
        return $this->hasOne(User::class, ['id' => 'modifier_id'])->inverseOf('productCategories0');
    }

    /**
     * Gets query for [[Products]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::class, ['category_id' => 'id'])->inverseOf('category');
    }

    /**
     * Gets query for [[Type]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(ProductType::class, ['id' => 'type_id'])->inverseOf('productCategories');
    }

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Custom Methods
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * {@inheritdoc}
     */
    public function search($params, $recsOnPage = 20)
    {
        if(key_exists('per-page', $params)) {
            $recsOnPage = (int)$params['per-page'];
        }
        $dataProvider = parent::search($params, $recsOnPage);

        $query = $dataProvider->query;
        $this->search = trim($this->search);

		if ($this->search) {
            $or = [
                'or',
                ['like', 'name', $this->search],
                ['like', 'comments', $this->search],
            ];
            $query->andWhere($or);
        }
        if ($this->name) {
            $query->andWhere(['like', 'name', $this->name]);
        }
		if ($this->comments) {
            $query->andWhere(['like', 'comments', $this->comments]);
        }

		// Return data provider
        return $dataProvider;
    }

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// End:
	// Custom Methods
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
