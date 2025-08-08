<?php

namespace app\models\forms;

use app\models\PayrollTransactionCategory;
use yii\base\Model;

class PayrollTransactionCategoriesForm extends Model
{
    public $payroll_transaction_categories;
    private $modelClassItem = PayrollTransactionCategory::class;

    /** @var PayrollTransactionCategory[]  */
    private $items = [];

    public function rules()
    {
        return [
            ['payroll_transaction_categories', 'validatePayrollTransactionCategories']
        ];
    }

    public function validatePayrollTransactionCategories($attribute)
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