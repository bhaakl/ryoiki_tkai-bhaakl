<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "notification".
 *
 * @property int $id
 * @property string $name Наименование
 * @property string|null $description Описание
 * @property string|null $template Шаблон
 * @property string|null $comments Комментарий
 * @property int|null $is_active Активно
 * @property string $created Дата создания
 * @property string|null $modified Дата изменения
 * @property int|null $modifier_id Изменил
 *
 * @property User $modifier
 */
class Notification extends BaseActiveRecord
{
    protected static $safeDelete = false;

    public $templates = [
        1 => [
            'class' => Task::class,
            'vars' => [
                'setter' => 'setter.fio',
                'deadline' => 'deadline',
            ]
        ]
    ];

    public $templateVars = [
        'comment' => 'comments'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notification';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'template', 'comments', 'modified', 'modifier_id'], 'default', 'value' => null],
            [['is_active'], 'default', 'value' => 1],
            [['is_active', 'modifier_id'], 'integer'],
            [['created', 'modified'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['description', 'template', 'comments'], 'string'],
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
            'description' => Yii::t('app', 'Описание'),
            'template' => Yii::t('app', 'Шаблон'),
            'comments' => Yii::t('app', 'Комментарий'),
            'is_active' => Yii::t('app', 'Активно'),
            'created' => Yii::t('app', 'Дата создания'),
            'modified' => Yii::t('app', 'Дата изменения'),
            'modifier_id' => Yii::t('app', 'Изменил'),
        ];
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
     * {@inheritdoc}
     */
    public function search($params, $recsOnPage = 0)
    {
        $dataProvider = parent::search($params, $recsOnPage);
        $dataProvider->setSort(['defaultOrder' => ['id' => SORT_ASC]]);
        return $dataProvider;
    }

}
