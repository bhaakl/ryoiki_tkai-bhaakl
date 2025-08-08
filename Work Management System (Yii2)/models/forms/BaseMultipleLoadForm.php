<?php

namespace app\models\forms;

use yii\base\Model;

class BaseMultipleLoadForm extends Model
{
    public $key; // имя ключа в запросе, в котором передаётся массив для создания новых записей
    public $modelClassItem; // Класс модели ActiveRecord

    /** @var $modelClassItem::class[] */
    private $items = [];

    public function rules()
    {
        return [
            [$this->key, 'validateModels']
        ];
    }

    public function validateModels($attribute)
    {
        foreach($this->$attribute as $index => $fields) {
            static::validateModel($attribute, $index, $fields);
        }
    }
	
	protected function afterLoadModel($model)
    {
        return $model;
    }
	
	protected function validateModel($attribute, $index, $fields)
    {
        $model = new $this->modelClassItem();
		$model->scenario = $model::SCENARIO_INSERT;
		$model->load($fields, '');
		$model = static::afterLoadModel($model);
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