<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_w_trip_link".
 *
 * @property int $id
 * @property int $order_id Ссылка на заказ
 * @property int $trip_id Ссылка на рейс
 * @property float|null $area_plan Запланированный к отправке объем, кв.м
 * @property float|null $area_fact Фактически отправленный объем, кв.м
 * @property float|null $bundles_number_plan Запланированное количество пачек
 * @property float|null $bundles_number_fact Фактическое количество пачек
 * @property string $content Содержимое заказа в рейсе
 * @property int|null $accounting_documents Закрывающие документы
 * @property int|null $loading_documents Документы при отгрузке
 * @property int|null $is_accountant_confirmed Бухгалтерия подтверждает документы
 * @property int|null $is_client_refused Клиент отказался
 * @property string|null $refusal_reason Причина отказа
 * @property int $is_deleted
 * @property string|null $created
 * @property int|null $creator_id
 * @property string|null $modified
 * @property int|null $modifier_id
 *
 * @property User $creator
 * @property User $modifier
 * @property Order $order
 * @property Trip $trip
 */
class OrderWTripLink extends BaseActiveRecord
{
    public const ACCOUNTING_DOCUMENT_TYPES = [
        0 => 'Оригинал',
        1 => 'ЭДО',
        2 => 'Фото',
        3 => 'Скан',
    ];

    public const LOADING_DOCUMENT_TYPES = [
        0 => 'Фото',
        1 => 'Скан',
        2 => 'Нет',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_w_trip_link';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['area_plan', 'area_fact', 'bundles_number_plan', 'bundles_number_fact', 'accounting_documents', 'loading_documents', 'refusal_reason', 'created', 'creator_id', 'modified', 'modifier_id'], 'default', 'value' => null],
            [['is_deleted'], 'default', 'value' => 0],
            [['order_id', 'trip_id', 'content'], 'required'],
            [['order_id', 'trip_id', 'accounting_documents', 'loading_documents', 'is_accountant_confirmed', 'is_client_refused', 'is_deleted', 'creator_id', 'modifier_id'], 'integer'],
            [['area_plan', 'area_fact', 'bundles_number_plan', 'bundles_number_fact'], 'filter', 'filter' => function ($value) {
                return (float)str_replace([' ', ','], ['', '.'], $value ?? '');
            }],
            [['area_plan', 'area_fact', 'bundles_number_plan', 'bundles_number_fact'], 'number'],
            [['content'], 'string'],
            [['created', 'modified'], 'safe'],
            [['refusal_reason'], 'string', 'max' => 255],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['creator_id' => 'id']],
            [['modifier_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['modifier_id' => 'id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['order_id' => 'id']],
            [['trip_id'], 'exist', 'skipOnError' => true, 'targetClass' => Trip::class, 'targetAttribute' => ['trip_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'order_id' => Yii::t('app', 'Ссылка на заказ'),
            'trip_id' => Yii::t('app', 'Ссылка на рейс'),
            'area_plan' => Yii::t('app', 'Запланированный к отправке объем, кв.м'),
            'area_fact' => Yii::t('app', 'Фактически отправленный объем, кв.м'),
            'bundles_number_plan' => Yii::t('app', 'Запланированное количество пачек'),
            'bundles_number_fact' => Yii::t('app', 'Фактическое количество пачек'),
            'content' => Yii::t('app', 'Содержимое заказа в рейсе'),
            'accounting_documents' => Yii::t('app', 'Закрывающие документы'),
            'loading_documents' => Yii::t('app', 'Документы при отгрузке'),
            'is_accountant_confirmed' => Yii::t('app', 'Бухгалтерия подтверждает документы'),
            'is_client_refused' => Yii::t('app', 'Клиент отказался'),
            'refusal_reason' => Yii::t('app', 'Причина отказа'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
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
     * Gets query for [[Modifier]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getModifier()
    {
        return $this->hasOne(User::class, ['id' => 'modifier_id']);
    }

    /**
     * Gets query for [[Order]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    /**
     * Gets query for [[Trip]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrip()
    {
        return $this->hasOne(Trip::class, ['id' => 'trip_id']);
    }

}
