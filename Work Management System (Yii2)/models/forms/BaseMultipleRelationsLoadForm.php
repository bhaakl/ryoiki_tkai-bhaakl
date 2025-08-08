<?php

namespace app\models\forms;

use yii\base\Model;

class BaseMultipleRelationsLoadForm extends BaseMultipleLoadForm
{
    public $fk_attr_name; // Имя атрибута внешнего ключа.
    public $fk_attr_value; // Значение атрибута внешнего ключа.
	// Нужно для привязки экземпляра этой модели к экземпляру другой модели (много "этих" моделей, одна "другая")
	
	protected function afterLoadModel($model)
    {
        $attr_name = $this->fk_attr_name;
		$model->$attr_name = $this->fk_attr_value;
		return $model;
    }
}