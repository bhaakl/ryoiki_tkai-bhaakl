<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%measure_unit}}".
 *
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property int|null $decimal_places Количество знаков после запятой
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
class MeasureUnit extends BaseActiveRecord
{
	public $search = '';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%measure_unit}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'short_name'], 'required', 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
            [['decimal_places', 'is_deleted', 'creator_id', 'modifier_id'], 'integer'],
            [['comments'], 'string'],
            [['created', 'modified'], 'safe'],
            [['name'], 'string', 'max' => 64],
            [['short_name'], 'string', 'max' => 16],
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
            'decimal_places' => Yii::t('app', 'Десятичные знаки'),
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
        return $this->hasOne(User::class, ['id' => 'creator_id'])->inverseOf('measureUnits');
    }

    /**
     * Gets query for [[Modifier]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getModifier()
    {
        return $this->hasOne(User::class, ['id' => 'modifier_id'])->inverseOf('measureUnits0');
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

    public function getUnitFullName()
    {
        return "($this->short_name.) ".$this->name;
    }

}
