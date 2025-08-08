<?php

namespace app\models;

use Itguild\Debug\Debug;
use Yii;

/**
 * This is the model class for table "{{%production_stage}}".
 *
 * @property int $id
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
 */
class ProductionStage extends BaseActiveRecord
{
    public const STAGE_INSULATION = 'insulation'; // Утеплитель
    public const STAGE_SHEET = 'sheet'; // Внутренний/Внешний лист
    public const STAGE_SANDWICH = 'sandwich'; // Сэндвич-панель
    public const STAGE_MM35 = 'mm35'; // ММ 35
    public const STAGE_ARCH4521 = 'arch4521'; // Арка 45/21
    public const STAGE_BENDING = 'bending'; // Гибка
    public const STAGE_PL = 'pl'; // ПЛ

    public const AVAILABLE_STAGES = [
        self::STAGE_INSULATION,
        self::STAGE_SHEET,
        self::STAGE_SANDWICH,
        self::STAGE_MM35,
        self::STAGE_ARCH4521,
        self::STAGE_BENDING,
        self::STAGE_PL,
    ];

	public $search = '';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%production_stage}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'short_name'], 'required', 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
            [['comments'], 'string'],
            [['is_deleted', 'creator_id', 'modifier_id'], 'integer'],
            [['created', 'modified'], 'safe'],
            [['name'], 'string', 'max' => 256],
            [['short_name'], 'string', 'max' => 32],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['creator_id' => 'id']],
            [['modifier_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['modifier_id' => 'id']],
            [['name', 'comments'], 'safe', 'on' => BaseActiveRecord::SCENARIO_SEARCH],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', '№ Этапа'),
            'name' => Yii::t('app', 'Наименование этапа'),
            'short_name' => Yii::t('app', 'Описание этапа'),
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
        return $this->hasOne(User::class, ['id' => 'creator_id'])->inverseOf('productionStages');
    }

    /**
     * Gets query for [[Modifier]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getModifier()
    {
        return $this->hasOne(User::class, ['id' => 'modifier_id'])->inverseOf('productionStages0');
    }

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Custom Methods
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * {@inheritdoc}
     */
    public function search($params, $recsOnPage = 20)
    {
        $query = self::find()->where(['is_deleted' => 0]);

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => $recsOnPage ? ['pageSize' => $recsOnPage] : false,
            'sort' => ['defaultOrder' => ['id' => SORT_ASC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $this->search = trim($this->search);

        if ($this->search) {
            $query->andWhere([
                'or',
                ['like', 'name', $this->search],
                ['like', 'comments', $this->search],
            ]);
        }

        if ($this->name) {
            $query->andWhere(['like', 'name', $this->name]);
        }

        if ($this->comments) {
            $query->andWhere(['like', 'comments', $this->comments]);
        }

        return $dataProvider;
    }

    /**
     * @return array 
     */
    public static function unitLabels(): array
    {
        return [
            'unit_count'       => 'шт.',   // штуки
            'number_of_square'=> 'кв.',    // квадратные метры
            'number_of_hours'=> 'ч.',    // часы
            'number_of_min'=> 'мин.',    // часы
            'mass_quantity_kg'=> 'кг.',    // килограммы
        ];
    }

    /**
     * @return string (e.g. "123 шт.", "45 кв.")
     */
    public static function formatUnit(int $value, string $field): string
    {
        $labels = static::unitLabels();
        $label  = $labels[$field] ?? '';
        return $value . ($label ? ' ' . $label : '');
    }

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// End:
	// Custom Methods
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
