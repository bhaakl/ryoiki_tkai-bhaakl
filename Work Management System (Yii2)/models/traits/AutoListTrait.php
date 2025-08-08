<?php


namespace app\models\traits;


use app\models\AutoListItem;

trait AutoListTrait
{
    abstract function getAutoListMap();

    public function updateAutoLists()
    {
        foreach ($this->getAutoListMap() as $attribute => $list_id) {
            if($this->$attribute && !AutoListItem::findOne(['auto_list_id' => $list_id, 'name' => $this->$attribute])) {
                (new AutoListItem(['auto_list_id' => $list_id, 'name' => $this->$attribute]))->save();
            }
        }
    }
}