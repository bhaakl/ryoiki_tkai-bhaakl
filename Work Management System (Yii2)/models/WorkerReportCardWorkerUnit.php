<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "worker_report_card_worker_unit".
 *
 * @property int $id
 * @property int|null $card_worker_id
 * @property int|null $measure_unit_id
 * @property int $value_plan
 * @property int $value_fact
 *
 * @property MeasureUnit $measureUnit
 * @property WorkerReportCardWorker $worker
 */
class WorkerReportCardWorkerUnit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'worker_report_card_worker_unit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['card_worker_id', 'measure_unit_id'], 'default', 'value' => null],
            [['value_plan', 'value_fact'], 'default', 'value' => 0],
            [['card_worker_id', 'measure_unit_id', 'value_plan', 'value_fact'], 'integer'],
            [['measure_unit_id'], 'exist', 'skipOnError' => true, 'targetClass' => MeasureUnit::class, 'targetAttribute' => ['measure_unit_id' => 'id']],
            [['card_worker_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkerReportCardWorker::class, 'targetAttribute' => ['card_worker_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'card_worker_id' => Yii::t('app', 'Worker ID'),
            'measure_unit_id' => Yii::t('app', 'Measure Unit ID'),
            'value_plan' => Yii::t('app', 'Value'),
            'value_fact' => Yii::t('app', 'Value'),
        ];
    }

    /**
     * Gets query for [[MeasureUnit]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMeasureUnit()
    {
        return $this->hasOne(MeasureUnit::class, ['id' => 'measure_unit_id']);
    }

    /**
     * Gets query for [[Worker]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWorker()
    {
        return $this->hasOne(WorkerReportCardWorker::class, ['id' => 'card_worker_id']);
    }

}
