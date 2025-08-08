<?php

namespace app\models\forms;

use app\models\SalaryPayments;
use yii\base\Model;

class SalaryPaymentsForm extends Model
{

    public $salaryPayments;
    private string $modelClassItem = SalaryPayments::class;

    /** @var SalaryPayments[]  */
    private array $items = [];

    public function rules()
    {
        return [
            ['salaryPayments', 'validateSalaryPayments']
        ];
    }

    public function validateSalaryPayments($attribute)
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