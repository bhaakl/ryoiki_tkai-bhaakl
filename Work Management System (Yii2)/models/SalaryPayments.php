<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "salary_payments".
 *
 * @property int $id
 * @property string|null $created
 * @property int|null $salary_type_id
 * @property int|null $account_type_id
 * @property int $is_deleted
 * @property string|null $comments
 * @property int|null $user_id
 * @property int|null $amount
 * @property int $status
 *
 * @property User $user
 */
class SalaryPayments extends BaseActiveRecord
{
    const SALARY_TYPE_SALARY = 1;
    const SALARY_TYPE_PREPAID = 2;

    const ACCOUNT_TYPE_CASH = 1;
    const ACCOUNT_TYPE_OTHER = 2;

    const STATUS_WORKS = 1;
    const STATUS_NOT_WORK = 2;

    public $user_state_id;


    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'salary_payments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['created', 'salary_type_id', 'account_type_id', 'user_id'], 'required', 'on' => [self::SCENARIO_INSERT, self::SCENARIO_UPDATE]],
            [['created', 'salary_type_id', 'account_type_id', 'comments', 'user_id'], 'default', 'value' => null],
            [['user_state_id'], 'integer', 'on' => [self::SCENARIO_SEARCH]],
            [['created'], 'safe'],
            [['salary_type_id', 'account_type_id', 'is_deleted', 'user_id', 'amount', 'status'], 'integer'],
            [['comments'], 'string'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'created' => 'Дата',
            'salary_type_id' => 'Категория',
            'account_type_id' => 'Тип счета',
            'is_deleted' => 'Is Deleted',
            'comments' => 'Описание',
            'user_id' => 'Сотрудник',
            'amount' => 'Сумма',
            'status' => 'Статус',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
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
        $query->joinWith('user', false);

        if ($this->salary_type_id) {
            $query->andWhere(['salary_type_id' => $this->salary_type_id]);
        }
        if ($this->account_type_id) {
            $query->andWhere(['account_type_id' => $this->account_type_id]);
        }

        $query->andFilterWhere(['user.state_id' => $this->user_state_id]);

        if ($this->comments) {
            $query->andWhere(['like', 'comments', $this->comments]);
        }
        if ($this->user_id) {
            $query->andWhere(['user_id' => $this->user_id]);
        }
        if ($this->created){
            $date = explode(" - ", $this->created);
            $query->andWhere(['between', self::tableName().'.created', $date[0], $date[1]]);
        }

        // Return data provider
        return $dataProvider;
    }

    /**
     * @return string[]
     */
    public static function getSalaryTypes(): array
    {
        return CashFlowTransactionCategory::listAll('id', 'name');
    }

    /**
     * @return string[]
     */
    public static function getAccountTypes(): array
    {
        return [
            self::ACCOUNT_TYPE_CASH => "Из кассы",
            self::ACCOUNT_TYPE_OTHER => "Другое",
        ];
    }

    /**
     * @return string[]
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_WORKS => "Работает",
            self::STATUS_NOT_WORK => "Не работает",
        ];
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

    public static function getByUserAndMonth(int $userId, string $monthYear)
    {
        $firstDay = date('Y-m-01', strtotime("01-$monthYear"));
        $lastDay = date('Y-m-t', strtotime("01-$monthYear"));
        $query = self::find()
            ->where(['is_deleted' => 0])
            ->andWhere(['user_id' => $userId]);

        if (!empty($firstDay) and !empty($lastDay)){
            $query->andWhere(['between', 'created', $firstDay, $lastDay]);
        }

        $report = $query->all();

        return array_sum(ArrayHelper::getColumn($report, 'amount'));
    }

    public static function getByUserAndDate(int $userId, $startDate, $endDate)
    {
        $query = self::find()
            ->where(['is_deleted' => 0])
            ->andWhere(['user_id' => $userId]);

        if (!empty($startDate) and !empty($endDate)){
            $query->andWhere(['between', 'created', $startDate, $endDate]);
        }

        $report = $query->all();
/*
        var_dump( $userId);
        var_dump( $startDate);
        var_dump( $endDate);
        var_dump( $report);
        die;*/

        return array_sum(ArrayHelper::getColumn($report, 'amount'));
    }
    public static function getByUserAndOneDate(int $userId, $date)
    {
        $query = self::find()
            ->where(['is_deleted' => 0])
            ->andWhere(['user_id' => $userId]);

        $query->andWhere(['created' => $date]);

        $report = $query->all();

        return array_sum(ArrayHelper::getColumn($report, 'amount'));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // End:
    // Custom Methods
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

}
