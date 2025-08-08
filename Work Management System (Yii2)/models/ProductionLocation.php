<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%production_location}}".
 *
 * @property int $id
 * @property int|null $category_id
 * @property string $name
 * @property string|null $comments Любые комментарии
 * @property int $is_deleted
 * @property string|null $created
 * @property int|null $creator_id
 * @property string|null $modified
 * @property int|null $modifier_id
 *
 * @property ProductionLocationCategory $category
 * @property User $creator
 * @property User $modifier
 */
class ProductionLocation extends BaseActiveRecord
{
	public $search = '';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%production_location}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'is_deleted', 'creator_id', 'modifier_id'], 'integer'],
            [['category_id', 'name'], 'required', 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
            [['comments'], 'string'],
            [['created', 'modified'], 'safe'],
            [['name'], 'string', 'max' => 256],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductionLocationCategory::class, 'targetAttribute' => ['category_id' => 'id']],
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
            'name' => Yii::t('app', 'Наименование'),
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
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(ProductionLocationCategory::class, ['id' => 'category_id'])->inverseOf('productionLocations');
    }

    /**
     * Gets query for [[Creator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'creator_id'])->inverseOf('productionLocations');
    }

    /**
     * Gets query for [[Modifier]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getModifier()
    {
        return $this->hasOne(User::class, ['id' => 'modifier_id'])->inverseOf('productionLocations0');
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
        $query->andFilterWhere(['category_id' => $this->category_id]);
        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'comments', $this->comments]);

		// Return data provider
        return $dataProvider;
    }

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// End:
	// Custom Methods
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
