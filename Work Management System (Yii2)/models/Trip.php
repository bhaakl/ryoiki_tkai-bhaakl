<?php

namespace app\models;

use app\helpers\Date as DateHelper;
use Yii;

/**
 * This is the model class for table "trip".
 *
 * @property int $id
 * @property int $car_id
 * @property string $drivers Водители на рейсе
 * @property string|null $loading_plan_start_date Дата планируемой подачи на загрузку
 * @property string|null $loading_fact_start_date Дата подачи на загрузку
 * @property string|null $loading_fact_end_date Дата убытия
 * @property float|null $loading_time Время загрузки
 * @property int|null $is_call_required Созвон за день
 * @property string|null $comments
 * @property int $is_deleted
 * @property string|null $created
 * @property int|null $creator_id
 * @property string|null $modified
 * @property int|null $modifier_id
 *
 * @property Car $car
 * @property User $creator
 * @property User $modifier
 */
class Trip extends BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trip';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['loading_plan_start_date', 'loading_fact_start_date', 'loading_fact_end_date', 'loading_time', 'comments', 'created', 'creator_id', 'modified', 'modifier_id'], 'default', 'value' => null],
            [['is_deleted'], 'default', 'value' => 0],
            [['car_id'], 'required'],
            [['car_id', 'is_call_required', 'is_deleted', 'creator_id', 'modifier_id'], 'integer'],
            [['loading_time'], 'filter', 'filter' => function ($value) {
                return (float)str_replace([' ', ','], ['', '.'], $value ?? '');
            }],
            [['loading_time'], 'number'],
            [['loading_plan_start_date'], 'filter', 'filter' => function ($value) {
                return DateHelper::convertDateRuToIso($value);
            }],
            [['loading_fact_start_date', 'loading_fact_end_date'], 'filter', 'filter' => function ($value) {
                return DateHelper::convertDatetimeRuToIso($value);
            }],
            [['loading_plan_start_date', 'loading_fact_start_date', 'loading_fact_end_date', 'created', 'modified'], 'safe'],
            [['comments'], 'string'],
            [['drivers'], 'string', 'max' => 255],
            [['car_id'], 'exist', 'skipOnError' => true, 'targetClass' => Car::class, 'targetAttribute' => ['car_id' => 'id']],
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
            'id' => Yii::t('app', 'ID'),
            'car_id' => Yii::t('app', 'Автомобиль'),
            'drivers' => Yii::t('app', 'Водители на рейсе'),
            'loading_plan_start_date' => Yii::t('app', 'Дата планируемой подачи на загрузку'),
            'loading_fact_start_date' => Yii::t('app', 'Дата подачи на загрузку'),
            'loading_fact_end_date' => Yii::t('app', 'Дата убытия'),
            'loading_time' => Yii::t('app', 'Время загрузки'),
            'is_call_required' => Yii::t('app', 'Созвон за день'),
            'comments' => Yii::t('app', 'Comments'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'created' => Yii::t('app', 'Created'),
            'creator_id' => Yii::t('app', 'Creator ID'),
            'modified' => Yii::t('app', 'Modified'),
            'modifier_id' => Yii::t('app', 'Modifier ID'),
        ];
    }

    /**
     * Gets query for [[Car]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCar()
    {
        return $this->hasOne(Car::class, ['id' => 'car_id']);
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
