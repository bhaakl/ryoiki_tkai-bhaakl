<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%insulation_thickness}}".
 *
 * @property int $id
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
class InsulationThickness extends BaseActiveRecord
{
	public $search = '';
	
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%insulation_thickness}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required', 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
            [['id', 'is_deleted', 'creator_id', 'modifier_id'], 'integer'],
            [['comments'], 'string'],
            [['created', 'modified'], 'safe'],
            [['id'], 'unique'],
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
            'id' => Yii::t('app', 'Толщина, мм'),
            'comments' => Yii::t('app', 'Comments'),
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
        return $this->hasOne(User::class, ['id' => 'creator_id'])->inverseOf('insulationThicknesses');
    }

    /**
     * Gets query for [[Modifier]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getModifier()
    {
        return $this->hasOne(User::class, ['id' => 'modifier_id'])->inverseOf('insulationThicknesses0');
    }

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Custom Methods
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
    /**
     * {@inheritdoc}
     */
    public function search($params, $recsOnPage = 20)
    {
        $dataProvider = parent::search($params, $recsOnPage);

        $query = $dataProvider->query;
        $this->search = trim($this->search);
        
		if ($this->search) {
            $or = [
                'or',
                ['like', 'id', $this->search],
                ['like', 'comments', $this->search],
            ];
            $query->andWhere($or);
        }
        if ($this->id) {
            $query->andWhere(['like', 'id', $this->id]);
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
