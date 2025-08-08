<?php


namespace app\controllers;


use app\models\AutoList;
use app\models\AutoListItem;
use app\models\BaseActiveRecord;
use app\widgets\ActiveForm;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class AutoListsController extends AjaxController
{
    public $modelClass = AutoList::class;
    public $accessRules = [
        [
            'actions' => ['predict'],
            'allow' => true,
            'roles' => ['@'],
        ],
    ];

    public function actionPredict($id, $value)
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);
            $result = AutoListItem::find()->select(['name'])->where(['auto_list_id' => $model->id])->andWhere(['like', 'name', $value])->orderBy('name')->limit(10)->column();

            return ['items' => $result];
        }
        throw new BadRequestHttpException();
    }
}