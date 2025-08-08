<?php

namespace app\models;

use DateTime;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "worker_report_card".
 *
 * @property int $id
 * @property string|null $created
 * @property int|null $product_id
 * @property int $is_deleted
 * @property string|null $work_date
 * @property string|null $comments
 * @property int $status
 *
 * @property Product $product
 * @property WorkerReportCardWorker[] $workers
 */
class WorkerReportCard extends BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'worker_report_card';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['comments'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'created' => 'Created',
            'user_id' => 'User ID',
            'user_job_id' => 'Идентификатор должности',
            'user_division_id' => 'Идентификатор подразделение',
            'production_stage_id' => 'Production stage ID',
            'is_deleted' => 'Is Deleted',
            'work_date' => 'Date',
            'hours_spent' => 'Hours Spent',
            'number_of_square' => 'Number Of Square',
            'unit_count' => 'Количество единиц',
            'comments' => 'Описание',
            'status' => 'Status',
        ];
    }

    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    public function getWorkers()
    {
        return $this->hasMany(WorkerReportCardWorker::class, ['card_id' => 'id']);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Custom Methods
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public static function prepareRecords($date) {
        $workers = User::getWorkers();
        $products = Product::find()->joinWith('productMeasureUnits')->all();
        $records = WorkerReportCard::find()->where(['work_date' => $date])->indexBy('product_id')->all();
        foreach ($products as $product) {
            $record = null;
            if(key_exists($product->id, $records)) {
                $record = $records[$product->id];
            } else {
                $record = new WorkerReportCard(['work_date' => $date, 'product_id' => $product->id]);
                $record->save();
            }
            foreach ($workers as $worker) {
                $productWorker = WorkerReportCardWorker::find()->where(['user_id' => $worker->id, 'card_id' => $record->id])->one();
                if(!$productWorker) {
                    $productWorker = new WorkerReportCardWorker(['user_id' => $worker->id, 'card_id' => $record->id]);
                    $productWorker->save();
                }
                $units = WorkerReportCardWorkerUnit::find()->where(['card_worker_id' => $productWorker->id])->indexBy('measure_unit_id')->all();
                foreach ($product->productMeasureUnits as $productMeasureUnit) {
                    if(!key_exists($productMeasureUnit->measure_unit_id, $units)) {
                        (new WorkerReportCardWorkerUnit(['card_worker_id' => $productWorker->id, 'measure_unit_id' => $productMeasureUnit->measure_unit_id]))->save();
                    }
                }
            }
        }
    }

    /**
     * @param int $id
     * @return WorkerReportCardWorker|null
     */
    public function getWorkerByUserId($id)
    {
        foreach ($this->workers as $worker) {
            if($worker->user_id === $id) {
                return $worker;
            }
        }
        return null;
    }

    public static function getWorkingHoursInMonth($date = null)
    {
        if($date == null) {
            $date = date('Y-m');
        }
        $daysCount = date('t', strtotime($date.'-01'));
        $start = (int)date('N', strtotime($date.'-01'));
        $sundays = intval($daysCount / 7) + (int)($start + $daysCount % 7 > 7);
        $workingDaysCount = $daysCount - $sundays;
        return 12 * $workingDaysCount;
    }

    public static function findOrCreate($conditions)
    {
        $model = static::findOne($conditions);
        if (!$model) {
            $model = new static($conditions);
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                if (!$model->save()) {
                    $transaction->rollBack();

                    return static::findOne($conditions);
                }
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();

                return static::findOne($conditions);
            }
        }

        return $model;
    }

    public static function getProductionStageList(): array
    {
        return ProductionStage::find()
            ->where(['is_deleted' => 0])
            ->all();
    }

    public static function getHoursByUser(int $userId, ?string $date1 = '', ?string $date2 = ''): array
    {
        $query = self::find()->where(['user_id' => $userId])->andWhere(['is_deleted' => 0]);

        if ($date1 != '' && $date2 != '') {
            $query->andWhere(['between', self::tableName().'.work_date', $date1, $date2]);
        }
        return $query->all();
    }

    public static function getHoursSumByUser(int $userId, ?string $date1 = '', ?string $date2 = '')
    {
        return array_sum(ArrayHelper::getColumn(static::getHoursByUser($userId, $date1, $date2), 'hours_spent'));
    }

    public static function getHoursSumByUserInMonth(int $userId, string $monthYear)
    {
        $firstDay = date('Y-m-01', strtotime("01-$monthYear"));
        $lastDay = date('Y-m-t', strtotime("01-$monthYear"));
        $query = self::find()
            ->where(['is_deleted' => 0])
            ->andWhere(['user_id' => $userId]);

        if (!empty($firstDay) and !empty($lastDay)) {
            $query->andWhere(['between', 'created', $firstDay, $lastDay]);
        }

        $report = $query->all();

        return array_sum(ArrayHelper::getColumn($report, 'hours_spent'));
    }

    public static function getHoursSumByUserAndDay(int $userId, string $date): int
    {
        $hoursSum = 0;
        $reports = self::find()
            ->where(['user_id' => $userId])
            ->andWhere(['is_deleted' => 0])
            ->andWhere(['DATE(work_date)' => $date])
            ->all();

        foreach ($reports as $report) {
            /* @var $report WorkerReportCard */
            $hoursSum += (int)$report->hours_spent;
        }

        return $hoursSum;
    }

    public static function getCountByProduct(int $productId, string $monthYear): int
    {
        $firstDay = date('Y-m-01', strtotime("01-$monthYear"));
        $lastDay = date('Y-m-t', strtotime("01-$monthYear"));
        $query = WorkerReportCardWorker::find()
            ->select(['hours' => 'worker_report_card_worker.hours'])
            ->innerJoin('worker_report_card',
            'worker_report_card_worker.card_id = worker_report_card.id')
            ->where(['is_deleted' => 0])
            ->andWhere(['product_id' => $productId])
            ->andWhere(['between', 'created', $firstDay, $lastDay]);
        $report = $query->all();
        return array_sum(ArrayHelper::getColumn($report, 'hours'));
    }

    public static function getCountByProductionStageAndUserInMonth(int $userId, int $productionStageId, string $monthYear): array
    {
        $firstDay = date('Y-m-01', strtotime("01-$monthYear"));
        $lastDay = date('Y-m-t', strtotime("01-$monthYear"));
        $query = self::find()
            ->where(['is_deleted' => 0])
            ->andWhere(['production_stage_id' => $productionStageId])
            ->andWhere(['user_id' => $userId]);

        if (!empty($firstDay) and !empty($lastDay)) {
            $query->andWhere(['between', 'created', $firstDay, $lastDay]);
        }

        $report = $query->all();

        $unitsSum = array_sum(ArrayHelper::getColumn($report, 'unit_count'));
        $numberOfSquareSum = array_sum(ArrayHelper::getColumn($report, 'number_of_square'));

        return [$unitsSum, $numberOfSquareSum];
    }

    /**
     * @throws \Exception
     */
    public static function getCountByProductAndDay(int $productId, string $date): int
    {
        $report = WorkerReportCardWorker::find()
            ->select(['hours' => 'worker_report_card_worker.hours'])
            ->innerJoin('worker_report_card',
            'worker_report_card_worker.card_id = worker_report_card.id')
            ->where(['is_deleted' => 0])
            ->andWhere(['product_id' => $productId])
            ->andWhere(['DATE(work_date)' => $date])
            ->all();
        return array_sum(ArrayHelper::getColumn($report, 'hours'));
    }

    public static function getHoursByProductionStageInMonth(int $productionStageId, string $monthYear)
    {
        $firstDay = date('Y-m-01', strtotime("01-$monthYear"));
        $lastDay = date('Y-m-t', strtotime("01-$monthYear"));
        $query = self::find()
            ->where(['is_deleted' => 0])
            ->andWhere(['production_stage_id' => $productionStageId]);

        if (!empty($firstDay) and !empty($lastDay)) {
            $query->andWhere(['between', 'created', $firstDay, $lastDay]);
        }

        $report = $query->all();

        return array_sum(ArrayHelper::getColumn($report, 'hours_spent'));
    }

    public static function getHoursByProductionStageAndDay(int $productionStageId, string $date): int
    {
        $hours = 0;
        $reports = self::find()->where(['is_deleted' => 0])
            ->andWhere(['production_stage_id' => $productionStageId])
            ->andWhere(['DATE(work_date)' => $date])
            ->all();

        if ($reports) {
            foreach ($reports as $report) {
                /* @var $report WorkerReportCard */
                $hours += $report->hours_spent;
            }
        }

        return $hours;
    }

    public static function getHoursByUserAndProductionStageInMonth(int $userId, int $productionStageId, string $monthYear)
    {
        $firstDay = date('Y-m-01', strtotime("01-$monthYear"));
        $lastDay = date('Y-m-t', strtotime("01-$monthYear"));
        $query = self::find()
            ->where(['is_deleted' => 0])
            ->andWhere(['production_stage_id' => $productionStageId])
            ->andWhere(['user_id' => $userId]);

        if (!empty($firstDay) and !empty($lastDay)) {
            $query->andWhere(['between', 'created', $firstDay, $lastDay]);
        }

        $report = $query->all();

        return array_sum(ArrayHelper::getColumn($report, 'hours_spent'));
    }

    public static function getHoursByProductionStageAndUserInDay(int $userId, int $productionStageId, string $date): int
    {
        $hours = 0;
        $reports = self::find()->where(['is_deleted' => 0])
            ->andWhere(['production_stage_id' => $productionStageId])
            ->andWhere(['user_id' => $userId])
            ->andWhere(['DATE(work_date)' => $date])
            ->all();

        if ($reports) {
            foreach ($reports as $report) {
                /* @var $report WorkerReportCard */
                $hours += $report->hours_spent;
            }
        }

        return $hours;
    }

    public static function getCountByProductionStageAndUserInDay(int $userId, int $productionStageId, string $date): array
    {
        $unitsSum = 0;
        $numberOfSquareSum = 0;
        $reports = WorkerReportCard::find()->where(['is_deleted' => 0])
            ->andWhere(['production_stage_id' => $productionStageId])
            ->andWhere(['user_id' => $userId])
            ->andWhere(['DATE(work_date)' => $date])
            ->all();

        if ($reports) {
            foreach ($reports as $report) {
                /* @var $report WorkerReportCard */
                $unitsSum += $report->unit_count;
                $numberOfSquareSum += $report->number_of_square;
            }
        }

        return [$unitsSum, $numberOfSquareSum];
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // End:
    // Custom Methods
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

}
