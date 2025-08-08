<?php
namespace app\models\search;

use app\models\User;
use app\models\WorkerReportCard;
use yii\helpers\ArrayHelper;
use Yii;

/**
 *
 */


class PayrollStatementSearch extends User
{
    public $range;
    public $user_id;
    public $division_id;

    public $dateFrom;
    public $dateTo;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['range'], 'string'],
            [['user_id', 'state_id', 'job_id', 'division_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load($data, $formName = null)
    {
        if(parent::load($data, $formName)) {
            if($this->range) {
                $date = explode(" - ", $this->range);
                $this->dateFrom = $date[0];
                $this->dateTo = $date[1];
            } else {
                $this->dateFrom = date('Y-m-01');
                $this->dateTo = date('Y-m-t');
            }
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function loadDateRange()
    {
        if($this->range) {
            $date = explode(" - ", $this->range);
            $this->dateFrom = $date[0];
            $this->dateTo = $date[1];
        } else {
            $this->dateFrom = date('Y-m-01');
            $this->dateTo = date('Y-m-t');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fio' => Yii::t('app', 'Сотрудник'),
            'job' => Yii::t('app', 'Должность'),
            'division' => Yii::t('app', 'Подразделение'),
            'salary' => Yii::t('app', 'Оклад'),
            'state' => Yii::t('app', 'Статус'),
        ];
    }

//    public function

    public function search($params, $recsOnPage = 20)
    {
        if(key_exists('per-page', $params)) {
            $recsOnPage = (int)$params['per-page'];
        }
        $dataProvider = parent::search($params, $recsOnPage);
        $query = $dataProvider->query;


        $query->joinWith('job');

        // $query->andFilterWhere([\app\models\Job::tableName().'id' => $this->job_id]);

        $query->andFilterWhere([User::tableName().'.id' => $this->user_id]);
        // $query->andFilterWhere(['job.division_id' => $this->division_id]);
        // $query->andFilterWhere([User::tableName().'.state_id' => $this->state_id]);
        // $query->andFilterWhere([User::tableName().'.job_id' => $this->job_id]);
        // $query->andFilterWhere(['job.division_id' => $this->division_id]);

        return $dataProvider;
    }

    public function getHoursSpent($startDate, $endDate)
    {
        //var_dump($this->dateFrom); die;

        //в идеале перенести логику в метод search, чтобы не висло на больших выборках
        return WorkerReportCard::getHoursSumByUser($this->id, $startDate, $endDate);
    }

    /**
     * @return array
     */
    public static function getUsersList(): array
    {
        return ArrayHelper::map(
            User::find()
                ->where(['is_deleted' => 0])
                ->orderBy('fio')
                ->all(),
            'id',
            'fio'
        );
    }

}