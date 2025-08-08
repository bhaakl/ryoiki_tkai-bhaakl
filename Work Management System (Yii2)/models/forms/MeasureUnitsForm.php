<?php

namespace app\models\forms;

use app\models\MeasureUnit;
use yii\base\Model;

class MeasureUnitsForm extends Model
{
    public $measure_units;
    private $modelClassItem = MeasureUnit::class;

    /** @var MeasureUnit[]  */
    private $items = [];

    public function rules()
    {
        return [
            ['measure_units', 'validateMeasureUnits']
        ];
    }

    public function validateMeasureUnits($attribute)
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