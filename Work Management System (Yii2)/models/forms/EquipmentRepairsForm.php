<?php

namespace app\models\forms;

use app\models\EquipmentRepair;
use yii\base\Model;

class EquipmentRepairsForm extends Model
{
    public $equipment_repairs;
    private $modelClassItem = EquipmentRepair::class;

    /** @var EquipmentRepair[]  */
    private $items = [];

    public function rules()
    {
        return [
            ['equipment_repairs', 'validateEquipmentRepairs']
        ];
    }

    public function validateEquipmentRepairs($attribute)
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