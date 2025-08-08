<?php

namespace app\controllers;

use app\grid\GridModelActionsInterface;
use app\models\BaseActiveRecord;
use app\models\Division;
use app\models\EquipmentUnit;
use app\models\Job;
use app\models\search\EquipmentRepairSearch;
use app\models\EquipmentCategory;
use app\models\EquipmentRepair;
use app\models\forms\EquipmentRepairsForm;
use app\models\Status;
use app\models\User;
use app\widgets\ActiveForm;
use app\widgets\Flashes;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Response;

/**
 * EquipmentRepairs Controller
 */
class EquipmentRepairsController extends BaseController
{
    public $modelClass = EquipmentRepair::class;
    public $searchClass = EquipmentRepairSearch::class;
    public $multipleFormClass = EquipmentRepairsForm::class;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['app_equipment-repairs_index'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'roles' => ['app_equipment-repairs_view'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['app_equipment-repairs_create'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['edit'],
                        'roles' => ['app_equipment-repairs_edit'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['app_equipment-repairs_delete'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $this->addDictionaries();

        return parent::actionIndex();
    }

    public function actionView($id)
    {
        $model = EquipmentRepair::findOne($id);

        if ($model && $model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();
            return $this->redirect(['equipment-repairs/index']);
        }

        $this->addDictionaries();

        return $this->renderAjax('_view', [
            'model' => $model,
            'data' => $this->data,
        ]);
    }

    public function actionCreate()
    {
        $model = $this->newModel();
        $model->scenario = BaseActiveRecord::SCENARIO_INSERT;

        Flashes::clear();

        if ($model->load(Yii::$app->request->post())) {

            if ($model->save()) {
                if ($repairerIds = Yii::$app->request->post('EquipmentRepair')['repairers']) {
                    foreach ($repairerIds as $repairerId) {
                        $repairer = User::findOne(['id' => $repairerId, 'is_deleted' => 0]);

                        if ($repairer instanceof User) {
                            $model->link('repairers', $repairer);
                        }
                    }
                }

                Flashes::setSuccess('Запись добавлена');
                return $this->redirect(['index']);
            }

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
        }

        $this->addDictionaries();

        return $this->render($this->createTemplate, [
            'title' => $this->sysTitle(),
            'model' => $model,
            'data'  => $this->data,
        ]);
    }

    public function actionEdit($id)
    {
        $model = $this->findModel($id);
        $model->scenario = BaseActiveRecord::SCENARIO_UPDATE;

        Flashes::clear();

        if (!isset($model)) {
            Flashes::setError('Запись не найдена');
            return $this->redirect(['index']);
        }

        if ($model instanceof GridModelActionsInterface && !$model->actionAllowed('edit')) {
            return $this->redirect(['index']);
        }

        if ($model->load(Yii::$app->request->post())) {

            if ($model->save()) {
                if (!empty($model->repairers)) {
                    foreach ($model->repairers as $repairer) {
                        $model->unlink('repairers', $repairer, true);
                    }
                }

                if ($repairerIds = Yii::$app->request->post('EquipmentRepair')['repairers']) {
                    foreach ($repairerIds as $repairerId) {
                        $repairer = User::findOne(['id' => $repairerId, 'is_deleted' => 0]);

                        if ($repairer instanceof User) {
                            $model->link('repairers', $repairer);
                        }
                    }
                }

                Flashes::setSuccess('Запись обновлена');
                return $this->redirect(['index']);
            }

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
        }

        $this->addDictionaries();

        return $this->render($this->editTemplate, [
            'title' => $this->sysTitle(),
            'model' => $model,
            'data'  => $this->data,
        ]);
    }

    private function addDictionaries()
    {
        $this->data['categories'] = ArrayHelper::map(
            EquipmentCategory::find()
                ->where(['is_deleted' => 0])
                ->orderBy('name')
                ->all(),
            'id',
            'name'
        );

        $this->data['equipmentUnits'] = ArrayHelper::map(
            EquipmentUnit::find()
                ->where(['is_deleted' => 0])
                ->andWhere(['used_on_equipment_repair' => 1])
                ->orderBy('name')
                ->all(),
            'id',
            'name'
        );

        $this->data['jobs'] = ArrayHelper::map(
            Job::find()
                ->where(['is_deleted' => 0])
                ->orderBy('name')
                ->all(),
            'id',
            'name'
        );

        $this->data['divisions'] = ArrayHelper::map(
            Division::find()
                ->where(['is_deleted' => 0])
                ->orderBy('name')
                ->all(),
            'id',
            'name'
        );

        $this->data['statuses'] = ArrayHelper::map(
            Status::find()
                ->where(['is_deleted' => 0])
                ->andWhere(['used_on_equipment_repair' => 1])
                ->orderBy('name')
                ->all(),
            'id',
            'name'
        );

        $this->data['users'] = ArrayHelper::map(
            User::find()
                ->where(['is_deleted' => 0])
                ->orderBy('fio')
                ->all(),
            'id',
            'fio'
        );
    }
}
