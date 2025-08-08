<?php

namespace app\models\forms;

use app\models\ProductionLocationCategory;
use yii\base\Model;

class ProductionLocationCategoriesForm extends Model
{
    public $production_location_categories;
    private $modelClassItem = ProductionLocationCategory::class;

    /** @var ProductionLocationCategory[]  */
    private $items = [];

    public function rules()
    {
        return [
            ['production_location_categories', 'validateProductionLocationCategories']
        ];
    }

    public function validateProductionLocationCategories($attribute)
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