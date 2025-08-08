<?php

namespace app\models;

use app\helpers\Date as DateHelper;
use Yii;

/**
 * This is the model class for table "equipment_repair".
 *
 * @property int $id
 * @property int $equipment_unit_id
 * @property string|null $when_broken Когда вышло из строя
 * @property string|null $defect Что вышло из строя
 * @property string|null $reason Причина выхода из строя
 * @property string|null $when_repaired Когда отремонтировано
 * @property int|null $downtime
 * @property float|null $repair_cost Стоимость ремонте
 * @property string|null $comments Примечания
 * @property int $is_deleted
 * @property int $status_id
 * @property string|null $created
 * @property int|null $creator_id
 * @property string|null $modified
 * @property int|null $modifier_id
 *
 * @property User $creator
 * @property EquipmentRepairWRepairerLink[] $equipmentRepairWRepairerLinks
 * @property EquipmentUnit $equipmentUnit
 * @property User $modifier
 * @property User[] $repairers
 * @property Status $status
 */
class EquipmentRepair extends BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'equipment_repair';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['equipment_unit_id', 'status_id', 'defect'], 'required'],
            [['equipment_unit_id', 'downtime', 'is_deleted', 'status_id', 'creator_id', 'modifier_id'], 'integer'],
            [['when_broken', 'when_repaired', 'created', 'modified'], 'safe'],
            [['when_broken', 'when_repaired'], 'filter', 'filter' => function ($value) {
                return DateHelper::convertDateRuToIso($value);
            }],
            [['defect', 'reason', 'comments'], 'string'],
            [['repair_cost'], 'filter', 'filter' => function ($value) {
                return (float)str_replace(' ', '', $value ?? '');
            }],
            [['repair_cost'], 'number'],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['creator_id' => 'id']],
            [['equipment_unit_id'], 'exist', 'skipOnError' => true, 'targetClass' => EquipmentUnit::class, 'targetAttribute' => ['equipment_unit_id' => 'id']],
            [['modifier_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['modifier_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => Status::class, 'targetAttribute' => ['status_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'equipment_unit_id' => Yii::t('app', 'Наименование оборудования'),
            'when_broken' => Yii::t('app', 'Когда вышло из строя'),
            'defect' => Yii::t('app', 'Что вышло из строя'),
            'reason' => Yii::t('app', 'Причина выхода из строя'),
            'when_repaired' => Yii::t('app', 'Когда отремонтировано'),
            'downtime' => Yii::t('app', 'Общее время простоя'),
            'repair_cost' => Yii::t('app', 'Стоимость ремонте'),
            'comments' => Yii::t('app', 'Примечания'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'status_id' => Yii::t('app', 'Статус'),
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
     * Gets query for [[EquipmentRepairWRepairerLinks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEquipmentRepairWRepairerLinks()
    {
        return $this->hasMany(EquipmentRepairWRepairerLink::class, ['equipment_repair_id' => 'id']);
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
     * Gets query for [[Repairers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRepairers()
    {
        return $this->hasMany(User::class, ['id' => 'repairer_id'])->viaTable('equipment_repair_w_repairer_link', ['equipment_repair_id' => 'id']);
    }

    /**
     * Gets query for [[Status]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(Status::class, ['id' => 'status_id']);
    }
}
