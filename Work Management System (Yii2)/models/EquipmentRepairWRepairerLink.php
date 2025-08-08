<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "equipment_repair_w_repairer_link".
 *
 * @property int $id
 * @property int $repairer_id
 * @property int $equipment_repair_id
 * @property string|null $created
 * @property int|null $creator_id
 * @property string|null $modified
 * @property int|null $modifier_id
 *
 * @property User $repairer
 * @property EquipmentRepair $equipmentRepair
 * @property User $creator
 * @property User $modifier
 */
class EquipmentRepairWRepairerLink extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'equipment_repair_w_repairer_link';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['repairer_id', 'equipment_repair_id'], 'required'],
            [['repairer_id', 'equipment_repair_id', 'creator_id', 'modifier_id'], 'integer'],
            [['created', 'modified'], 'safe'],
            [['repairer_id', 'equipment_repair_id'], 'unique', 'targetAttribute' => ['repairer_id', 'equipment_repair_id']],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['creator_id' => 'id']],
            [['equipment_repair_id'], 'exist', 'skipOnError' => true, 'targetClass' => EquipmentRepair::class, 'targetAttribute' => ['equipment_repair_id' => 'id']],
            [['modifier_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['modifier_id' => 'id']],
            [['repairer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['repairer_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'repairer_id' => Yii::t('app', 'Repairer ID'),
            'equipment_repair_id' => Yii::t('app', 'Equipment Repair ID'),
            'created' => Yii::t('app', 'Created'),
            'creator_id' => Yii::t('app', 'Creator ID'),
            'modified' => Yii::t('app', 'Modified'),
            'modifier_id' => Yii::t('app', 'Modifier ID'),
        ];
    }

    /**
     * Gets query for [[Repairer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRepairer()
    {
        return $this->hasOne(User::class, ['id' => 'repairer_id']);
    }

    /**
     * Gets query for [[EquipmentRepair]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEquipmentRepair()
    {
        return $this->hasOne(EquipmentRepair::class, ['id' => 'equipment_repair_id']);
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
     * Gets query for [[Modifier]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getModifier()
    {
        return $this->hasOne(User::class, ['id' => 'modifier_id']);
    }
}
