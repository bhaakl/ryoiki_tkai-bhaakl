<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%task_acceptance_status}}".
 *
 * @property int $id
 * @property string $name
 * @property string|null $comments Любые комментарии
 * @property int $is_deleted
 *
 * @property Task[] $tasks
 */
class TaskAcceptanceStatus extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%task_acceptance_status}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['comments'], 'string'],
            [['is_deleted'], 'integer'],
            [['name'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'comments' => Yii::t('app', 'Comments'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
        ];
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Task::class, ['acceptance_status_id' => 'id'])->inverseOf('acceptanceStatus');
    }
}
