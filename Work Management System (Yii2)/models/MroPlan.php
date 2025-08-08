<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mro_plan".
 *
 * @property int $id
 * @property int $equipment_unit_id
 * @property int $mro_work_type_id
 * @property int $year
 * @property string|null $plan_date Плановая дата
 * @property string|null $fact_date Фактическая дата
 * @property string|null $comments
 * @property string|null $created
 * @property int|null $creator_id
 * @property string|null $modified
 * @property int|null $modifier_id
 *
 * @property EquipmentUnit $equipmentUnit
 * @property MroWorkType $mroWorkType
 * @property User $creator
 * @property User $modifier
 */
class MroPlan extends BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mro_plan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['plan_date', 'fact_date', 'comments', 'created', 'creator_id', 'modified', 'modifier_id'], 'default', 'value' => null],
            [['equipment_unit_id', 'mro_work_type_id', 'year'], 'required'],
            [['equipment_unit_id', 'mro_work_type_id', 'year', 'creator_id', 'modifier_id'], 'integer'],
            [['plan_date', 'fact_date', 'created', 'modified'], 'safe'],
            [['comments'], 'string'],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['creator_id' => 'id']],
            [['equipment_unit_id'], 'exist', 'skipOnError' => true, 'targetClass' => EquipmentUnit::class, 'targetAttribute' => ['equipment_unit_id' => 'id']],
            [['modifier_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['modifier_id' => 'id']],
            [['mro_work_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => MroWorkType::class, 'targetAttribute' => ['mro_work_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'equipment_unit_id' => Yii::t('app', 'Equipment Unit ID'),
            'mro_work_type_id' => Yii::t('app', 'Mro Work Type ID'),
            'year' => Yii::t('app', 'Год'),
            'plan_date' => Yii::t('app', 'Плановая дата'),
            'fact_date' => Yii::t('app', 'Фактическая дата'),
            'comments' => Yii::t('app', 'Comments'),
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
     * Gets query for [[EquipmentUnit]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEquipmentUnit()
    {
        return $this->hasOne(EquipmentUnit::class, ['id' => 'equipment_unit_id']);
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
     * Gets query for [[MroWorkType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMroWorkType()
    {
        return $this->hasOne(MroWorkType::class, ['id' => 'mro_work_type_id']);
    }

}
