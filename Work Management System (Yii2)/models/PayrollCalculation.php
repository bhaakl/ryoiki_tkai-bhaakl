<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "payroll_calculation".
 *
 * @property int $id
 * @property string|null $created
 * @property int|null $salary_category_id
 * @property int|null $account_type_id
 * @property int $is_deleted
 * @property string|null $comments
 * @property int|null $user_id
 * @property int|null $amount
 * @property int $status
 *
 * @property User $user
 */
class PayrollCalculation extends BaseActiveRecord
{
    const ACCOUNT_TYPE_CASH = 1;
    const ACCOUNT_TYPE_OTHER = 2;

    public $user_state_id;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payroll_calculation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['salary_category_id', 'account_type_id', 'user_id'], 'required', 'on' => [self::SCENARIO_UPDATE, self::SCENARIO_INSERT]],
            [['created', 'salary_category_id', 'account_type_id', 'comments', 'user_id'], 'default', 'value' => null],
            [['user_state_id'], 'integer', 'on' => [self::SCENARIO_SEARCH]],
            [['created'], 'safe'],
            [['salary_category_id', 'account_type_id', 'is_deleted', 'user_id', 'amount'], 'integer'],
            [['comments'], 'string'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created' => 'Дата',
            'salary_category_id' => 'Категория',
            'account_type_id' => 'Тип счета',
            'is_deleted' => 'Is Deleted',
            'comments' => 'Описание',
            'user_id' => 'Сотрудник',
            'amount' => 'Сумма',
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

        if ($this->salary_category_id) {
            $query->andWhere(['salary_category_id' => $this->salary_category_id]);
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
            $query->andWhere(['between', 'created', $date[0], $date[1]]);
        }

        // Return data provider
        return $dataProvider;
    }

    /**
     * @return string[]
     */
    public static function getSalaryTypes(): array
    {
        return ArrayHelper::map(SalaryPaymentCategory::find()->all(), 'id', 'name');
    }

    public function getCashFlowTransactionCategory(): \yii\db\ActiveQuery
    {
        return $this->hasOne(CashFlowTransactionCategory::class, ['id' => 'salary_category_id']);
    }

    public static function getPayrollByUser(int $userId): array
    {
        return static::find()->where(['user_id' => $userId])->andWhere(['is_deleted' => 0])->all();
    }

    public static function getPayrollByUserAndCategory(int $userId, int $categoryId): ?int
    {
        $sum = 0;
        $list = static::find()->where(['user_id' => $userId])
            ->andWhere(['salary_category_id' => $categoryId])
            ->andWhere(['is_deleted' => 0])
            ->all();

        foreach ($list as $item){
            /* @var $item PayrollCalculation */
            $sum += $item->amount;
        }

        return $sum;
    }

    public static function getPayrollSumByUser(int $userId, int $typeId = 1)
    {
        $sum = 0;
        $list = static::find()->where(['user_id' => $userId])->andWhere(['is_deleted' => 0])->all();
        foreach ($list as $item){
            if ($item->cashFlowTransactionCategory->type_id == $typeId){
                $sum += $item->amount;
            }
        }

        return $sum;
    }

    public static function getPayrollSumByUserAndDate(int $userId, int $typeId, $startDate, $endDate)
    {
        $typeId = $typeId ?? 1;
        
        $sum = 0;
        $list = static::find()->where(['user_id' => $userId])->andWhere(['is_deleted' => 0]);


        if (!empty($startDate) and !empty($endDate)){
            $list->andWhere(['between', 'created', $startDate, $endDate]);
        }


        $list = $list->all();
        foreach ($list as $item){
            if ($item->cashFlowTransactionCategory->type_id == $typeId){
                $sum += $item->amount;
            }
        }

        return $sum;
    }


    public static function getPayrollByUserAndCategoryAndDate(int $userId, int $categoryId, $startDate, $endDate): ?int
    {
        $sum = 0;
        $list = static::find()->where(['user_id' => $userId])
            ->andWhere(['salary_category_id' => $categoryId])
            ->andWhere(['is_deleted' => 0]);



        if (!empty($startDate) and !empty($endDate)){
            $list->andWhere(['between', 'created', $startDate, $endDate]);
        }


        $list = $list->all();

        foreach ($list as $item){
            /* @var $item PayrollCalculation */
            $sum += $item->amount;
        }

        return $sum;
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

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // End:
    // Custom Methods
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

}
