<?php

namespace app\models;

use app\helpers\Date as DateHelper;
use Yii;

/**
 * This is the model class for table "order_w_production_stage_link".
 *
 * @property int $id
 * @property int $production_stage_id
 * @property int $order_id
 * @property string|null $production_start_date
 * @property string|null $release_plan_date
 * @property string|null $release_fact_date
 * @property string|null $inner_type Вид ВНТ
 * @property string|null $outer_type Вид ВНШ
 * @property string|null $inner_color Цвет ВНТ
 * @property string|null $outer_color Цвет ВНШ
 * @property float|null $inner_thickness Внутренняя толщина
 * @property float|null $outer_thickness Внешняя толщина
 * @property string|null $comments Любые комментарии
 * @property string|null $ticket_positions Позиции в бланке на печать
 * @property int $is_deleted
 * @property string|null $created
 * @property int|null $creator_id
 * @property string|null $modified
 * @property int|null $modifier_id
 *
 * @property User $creator
 * @property User $modifier
 * @property Product $order
 * @property ProductionStage $productionStage
 * @property Insulation[] $insulations
 */
class OrderWProductionStageLink extends BaseActiveRecord
{
    public $insulations = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_w_production_stage_link';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['production_start_date', 'release_plan_date', 'release_fact_date', 'comments', 'created', 'creator_id', 'modified', 'modifier_id'], 'default', 'value' => null],
            [['inner_type', 'inner_color', 'outer_type', 'outer_color', 'inner_thickness', 'outer_thickness', 'ticket_positions'], 'default', 'value' => null],
            [['is_deleted'], 'default', 'value' => 0],
            [['production_stage_id', 'order_id'], 'required'],
            [['production_stage_id', 'order_id', 'is_deleted', 'creator_id', 'modifier_id'], 'integer'],
            [['inner_thickness', 'outer_thickness'], 'filter', 'filter' => function ($value) {
                return (float)str_replace([' ', ','], ['', '.'], $value ?? '');
            }],
            [['inner_thickness', 'outer_thickness'], 'number'],
            [['production_start_date', 'release_plan_date', 'release_fact_date', 'created', 'modified', 'ticket_positions'], 'safe'],
            [['production_start_date', 'release_plan_date', 'release_fact_date'], 'filter', 'filter' => function ($value) {
                return DateHelper::convertDateRuToIso($value);
            }],
            [['inner_type', 'inner_color', 'outer_type', 'outer_color', 'comments', 'ticket_positions'], 'string'],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['creator_id' => 'id']],
            [['modifier_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['modifier_id' => 'id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['order_id' => 'id']],
            [['production_stage_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductionStage::class, 'targetAttribute' => ['production_stage_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'production_stage_id' => Yii::t('app', 'Production Stage ID'),
            'order_id' => Yii::t('app', 'Order ID'),
            'production_start_date' => Yii::t('app', 'Production Start Date'),
            'release_plan_date' => Yii::t('app', 'Release Plan Date'),
            'release_fact_date' => Yii::t('app', 'Release Fact Date'),
            'inner_type' => Yii::t('app', 'Inner Type'),
            'outer_type' => Yii::t('app', 'Outer Type'),
            'inner_color' => Yii::t('app', 'Inner Color'),
            'outer_color' => Yii::t('app', 'Outer Color'),
            'inner_thickness' => Yii::t('app', 'Inner Thickness'),
            'outer_thickness' => Yii::t('app', 'Outer Thickness'),
            'comments' => Yii::t('app', 'Любые комментарии'),
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
        return $this->hasOne(Product::class, ['id' => 'order_id']);
    }

    /**
     * Gets query for [[ProductionStage]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductionStage()
    {
        return $this->hasOne(ProductionStage::class, ['id' => 'production_stage_id']);
    }

    /**
     * Gets query for [[Insulations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInsulations()
    {
        return $this->hasMany(Insulation::class, ['id' => 'insulation_id'])->viaTable('order_w_production_stage_w_insulation_link', ['order_production_stage_id' => 'id']);
    }
}
