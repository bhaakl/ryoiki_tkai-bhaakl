<?php

namespace app\controllers;

use app\widgets\ActiveForm;
use Yii;
use app\models\User;
use yii\web\Response;

/**
 * People Controller
 */
class PeopleController extends AjaxController
{
    public $modelClass = User::class;

    public function actionView($id)
    {
        if (Yii::$app->request->isAjax) {
            $model = $this->findModel($id);
            return $this->renderPartial('_view', ['model' => $model]);
        }
        return parent::actionView($id);
    }

    public function actionEdit($id, $is_account=false)
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);
            if(Yii::$app->request->isGet) {
                return ['roleName' => $model->roleName];
            }
            $model->disableShowErrors();
            if($is_account) {
                $model->scenario = User::SCENARIO_ACCOUNT;
            } else {
                $model->scenario = User::SCENARIO_UPDATE;
            }

            if ($model->load(Yii::$app->request->post(), '')) {
                $errors = ActiveForm::validate($model);
                if (empty($errors)) {
                    return ['result' => $model->save()];
                } else {
                    return ['result' => false, 'errors' => $errors];
                }
            }
        }
        return parent::actionEdit($id);
    }
}
