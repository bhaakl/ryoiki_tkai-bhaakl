<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_measure_unit".
 *
 * @property int $id
 * @property int $product_id Продукт
 * @property int $measure_unit_id Единица измерения
 * @property string $description Примечание
 * @property int $is_deleted Запись удалена
 * @property string $created Дата создания
 * @property int|null $creator_id Создал
 * @property string|null $modified Дата изменения
 * @property int|null $modifier_id Изменил
 *
 * @property User $creator
 * @property MeasureUnit $measureUnit
 * @property User $modifier
 * @property Product $product
 */
class ProductMeasureUnit extends BaseActiveRecord
{
    protected static $safeDelete = false;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_measure_unit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['creator_id', 'modified', 'modifier_id'], 'default', 'value' => null],
            [['is_deleted'], 'default', 'value' => 0],
            [['product_id', 'measure_unit_id'], 'required'],
            [['product_id', 'measure_unit_id', 'is_deleted', 'creator_id', 'modifier_id'], 'integer'],
            [['description'], 'string'],
            [['created', 'modified'], 'safe'],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['creator_id' => 'id']],
            [['measure_unit_id'], 'exist', 'skipOnError' => true, 'targetClass' => MeasureUnit::class, 'targetAttribute' => ['measure_unit_id' => 'id']],
            [['modifier_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['modifier_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'product_id' => Yii::t('app', 'Продукт'),
            'measure_unit_id' => Yii::t('app', 'Единица измерения'),
            'description' => Yii::t('app', 'Примечание'),
            'is_deleted' => Yii::t('app', 'Запись удалена'),
            'created' => Yii::t('app', 'Дата создания'),
            'creator_id' => Yii::t('app', 'Создал'),
            'modified' => Yii::t('app', 'Дата изменения'),
            'modifier_id' => Yii::t('app', 'Изменил'),
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
     * Gets query for [[MeasureUnit]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMeasureUnit()
    {
        return $this->hasOne(MeasureUnit::class, ['id' => 'measure_unit_id']);
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
     * {@inheritdoc}
     */
    public function search($params, $recsOnPage = 20)
    {
        $dataProvider = parent::search($params, $recsOnPage);

        $query = $dataProvider->query;
        $query->andWhere(['product_id' => $this->product_id]);
//
//        if ($this->name) {
//            $query->andWhere(['like', 'name', $this->name]);
//        }
        if ($this->description) {
            $query->andWhere(['like', 'description', $this->description]);
        }

        // Return data provider
        return $dataProvider;
    }

}
