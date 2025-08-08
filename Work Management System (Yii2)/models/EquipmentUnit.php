<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%equipment_unit}}".
 *
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string|null $comments Любые комментарии
 * @property int|null $used_on_mro Используется на странице "ТО"
 * @property int|null $used_on_equipment_repair Используется на странице "Ремонт оборудования"
 * @property int|null $used_on_loader_repair Используется на странице "Ремонт погрузчика"
 * @property int $is_deleted
 * @property string|null $created
 * @property int|null $creator_id
 * @property string|null $modified
 * @property int|null $modifier_id
 *
 * @property EquipmentCategory $category
 * @property User $creator
 * @property User $modifier
 */
class EquipmentUnit extends BaseActiveRecord
{
    const PAGE_NAMES = [
        'used_on_mro' => 'ТО',
        'used_on_equipment_repair' => 'Ремонт оборудования',
        'used_on_loader_repair' => 'Ремонт погрузчика'
    ];
    public $pages;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%equipment_unit}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'name'], 'required', 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
            [['category_id', 'used_on_mro', 'used_on_equipment_repair', 'used_on_loader_repair', 'is_deleted', 'creator_id', 'modifier_id'], 'integer'],
            [['comments'], 'string'],
            [['created', 'modified'], 'safe'],
            [['name'], 'string', 'max' => 256],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => EquipmentCategory::class, 'targetAttribute' => ['category_id' => 'id']],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['creator_id' => 'id']],
            [['modifier_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['modifier_id' => 'id']],
            [['pages'], 'safe']
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
            'used_on_mro' => Yii::t('app', 'ТО'),
            'used_on_equipment_repair' => Yii::t('app', 'Ремонт оборудования'),
            'used_on_loader_repair' => Yii::t('app', 'Ремонт погрузчика'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'created' => Yii::t('app', 'Created'),
            'creator_id' => Yii::t('app', 'Creator ID'),
            'modified' => Yii::t('app', 'Modified'),
            'modifier_id' => Yii::t('app', 'Modifier ID'),
            'pages' => Yii::t('app', 'Страницы'),
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(EquipmentCategory::class, ['id' => 'category_id'])->inverseOf('equipmentUnits');
    }

    /**
     * Gets query for [[Creator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'creator_id'])->inverseOf('equipmentUnits');
    }

    /**
     * Gets query for [[Modifier]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getModifier()
    {
        return $this->hasOne(User::class, ['id' => 'modifier_id'])->inverseOf('equipmentUnits0');
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

		if ($this->category_id) {
            $query->andWhere(['category_id' => $this->category_id]);
        }
		if ($this->name) {
            $query->andWhere(['like', 'name', $this->name]);
        }
		if ($this->comments) {
            $query->andWhere(['like', 'comments', $this->comments]);
        }

        if(is_array($this->pages)) {
            $search_items = ['or'];
            foreach ($this->pages as $page) {
                if($this->hasAttribute($page)) {
                    $search_items[] = [$page => 1];
                }
            }
            if(count($search_items) > 1) {
                $query->andWhere($search_items);
            }
        }

		// Return data provider
        return $dataProvider;
    }

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// End:
	// Custom Methods
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
