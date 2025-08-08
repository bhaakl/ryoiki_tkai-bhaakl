<?php

namespace app\models;

use app\base\Cache;
use Yii;

/**
 * This is the model class for table "{{%product_type}}".
 *
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property int|null $has_production_stages 1 - имеет стадии производства; 0 или null - не имеет
 * @property string|null $comments Любые комментарии
 * @property int $is_deleted
 * @property string|null $created
 * @property int|null $creator_id
 * @property string|null $modified
 * @property int|null $modifier_id
 *
 * @property User $creator
 * @property User $modifier
 * @property ProductCategory[] $productCategories
 */
class ProductType extends BaseActiveRecord
{
	public $search = '';

    public const TYPE_CONSUMABLE = 'Расходники';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%product_type}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'short_name'], 'required', 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
            [['has_production_stages', 'is_deleted', 'creator_id', 'modifier_id'], 'integer'],
            [['comments'], 'string'],
            [['created', 'modified'], 'safe'],
            [['name'], 'string', 'max' => 256],
            [['short_name'], 'string', 'max' => 32],
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
            'name' => Yii::t('app', 'Наименование'),
            'short_name' => Yii::t('app', 'Сокращение'),
            'has_production_stages' => Yii::t('app', 'Маршрут'),
            'comments' => Yii::t('app', 'Описание'),
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
        return $this->hasOne(User::class, ['id' => 'creator_id'])->inverseOf('productTypes');
    }

    /**
     * Gets query for [[Modifier]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getModifier()
    {
        return $this->hasOne(User::class, ['id' => 'modifier_id'])->inverseOf('productTypes0');
    }

    /**
     * Gets query for [[ProductCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductCategories()
    {
        return $this->hasMany(ProductCategory::class, ['type_id' => 'id'])->inverseOf('type');
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

    public static function getList($full = false)
    {
        return Cache::getOrSetCache(static::tableName(), $full ? 'list_full' : 'list', function() use($full) {
            $qry = $full ? static::find() : static::findActive();
            $tmp = [];
            foreach($qry->each(1) as $model) {
                $tmp[$model->id] = (string)$model;
            }
            asort($tmp);
            return $tmp;
        }, 0, true);
    }

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// End:
	// Custom Methods
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
