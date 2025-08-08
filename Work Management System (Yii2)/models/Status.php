<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%status}}".
 *
 * @property int $id
 * @property int|null $scope_id
 * @property string $type
 * @property string $name
 * @property string|null $comments Любые комментарии
 * @property string|null $bg_color Цвет фона для этого статуса (универсальный на всех страницах приложения)
 * @property int|null $used_on_equipment_repair Используется на странице "Ремонт оборудования"
 * @property int|null $used_on_loader_repair Используется на странице "Ремонт погрузчика"
 * @property int|null $used_on_consumables_warehouse_summary Используется на странице "Склад расходников - Свод"
 * @property int|null $used_on_metal_warehouse_summary Используется на странице "Склад металла - Свод"
 * @property int|null $used_on_mw_warehouse_summary Используется на странице "Склад МВ - Свод"
 * @property int|null $used_on_glue_warehouse_summary Используется на странице "Склад клея - Свод"
 * @property int|null $used_on_psb_s_warehouse_summary Используется на странице "Склад ПСБ-С - Свод"
 * @property int|null $used_on_sales Используется на странице "Продажи"
 * @property int|null $used_on_logistics Используется на странице "Логист"
 * @property int|null $used_on_tasks Используется на странице "Задачи"
 * @property int $is_deleted
 * @property string|null $created
 * @property int|null $creator_id
 * @property string|null $modified
 * @property int|null $modifier_id
 *
 * @property User $creator
 * @property User $modifier
 * @property StatusScope $scope
 * @property Task[] $tasks
 */
class Status extends BaseActiveRecord
{
    public const STATUS_TYPE_NEW = 'new';
    public const STATUS_TYPE_PROGRESS = 'progress';
    public const STATUS_TYPE_DONE = 'done';
    public const STATUS_TYPE_PAUSED = 'paused';
    public const STATUS_TYPE_CANCELED = 'canceled';
    public const STATUS_TYPES = [
        self::STATUS_TYPE_NEW,
        self::STATUS_TYPE_PROGRESS,
        self::STATUS_TYPE_DONE,
        self::STATUS_TYPE_PAUSED,
        self::STATUS_TYPE_CANCELED,
    ];

    public const ACTIVE_STATUS_TYPES = [
        self::STATUS_TYPE_NEW,
        self::STATUS_TYPE_PROGRESS,
        self::STATUS_TYPE_PAUSED,
    ];

    const PAGE_NAMES = [
        'used_on_equipment_repair' => 'Ремонт оборудования',
        'used_on_loader_repair' => 'Ремонт погрузчика',
        'used_on_consumables_warehouse_summary' => 'Склад расходников - Свод',
        'used_on_metal_warehouse_summary' => 'Склад металла - Свод',
        'used_on_mw_warehouse_summary' => 'Склад МВ - Свод',
        'used_on_glue_warehouse_summary' => 'Склад клея - Свод',
        'used_on_psb_s_warehouse_summary' => 'Склад ПСБ-С - Свод',
        'used_on_sales' => 'Продажи',
        'used_on_logistics' => 'Логист',
        'used_on_tasks' => 'Задачи',
    ];

    public $pages;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%status}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['scope_id', 'used_on_equipment_repair', 'used_on_loader_repair', 'used_on_consumables_warehouse_summary', 'used_on_metal_warehouse_summary', 'used_on_mw_warehouse_summary', 'used_on_glue_warehouse_summary', 'used_on_psb_s_warehouse_summary', 'used_on_sales', 'used_on_logistics', 'used_on_tasks', 'is_deleted', 'creator_id', 'modifier_id'], 'integer'],
            [['name'], 'required', 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
            [['type', 'comments'], 'string'],
            [['created', 'modified'], 'safe'],
            [['name'], 'string', 'max' => 256],
            [['bg_color'], 'string', 'max' => 16],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['creator_id' => 'id']],
            [['modifier_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['modifier_id' => 'id']],
            [['scope_id'], 'exist', 'skipOnError' => true, 'targetClass' => StatusScope::class, 'targetAttribute' => ['scope_id' => 'id']],
            [['pages'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'scope_id' => Yii::t('app', 'Сфера применения'),
            'type' => Yii::t('app', 'Тип'),
            'name' => Yii::t('app', 'Наименование'),
            'comments' => Yii::t('app', 'Comments'),
            'bg_color' => Yii::t('app', 'Цвет заливки'),
            'used_on_equipment_repair' => Yii::t('app', 'Ремонт оборудования'),
            'used_on_loader_repair' => Yii::t('app', 'Ремонт погрузчика'),
            'used_on_consumables_warehouse_summary' => Yii::t('app', 'Склад расходников - Свод'),
            'used_on_metal_warehouse_summary' => Yii::t('app', 'Склад металла - Свод'),
            'used_on_mw_warehouse_summary' => Yii::t('app', 'Склад МВ - Свод'),
            'used_on_glue_warehouse_summary' => Yii::t('app', 'Склад клея - Свод'),
            'used_on_psb_s_warehouse_summary' => Yii::t('app', 'Склад ПСБ-С - Свод'),
            'used_on_sales' => Yii::t('app', 'Продажи'),
            'used_on_logistics' => Yii::t('app', 'Логист'),
            'used_on_tasks' => Yii::t('app', 'Задачи'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'created' => Yii::t('app', 'Created'),
            'creator_id' => Yii::t('app', 'Creator ID'),
            'modified' => Yii::t('app', 'Modified'),
            'modifier_id' => Yii::t('app', 'Modifier ID'),
            'pages' => Yii::t('app', 'Страницы'),
        ];
    }

    public static function getSalesStatusTypes()
    {
        return ArrayHelper::map(
            self::find()
                ->where(['is_deleted' => 0])
                ->andWhere(['used_on_sales' => 1])
                ->andWhere('type IS NOT NULL')
                ->all(),
            'type',
            'id'
        );
    }

    /**
     * Gets query for [[Creator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'creator_id'])->inverseOf('statuses');
    }

    /**
     * Gets query for [[Modifier]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getModifier()
    {
        return $this->hasOne(User::class, ['id' => 'modifier_id'])->inverseOf('statuses0');
    }

    /**
     * Gets query for [[Scope]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScope()
    {
        return $this->hasOne(StatusScope::class, ['id' => 'scope_id'])->inverseOf('statuses');
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Task::class, ['status_id' => 'id'])->inverseOf('status');
    }

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Custom Methods
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * {@inheritdoc}
     */
    public function search($params, $recsOnPage = 20)
    {
        if(key_exists('per-page', $params)) {
            $recsOnPage = (int)$params['per-page'];
        }
        $dataProvider = parent::search($params, $recsOnPage);

        $query = $dataProvider->query;

		if ($this->scope_id) {
            $query->andWhere(['scope_id' => $this->scope_id]);
        }
		if ($this->name) {
            $query->andWhere(['like', 'name', $this->name]);
        }
		if ($this->comments) {
            $query->andWhere(['like', 'comments', $this->comments]);
        }

        if(is_array($this->pages)) {
            $search_items = ['or'];
            foreach ($this->pages as $page) {
                if($this->hasAttribute($page)) {
                    $search_items[] = [$page => 1];
                }
            }
            if(count($search_items) > 1) {
                $query->andWhere($search_items);
            }
        }

		// Return data provider
        return $dataProvider;
    }

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// End:
	// Custom Methods
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
