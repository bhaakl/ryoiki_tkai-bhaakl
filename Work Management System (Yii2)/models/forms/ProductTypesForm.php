<?php

namespace app\models\forms;

use app\models\ProductType;
use yii\base\Model;

class ProductTypesForm extends Model
{
    public $product_types;
    private $modelClassItem = ProductType::class;

    /** @var ProductType[]  */
    private $items = [];

    public function rules()
    {
        return [
            ['product_types', 'validateProductTypes']
        ];
    }

    public function validateProductTypes($attribute)
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