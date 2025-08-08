<?php

namespace app\models;

use app\helpers\Date as DateHelper;
use app\helpers\NotificationHelper;
use app\widgets\Flashes;
use Yii;

/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property string $number Номер заказа
 * @property int $specification Спецификация: есть/нет
 * @property int|null $status_id Статус заказа (справочник)
 * @property int|null $customer_id Организация-заказчик (справочник)
 * @property int|null $manager_id Менеджер-инициатор (справочник)
 * @property int|null $product_id Товар (справочник)
 * @property int|null $material_width_id Ширина материала (справочник)
 * @property int|null $insulation_thickness_id Толщина утеплителя (справочник)
 * @property int|null $min_length Минимальная длина, мм
 * @property int|null $max_length Максимальная длина, мм
 * @property float|null $area Площадь, кв. м
 * @property float|null $area_finished Сделано, кв. м
 * @property float|null $bundles_number Количество пачек
 * @property string|null $1c_date Дата в 1С
 * @property int|null $production_location_id Адрес производства (справочник)
 * @property int|null $delivery_type_id Тип доставки (справочник)
 * @property string|null $delivery_address Адрес доставки
 * @property string|null $production_start_date Дата начала производства
 * @property string|null $release_plan_date Дата ВП изначальная
 * @property string|null $release_actual_date Дата ВП актуальная
 * @property string|null $release_fact_date Дата ВП фактическая
 * @property string|null $insulation_receipt_date Дата поступления утеплителя
 * @property string|null $metal_receipt_date Дата поступления металла
 * @property string|null $prepayment Предоплата
 * @property float|null $cash_payment Доплата нал/терминал
 * @property float|null $cashless_payment Доплата безнал
 * @property string|null $comments Любые комментарии
 * @property string|null $created
 * @property int|null $creator_id
 * @property string|null $modified
 * @property int|null $modifier_id
 *
 * @property User $creator
 * @property Organization $customer
 * @property DeliveryType $deliveryType
 * @property InsulationThickness $insulationThickness
 * @property User $manager
 * @property MaterialWidth $materialWidth
 * @property User $modifier
 * @property Product $product
 * @property ProductionLocation $productionLocation
 */
class Order extends BaseActiveRecord
{
    public $statusTypes = [];

    public string $customerName = '';
    public string $productName = '';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status_id', 'customer_id', 'manager_id', 'product_id', 'material_width_id', 'insulation_thickness_id', 'min_length', 'max_length', 'area', 'area_finished', 'bundles_number', 'cars', '1c_date', 'production_location_id', 'delivery_type_id', 'delivery_address', 'production_start_date', 'release_plan_date', 'release_actual_date', 'release_fact_date', 'insulation_receipt_date', 'metal_receipt_date', 'prepayment', 'cash_payment', 'cashless_payment', 'comments', 'created', 'creator_id', 'modified', 'modifier_id'], 'default', 'value' => null],
            [['specification'], 'default', 'value' => 0],
            [['number', 'customer_id', 'product_id'], 'required'],
            [['specification', 'status_id', 'customer_id', 'manager_id', 'product_id', 'material_width_id', 'insulation_thickness_id', 'min_length', 'max_length', 'production_location_id', 'delivery_type_id', 'creator_id', 'modifier_id'], 'integer'],
            [['area', 'area_finished', 'bundles_number', 'cash_payment', 'cashless_payment'], 'filter', 'filter' => function ($value) {
                return (float)str_replace([' ', ','], ['', '.'], $value ?? '');
            }],
            [['area', 'area_finished', 'bundles_number', 'cash_payment', 'cashless_payment'], 'number'],
            [['1c_date', 'production_start_date', 'release_plan_date', 'release_actual_date', 'release_fact_date', 'insulation_receipt_date', 'metal_receipt_date'], 'filter', 'filter' => function ($value) {
                return DateHelper::convertDateRuToIso($value);
            }],
            ['production_start_date', 'filter', 'filter' => function ($value) {
                if ($this->isNewRecord) {
                   return $value;
                }
                if (!Yii::$app->user->can('app_orders_production-start-date-correction')
                    || (!empty($this->getOldAttribute('production_start_date')) && empty($value))) {
                    return $this->getOldAttribute('production_start_date');
                }
                return $value;
            }, 'skipOnEmpty' => false, 'skipOnError' => false],
            ['release_fact_date', 'filter', 'filter' => function ($value) {
                if ($this->isNewRecord) {
                   return $value;
                }
                if (!Yii::$app->user->can('app_orders_release-fact-date-correction')
                    || (!empty($this->getOldAttribute('release_fact_date')) && empty($value))) {
                    return $this->getOldAttribute('release_fact_date');
                }
                return $value;
            }, 'skipOnEmpty' => false, 'skipOnError' => false],
            [['1c_date', 'production_start_date', 'release_plan_date', 'release_actual_date', 'release_fact_date', 'insulation_receipt_date', 'metal_receipt_date', 'created', 'modified'], 'safe'],
            [['cars', 'delivery_address', 'comments'], 'string'],
            [['product_name', 'customer_name'], 'safe'],
            [['number', 'prepayment'], 'string', 'max' => 255],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['creator_id' => 'id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Organization::class, 'targetAttribute' => ['customer_id' => 'id']],
            [['delivery_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => DeliveryType::class, 'targetAttribute' => ['delivery_type_id' => 'id']],
            [['insulation_thickness_id'], 'exist', 'skipOnError' => true, 'targetClass' => InsulationThickness::class, 'targetAttribute' => ['insulation_thickness_id' => 'id']],
            [['manager_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['manager_id' => 'id']],
            [['material_width_id'], 'exist', 'skipOnError' => true, 'targetClass' => MaterialWidth::class, 'targetAttribute' => ['material_width_id' => 'id']],
            [['modifier_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['modifier_id' => 'id']],
            [['production_location_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductionLocation::class, 'targetAttribute' => ['production_location_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
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
            'number' => Yii::t('app', 'Номер заказа'),
            'status_id' => Yii::t('app', 'Статус'),
            'specification' => Yii::t('app', 'Спецификация: есть/нет'),
            'customer_id' => Yii::t('app', 'Организация-заказчик (справочник)'),
            'manager_id' => Yii::t('app', 'Менеджер-инициатор (справочник)'),
            'product_id' => Yii::t('app', 'Товар (справочник)'),
            'material_width_id' => Yii::t('app', 'Ширина материала (справочник)'),
            'insulation_thickness_id' => Yii::t('app', 'Толщина утеплителя (справочник)'),
            'min_length' => Yii::t('app', 'Минимальная длина, мм'),
            'max_length' => Yii::t('app', 'Максимальная длина, мм'),
            'area' => Yii::t('app', 'Площадь, кв. м'),
            'area_finished' => Yii::t('app', 'Сделано, кв. м'),
            'bundles_number' => Yii::t('app', 'Количество пачек'),
            '1c_date' => Yii::t('app', 'Дата в 1С'),
            'production_location_id' => Yii::t('app', 'Адрес производства (справочник)'),
            'delivery_type_id' => Yii::t('app', 'Тип доставки (справочник)'),
            'delivery_address' => Yii::t('app', 'Адрес доставки'),
            'production_start_date' => Yii::t('app', 'Дата начала производства'),
            'release_plan_date' => Yii::t('app', 'Дата ВП изначальная'),
            'release_actual_date' => Yii::t('app', 'Дата ВП актуальная'),
            'release_fact_date' => Yii::t('app', 'Дата ВП фактическая'),
            'insulation_receipt_date' => Yii::t('app', 'Дата поступления утеплителя'),
            'metal_receipt_date' => Yii::t('app', 'Дата поступления металла'),
            'prepayment' => Yii::t('app', 'Предоплата'),
            'cash_payment' => Yii::t('app', 'Нал/терминал к оплате'),
            'cashless_payment' => Yii::t('app', 'Безнал к оплате'),
            'comments' => Yii::t('app', 'Любые комментарии'),
            'created' => Yii::t('app', 'Created'),
            'creator_id' => Yii::t('app', 'Creator ID'),
            'modified' => Yii::t('app', 'Modified'),
            'modifier_id' => Yii::t('app', 'Modifier ID'),
        ];
    }

    public function init()
    {
        parent::init();

        $this->statusTypes = Status::getSalesStatusTypes();
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if (empty($this->statusTypes[Status::STATUS_TYPE_NEW])
            || empty($this->statusTypes[Status::STATUS_TYPE_PROGRESS])
            || empty($this->statusTypes[Status::STATUS_TYPE_DONE])
        ) {
            Flashes::setError('Не заполнен справочник статусов заказа.');
            return false;
        }

        if ($this->isNewRecord) {
            $this->status_id = $this->statusTypes[Status::STATUS_TYPE_NEW];
            $this->manager_id = Yii::$app->user->id;
        }

        if (!empty($this->production_start_date)
            && ($this->status_id === $this->statusTypes[Status::STATUS_TYPE_NEW])
        ) {
            $this->status_id = $this->statusTypes[Status::STATUS_TYPE_PROGRESS];
        }

        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // starting production
        if (($changedAttributes['status_id'] ?? null) === $this->statusTypes[Status::STATUS_TYPE_NEW]
            && $this->status_id === $this->statusTypes[Status::STATUS_TYPE_PROGRESS]
        ) {
            $this->initStages();
        }

        if(key_exists('release_actual_date', $changedAttributes) && $this->release_actual_date && $this->release_actual_date != $this->release_plan_date) {
            NotificationHelper::notifyPlanningDateChanged($this);
        }
    }

    private function initStages()
    {
        $productStages = $this->product->getProductionStages()->all();
        if (!empty($productStages)) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                Yii::$app->db->createCommand()->delete('order_w_production_stage_link', 'order_id = ' . $this->id)->execute();

                foreach ($productStages as $productStage) {
                    $orderStage = new OrderWProductionStageLink([
                        'order_id' => $this->id,
                        'production_stage_id' => $productStage->id,
                        'production_start_date' => $this->production_start_date,
                        'creator_id' => Yii::$app->user->id,
                        'created' => date('Y-m-d H:i:s'),
                        'modifier_id' => Yii::$app->user->id,
                        'modified' => date('Y-m-d H:i:s'),
                    ]);
                    $orderStage->save(false);
                }
                $transaction->commit();
            } catch (\Throwable $e) {
                Flashes::setError('Ошибка при формировании маршрута.');
                $transaction->rollBack();
            }
        }
    }

    public function hasEditableStatus()
    {
        return in_array($this->status_id, [
            $this->statusTypes[Status::STATUS_TYPE_NEW],
            $this->statusTypes[Status::STATUS_TYPE_PROGRESS]
        ], true);
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
     * Gets query for [[Customer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Organization::class, ['id' => 'customer_id']);
    }

    /**
     * Gets query for [[DeliveryType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeliveryType()
    {
        return $this->hasOne(DeliveryType::class, ['id' => 'delivery_type_id']);
    }

    /**
     * Gets query for [[InsulationThickness]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInsulationThickness()
    {
        return $this->hasOne(InsulationThickness::class, ['id' => 'insulation_thickness_id']);
    }

    /**
     * Gets query for [[Manager]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getManager()
    {
        return $this->hasOne(User::class, ['id' => 'manager_id']);
    }

    /**
     * Gets query for [[MaterialWidth]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialWidth()
    {
        return $this->hasOne(MaterialWidth::class, ['id' => 'material_width_id']);
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
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * Gets query for [[ProductionLocation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductionLocation()
    {
        return $this->hasOne(ProductionLocation::class, ['id' => 'production_location_id']);
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
