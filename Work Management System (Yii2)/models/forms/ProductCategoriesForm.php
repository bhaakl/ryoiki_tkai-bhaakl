<?php

namespace app\models\forms;

use app\models\ProductCategory;
use yii\base\Model;

class ProductCategoriesForm extends Model
{
    public $product_categories;
    private $modelClassItem = ProductCategory::class;

    /** @var ProductCategory[]  */
    private $items = [];

    public function rules()
    {
        return [
            ['product_categories', 'validateProductCategories']
        ];
    }

    public function validateProductCategories($attribute)
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