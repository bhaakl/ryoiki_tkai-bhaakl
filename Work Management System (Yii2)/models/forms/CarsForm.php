<?php

namespace app\models\forms;

use app\models\Car;
use yii\base\Model;

class CarsForm extends Model
{
    public $cars;
    private $modelClassItem = Car::class;

    /** @var Car[]  */
    private $items = [];

    public function rules()
    {
        return [
            ['cars', 'validateCars']
        ];
    }

    public function validateCars($attribute)
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