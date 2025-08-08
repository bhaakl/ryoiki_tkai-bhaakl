<?php

namespace app\models\forms;

use app\models\Status;
use yii\base\Model;

class StatusesForm extends Model
{
    public $statuses;
    private $modelClassItem = Status::class;

    /** @var Status[]  */
    private $items = [];

    public function rules()
    {
        return [
            ['statuses', 'validateStatuses']
        ];
    }

    public function validateStatuses($attribute)
    {
        foreach($this->$attribute as $index => $fields) {
            $model = new $this->modelClassItem();
            $model->scenario = $model::SCENARIO_INSERT;
            $model->load($fields, '');
            $model->disableShowErrors();
            if (!$model->validate()) {
                foreach ($model->getErrors() as $name => $error) {
                    $key = $attribute . '[' . $index . '][' . $name . ']';
                    $this->addError($key, $error);
                }
            } else {
                $this->items[] = $model;
            }
        }
    }

    public function save()
    {
        foreach ($this->items as $item) {
            if (!$item->save()) {
                return false;
            }
        }

        return true;
    }
}