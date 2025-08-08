<?php

namespace app\models\forms;

use app\models\ProductWProductionStageLink;

class ProductWProductionStagesForm extends BaseMultipleRelationsLoadForm
{
	public $fk_attr_name = 'product_id';
	
    public $key = 'productionStages';
    public $productionStages; // Чтобы load() в эту переменную
	
    public $modelClassItem = ProductWProductionStageLink::class;
}