<?php

namespace app\controllers;

use app\models\BaseActiveRecord;
use app\models\Notification;
use app\widgets\Flashes;
use Yii;

/**
 * Notifications Controller
 */
class NotificationsController extends BaseController
{
    public $modelClass = Notification::class;
    public function actionIndex()
    {
        $params = Yii::$app->request->queryParams;
        $searchModel = new $this->modelClass();
        $searchModel->scenario = BaseActiveRecord::SCENARIO_SEARCH;
        $dataProvider = $searchModel->search($params, $this->recsOnPage);

        if ($this->request->isPost) {
            $model = $this->findModel(Yii::$app->request->post('id'));
            $model->load(Yii::$app->request->post());

            if (!$model->validate() || !$model->save()) {
                Flashes::setError('Ошибка сохранения.');
                Yii::error('Validation errors: ' . print_r($model->getErrors(), true));
            } else {
                Flashes::setSuccess('Изменения сохранены');
            }
        }

        return $this->render($this->indexTemplate, [
            'title'        => $this->sysTitle(),
            'dataProvider' => $dataProvider,
        ]);
    }
}
