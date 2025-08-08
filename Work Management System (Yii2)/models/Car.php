<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%car}}".
 *
 * @property int $id
 * @property int $type_id
 * @property int|null $possession_type_id
 * @property string $reg_number Регистрационный номер автомобиля
 * @property string $name
 * @property string|null $drivers Водители
 * @property string|null $comments Любые комментарии
 * @property int $is_deleted
 * @property string|null $created
 * @property int|null $creator_id
 * @property string|null $modified
 * @property int|null $modifier_id
 *
 * @property User $creator
 * @property User $modifier
 * @property CarPossessionType $possessionType
 * @property CarType $type
 */
class Car extends BaseActiveRecord
{
	public $search = '';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%car}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type_id', 'reg_number', 'name'], 'required', 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
            [['type_id', 'possession_type_id', 'is_deleted', 'creator_id', 'modifier_id'], 'integer'],
            [['drivers', 'comments'], 'string'],
            [['created', 'modified'], 'safe'],
            [['reg_number'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 256],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['creator_id' => 'id']],
            [['modifier_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['modifier_id' => 'id']],
            [['possession_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => CarPossessionType::class, 'targetAttribute' => ['possession_type_id' => 'id']],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => CarType::class, 'targetAttribute' => ['type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type_id' => Yii::t('app', 'Тип'),
            'possession_type_id' => Yii::t('app', 'Possession Type ID'),
            'reg_number' => Yii::t('app', 'Рег. номер'),
            'name' => Yii::t('app', 'Название'),
            'drivers' => Yii::t('app', 'Drivers'),
            'comments' => Yii::t('app', 'Комментарий'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'created' => Yii::t('app', 'Created'),
            'creator_id' => Yii::t('app', 'Creator ID'),
            'modified' => Yii::t('app', 'Modified'),
            'modifier_id' => Yii::t('app', 'Modifier ID'),

			'search' => Yii::t('app', 'Поиск'),
        ];
    }

    public function getCarType()
    {
        return $this->hasOne(CarType::class, ['id' => 'type_id'])->inverseOf('cars');
    }

    /**
     * Gets query for [[Creator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'creator_id'])->inverseOf('cars');
    }

    /**
     * Gets query for [[Modifier]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getModifier()
    {
        return $this->hasOne(User::class, ['id' => 'modifier_id'])->inverseOf('cars0');
    }

    /**
     * Gets query for [[PossessionType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPossessionType()
    {
        return $this->hasOne(CarPossessionType::class, ['id' => 'possession_type_id'])->inverseOf('cars');
    }

    /**
     * Gets query for [[Type]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(CarType::class, ['id' => 'type_id'])->inverseOf('cars');
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

		if ($this->type_id) {
            $query->andWhere(['type_id' => $this->type_id]);
        }
		if ($this->possession_type_id) {
            $query->andWhere(['possession_type_id' => $this->possession_type_id]);
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

    static public function getCountsByTypeString($shortInfoString, $carTypes = [])
    {
        if (empty($shortInfoString)) {
            return '';
        }

        $groups = array_map('trim', explode(',', $shortInfoString));

        $result = [];

        foreach ($groups as $group) {
            if (!empty($group)) {
                if (preg_match('/^(\d*)(\D)$/u', $group, $matches)) {
                    $count = !empty($matches[1]) ? (int)$matches[1] : 1;
                    $symbol = $matches[2];

                    $result[] = sprintf('%s: %d шт.', $carTypes[$symbol] ?? '', $count);
                }
            }
        }

        return implode(', ', $result);
    }

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// End:
	// Custom Methods
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
