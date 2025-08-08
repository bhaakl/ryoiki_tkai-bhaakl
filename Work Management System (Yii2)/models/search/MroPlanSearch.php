<?php

namespace app\models\search;

use app\models\EquipmentUnit;
use app\models\MroPlan;
use app\models\MroWorkType;
use Yii;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Модель для поиска MroPlan
 */
class MroPlanSearch extends MroPlan
{
    protected $defaultOrder = ['name' => SORT_ASC];

    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'year',
            'category_id',
        ]);
    }

    public function rules()
    {
        return [
            [['equipment_unit_id', 'mro_work_type_id', 'creator_id', 'modifier_id'], 'integer'],
            [['plan_date', 'fact_date', 'created', 'modified'], 'safe'],
            [['comments'], 'string'],
            [['year', 'category_id'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'year' => Yii::t('app', 'Год'),
            'name' => Yii::t('app', 'Наименование оборудования'),
            'category_id' => Yii::t('app', 'Категория'),
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
        $this->load($params);

        if (empty($this->year)) {
            $this->year = date('Y');
        }

        $equipmentUnits = ArrayHelper::map(
            EquipmentUnit::find()
                ->where(['is_deleted' => 0])
                ->andWhere(['used_on_mro' => 1])
                ->andFilterWhere(['=', 'id', $this->getAttribute('equipment_unit_id')])
                ->andFilterWhere(['=', 'category_id', $this->getAttribute('category_id')])
                ->orderBy('name')
                ->all(),
            'id',
            'name'
        );

        $mroWorkTypes = ArrayHelper::map(
            MroWorkType::find()
                ->where(['is_deleted' => 0])
                ->orderBy('sorting_number')
                ->all(),
            'id',
            'name'
        );

        $mroPlans = [];
        $query = (new Query())
            ->from('mro_plan')
            ->andFilterWhere(['=', 'year', $this->getAttribute('year')])
            ->all();

        foreach ($query as $row) {
            $mroPlans[$row['equipment_unit_id']][$row['mro_work_type_id']] = [
                'plan_date' => $row['plan_date'],
                'fact_date' => $row['fact_date'],
                'comments' => $row['comments'],
                'year' => $row['year'],
            ];
        }

        $allModels = [];

        foreach ($equipmentUnits as $equipmentUnitId => $equipmentUnitName) {
            $row = [
                'id' => $equipmentUnitId,
                'name' => $equipmentUnitName,
                'plan-fact' => 'план,факт'
            ];
            $comments = [];

            foreach ($mroWorkTypes as $mroWorkTypeId => $mroWorkTypeName) {
                $planDate = !empty($mroPlans[$equipmentUnitId][$mroWorkTypeId]['plan_date'])
                    ? Yii::$app->formatter->asDate($mroPlans[$equipmentUnitId][$mroWorkTypeId]['plan_date'], 'dd.MM.yyyy')
                    : '&nbsp;';
                $factDate = !empty($mroPlans[$equipmentUnitId][$mroWorkTypeId]['fact_date'])
                    ? Yii::$app->formatter->asDate($mroPlans[$equipmentUnitId][$mroWorkTypeId]['fact_date'], 'dd.MM.yyyy')
                    : '&nbsp;';

                $row[$mroWorkTypeName] = $planDate . ',' . $factDate;

                if (!empty($mroPlans[$equipmentUnitId][$mroWorkTypeId]['comments'])) {
                    $comments[] = $mroPlans[$equipmentUnitId][$mroWorkTypeId]['comments'];
                }
            }
            $row['comments'] = implode('<br>', $comments);

            $allModels[] = $row;
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $allModels,
            'sort' => $this->sortingEnabled ? ['defaultOrder' => $this->defaultOrder] : false,
            'pagination' => $recsOnPage > 0 ? [
                'defaultPageSize' => $recsOnPage,
            ] : false,
        ]);

        $dataProvider->sort->attributes['name'] = [
            'asc' => ['name' => SORT_ASC],
            'desc' => ['name' => SORT_DESC],
        ];

        // Return allModels provider
        return $dataProvider;
    }
}
