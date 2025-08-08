<?php

namespace app\models\search;

use app\helpers\Date as DateHelper;
use app\models\EquipmentRepair;
use app\models\EquipmentUnit;
use app\models\Status;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Модель для поиска EquipmentRepair
 */
class LoaderRepairSearch extends EquipmentRepair
{
    public $search = '';

    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'equipmentUnit.name',
            'equipmentUnit.category_id',
            'job_id',
            'division_id',
            'user_id',
        ]);
    }

    public function rules()
    {
        return [
            [['equipment_unit_id', 'downtime', 'is_deleted', 'status_id'], 'integer'],
            [['when_broken', 'when_repaired', 'created', 'modified'], 'safe'],
            [['defect', 'reason', 'comments'], 'string'],
            [['repair_cost'], 'number'],
            [['equipment_unit_id'], 'exist', 'skipOnError' => true, 'targetClass' => EquipmentUnit::class, 'targetAttribute' => ['equipment_unit_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => Status::class, 'targetAttribute' => ['status_id' => 'id']],
            [['equipmentUnit.name', 'equipmentUnit.category_id', 'search', 'job_id', 'division_id', 'user_id'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'search' => Yii::t('app', 'Поиск'),
            'equipmentUnit.category_id' => Yii::t('app', 'Категория'),
            'job_id' => Yii::t('app', 'Должность'),
            'division_id' => Yii::t('app', 'Подразделение'),
            'user_id' => Yii::t('app', 'Сотрудник'),
        ]);
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * {@inheritdoc}
     */
    public function search($params, $recsOnPage = 20)
    {
        $query = EquipmentRepair::find()
            ->joinWith('equipmentUnit')
            ->joinWith('status')
            ->joinWith('equipmentUnit.category equipmentCategory')
            ->where(['equipment_unit.used_on_loader_repair' => 1])
            ->andWhere(['equipment_repair.is_deleted' => 0])
            ->andWhere(['equipment_unit.is_deleted' => 0])
        ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => $this->sortingEnabled ? ['defaultOrder' => $this->defaultOrder] : false,
            'pagination' => $recsOnPage > 0 ? [
                'defaultPageSize' => $recsOnPage,
            ] : false,
        ]);

        $dataProvider->sort->attributes['equipmentUnit.name'] = [
            'asc' => ['equipment_unit.name' => SORT_ASC],
            'desc' => ['equipment_unit.name' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query = $dataProvider->query;
        $this->search = trim($this->search);

        if ($this->search) {
            $or = [
                'or',
                ['like', 'defect', $this->search],
                ['like', 'reason', $this->search],
                ['like', 'equipment_repair.comments', $this->search],
                ['like', 'equipment_unit.name', $this->search],
            ];
            $query->andWhere($or);
        }

        if ($this->created && !empty($dateRange = DateHelper::parseDateRange($this->created))) {
            $query->andWhere(['between', 'equipment_repair.created', $dateRange['from'], $dateRange['to']]);
        }
        if ($this->when_broken && !empty($dateRange = DateHelper::parseDateRange($this->when_broken))) {
            $query->andWhere(['between', 'when_broken', $dateRange['from'], $dateRange['to']]);
        }
        if ($this->when_repaired && !empty($dateRange = DateHelper::parseDateRange($this->when_repaired))) {
            $query->andWhere(['between', 'when_repaired', $dateRange['from'], $dateRange['to']]);
        }

        $query->andFilterWhere(['=', 'equipment_unit_id', $this->getAttribute('equipment_unit_id')]);
        $query->andFilterWhere(['=', 'status_id', $this->getAttribute('status_id')]);

        // Return data provider
        return $dataProvider;
    }
}
