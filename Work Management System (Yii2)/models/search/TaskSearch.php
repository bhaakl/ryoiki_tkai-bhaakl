<?php

namespace app\models\search;

use app\helpers\Date as DateHelper;
use app\models\Task;
use app\models\Status;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Модель для поиска Order
 */
class TaskSearch extends Task
{
    public $search = '';

    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'performer_id'
        ]);
    }

    public function rules()
    {
        return [
            [['search', 'when_set', 'deadline', 'when_completed', 'id'], 'safe'],
            [['setter_id', 'responsible_id', 'performer_id', 'status_id', 'creator_id', 'acceptance_status_id'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'search' => Yii::t('app', 'Поиск'),
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
        $query = Task::find();
        $query->andWhere(['task.is_deleted' => 0]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => $this->sortingEnabled ? ['defaultOrder' => $this->defaultOrder] : false,
            'pagination' => $recsOnPage > 0 ? [
                'defaultPageSize' => $recsOnPage,
            ] : false,
        ]);

        $query->joinWith('setter as setter');
        $dataProvider->sort->attributes['setter.fio'] = [
            'asc' => ['setter.fio' => SORT_ASC],
            'desc' => ['setter.fio' => SORT_DESC],
         ];

        $query->joinWith('responsible as responsible');
        $dataProvider->sort->attributes['responsible.fio'] = [
            'asc' => ['responsible.fio' => SORT_ASC],
            'desc' => ['responsible.fio' => SORT_DESC],
         ];

        $this->search = trim($this->search);
        if ($this->search) {
            $or = [
                'or',
                ['like', 'task.task', $this->search],
                ['like', 'task.solution', $this->search],
                ['like', 'task.comments', $this->search],
            ];
            $query->andWhere($or);
        }

        $query->andFilterWhere(['=', 'setter_id', $this->getAttribute('setter_id')]);
        $query->andFilterWhere(['=', 'responsible_id', $this->getAttribute('responsible_id')]);
        $query->andFilterWhere(['=', 'status_id', $this->getAttribute('status_id')]);
        $query->andFilterWhere(['=', 'acceptance_status_id', $this->getAttribute('acceptance_status_id')]);

        if ($this->getAttribute('performer_id')) {
            $query->andWhere(['EXISTS', (new \yii\db\Query())
                ->select('performer.id')
                ->from('task_w_performer_link')
                ->join('INNER JOIN', 'user as performer', 'performer.id = task_w_performer_link.performer_id')
                ->where(['=', 'task_w_performer_link.performer_id', $this->getAttribute('performer_id')])
                ->andWhere('task_w_performer_link.task_id = task.id')
                ->andWhere(['task_w_performer_link.is_deleted' => 0])
                ->andWhere(['performer.is_deleted' => 0])
            ]);
        }

        if ($this->getAttribute('when_set') && !empty($dateRange = DateHelper::parseDateRange($this->getAttribute('when_set')))) {
            $query->andWhere(['between', 'when_set', $dateRange['from'], $dateRange['to']]);
        }
        if ($this->getAttribute('deadline') && !empty($dateRange = DateHelper::parseDateRange($this->getAttribute('deadline')))) {
            $query->andWhere(['between', 'deadline', $dateRange['from'], $dateRange['to']]);
        }
        if ($this->getAttribute('when_completed') && !empty($dateRange = DateHelper::parseDateRange($this->getAttribute('when_completed')))) {
            $query->andWhere(['between', 'when_completed', $dateRange['from'], $dateRange['to']]);
        }

        return $dataProvider;
	}

}
