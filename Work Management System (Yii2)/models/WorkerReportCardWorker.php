<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "worker_report_card_worker".
 *
 * @property int $id
 * @property int $card_id
 * @property int $user_id
 * @property int $hours_plan
 * @property int $hours_fact
 *
 * @property int $hours
 *
 * @property WorkerReportCard $card
 * @property User $user
 * @property WorkerReportCardWorkerUnit[] $units
 */
class WorkerReportCardWorker extends BaseActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'worker_report_card_worker';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['card_id', 'user_id'], 'required'],
            [['card_id', 'user_id', 'hours_plan', 'hours_fact'], 'integer'],
            [['card_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkerReportCard::class, 'targetAttribute' => ['card_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'card_id' => Yii::t('app', 'Card ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'hours' => Yii::t('app', 'Hours'),
        ];
    }

    /**
     * Gets query for [[Card]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCard()
    {
        return $this->hasOne(WorkerReportCard::class, ['id' => 'card_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Gets query for [[WorkerReportCardWorkerUnits]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUnits()
    {
        return $this->hasMany(WorkerReportCardWorkerUnit::class, ['card_worker_id' => 'id']);
    }

    public function getHours() {
        return $this->hours_fact;
    }

}
