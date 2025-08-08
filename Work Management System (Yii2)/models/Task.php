<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use app\helpers\Date;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%task}}".
 *
 * @property int $id
 * @property string $when_set Когда задача поставлена
 * @property string|null $deadline Крайний срок
 * @property string|null $when_completed Когда задача выполнена
 * @property int $status_id
 * @property int|null $acceptance_status_id
 * @property int $setter_id Постановщик задачи
 * @property int|null $responsible_id Ответственный по задаче
 * @property string|null $task Формулировка задачи
 * @property string|null $solution Решение
 * @property string|null $comments Любые комментарии
 * @property int $is_deleted
 * @property string|null $created
 * @property int|null $creator_id
 * @property string|null $modified
 * @property int|null $modifier_id
 *
 * @property TaskAcceptanceStatus $acceptanceStatus
 * @property User $creator
 * @property ForTaskPerformerFile[] $forTaskPerformerFiles
 * @property ForTaskPerformerLink[] $forTaskPerformerLinks
 * @property FromTaskPerformerFile[] $fromTaskPerformerFiles
 * @property FromTaskPerformerLink[] $fromTaskPerformerLinks
 * @property User $modifier
 * @property User[] $performers
 * @property User $responsible
 * @property User $setter
 * @property Status $status
 * @property TaskWPerformerLink[] $taskWPerformerLinks
 */
class Task extends BaseActiveRecord
{
    public $_taskWPerformerLinks;
    public $_forTaskPerformerLinks;
    public $_fromTaskPerformerLinks;
    public $_forTaskPerformerFiles;
    public $_fromTaskPerformerFiles;

    public $linkClass = [
        TaskWPerformerLink::class,
        ForTaskPerformerLink::class,
        FromTaskPerformerLink::class,
    ];

    protected function processUploadedFiles($fileModelClass)
    {
        $modelClassName = StringHelper::basename($fileModelClass);
        $attribute = '_' . lcfirst($modelClassName) . 's';
        $files = UploadedFile::getInstances($this, $attribute);
        foreach ($files as $file) {
            $fileModel = new $fileModelClass();
            $fileModel->task_id = $this->id;
            $baseName = \Yii::$app->security->generateRandomString(8);
            $fileName = "{$baseName}.{$file->extension}";

            $filePath = Yii::getAlias('@webroot/uploads/') . $fileName;
            if ($file->saveAs($filePath)) {
                $fileModel->file_path = $fileName;
                $fileModel->name = $file->baseName . '.' . $file->extension;

                if (!$fileModel->save()) {
                    Yii::error("Ошибка сохранения файла: " . print_r($fileModel->getErrors(), true));
                }
            }
        }
    }

    protected function processSubForms($modelClass)
    {
        $modelClassName = StringHelper::basename($modelClass);
        $variableName = lcfirst($modelClassName) . 's';
        $_variableName = '_' . $variableName;

        $ids = empty($this->$_variableName) ? [] : array_filter(array_column($this->$_variableName, 'id'));
        $modelClass::deleteAll([ 'and', ['task_id' => $this->id], ['not in', 'id', $ids] ]);

        $models = $this->$variableName;
        $data[$modelClassName] = $this->$_variableName;
        $modelClass::loadMultiple($models, $data);
        foreach ($models as $model) {
            if (!$model->validate() || !$model->save()) {
                Yii::error('Validation errors: ' . print_r($model->getErrors(), true));
            }
        }

        $newRecords = empty($this->$_variableName)
            ? []
            : array_filter($this->$_variableName, function($m) { return !$m['id']; });
        foreach ($newRecords as $data[$modelClassName]) {
            $data[$modelClassName]['task_id'] = $this->id;
            $model = new $modelClass();
            $model->load($data);
            if (!$model->validate() || !$model->save()) {
                Yii::error('Validation errors: ' . print_r($model->getErrors(), true));
            }
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $this->processSubForms(TaskWPerformerLink::class);
        $this->processSubForms(ForTaskPerformerLink::class);
        $this->processSubForms(FromTaskPerformerLink::class);
       	$this->processUploadedFiles(ForTaskPerformerFile::class);
        $this->processUploadedFiles(FromTaskPerformerFile::class);
    }

    public function afterValidate()
    {
        parent::afterValidate();

        foreach ($this->linkClass as $modelClass) {
            $modelClassName = StringHelper::basename($modelClass);
            $variableName = lcfirst($modelClassName) . 's';
            $_variableName = '_' . $variableName;

            if (empty($this->$_variableName)) continue;

            $ids = empty($this->$_variableName) ? [] : array_filter(array_column($this->$_variableName, 'id'));

            $models = $this->$variableName;
            $data[$modelClassName] = $this->$_variableName;
            $modelClass::loadMultiple($models, $data);
            foreach ($models as $key => $model) {
                if (!$model->validate()) {
    				Yii::error('Validation errors: ' . print_r($model->getErrors(), true));
                    $errors = $model->getErrors();
                    foreach ($errors as $attribute => $error) {
                        $this->addError($_variableName."[$key][$attribute]", $error);
                    }
    			}
            }

            $newRecords = empty($this->$_variableName)
                ? []
                : array_filter($this->$_variableName, function($m) { return !$m['id']; });
            foreach ($newRecords as $key => $data[$modelClassName]) {
                $model = new $modelClass();
                $model->load($data);
                $model->task_id = $this->id;
                $model->scenario = $this->scenario;
                if (!$model->validate()) {
    				Yii::error('Validation errors: ' . print_r($model->getErrors(), true));
                    $errors = $model->getErrors();
                    foreach ($errors as $attribute => $error) {
                        $this->addError($_variableName."[$key][$attribute]", $error);
                    }
    			}
            }

        }
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['task', 'when_set', 'status_id', 'setter_id'], 'required', 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
            [['deadline', 'when_completed', 'when_set', 'created', 'modified', '_taskWPerformerLinks', '_forTaskPerformerLinks', '_fromTaskPerformerLinks'], 'safe'],
            [['status_id', 'acceptance_status_id', 'setter_id', 'responsible_id', 'is_deleted', 'creator_id', 'modifier_id'], 'integer'],
            [['task', 'solution', 'comments'], 'string'],
            [['deadline', 'when_completed'], 'filter', 'filter' => function ($value) {
                return Date::convertDateRuToIso($value);
            }],
            [['_forTaskPerformerFiles', '_fromTaskPerformerFiles'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10],
            [['acceptance_status_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskAcceptanceStatus::class, 'targetAttribute' => ['acceptance_status_id' => 'id']],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['creator_id' => 'id']],
            [['modifier_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['modifier_id' => 'id']],
            [['responsible_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['responsible_id' => 'id']],
            [['setter_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['setter_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => Status::class, 'targetAttribute' => ['status_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'when_set' => Yii::t('app', 'Дата постановки'),
            'deadline' => Yii::t('app', 'Крайний срок'),
            'when_completed' => Yii::t('app', 'Дата выполнения'),
            'status_id' => Yii::t('app', 'Статус'),
            'acceptance_status_id' => Yii::t('app', 'Принято?'),
            'setter_id' => Yii::t('app', 'Постановщик'),
            'responsible_id' => Yii::t('app', 'Ответственный'),
            'task' => Yii::t('app', 'Задача'),
            'solution' => Yii::t('app', 'Решение'),
            'comments' => Yii::t('app', 'Комментарии'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'created' => Yii::t('app', 'Created'),
            'creator_id' => Yii::t('app', 'Creator ID'),
            'modified' => Yii::t('app', 'Modified'),
            'modifier_id' => Yii::t('app', 'Modifier ID'),
            'performer_id' => Yii::t('app', 'Исполнитель'),

			'performers' => Yii::t('app', 'Исполнители'),
			'forTaskPerformerLinks' => Yii::t('app', 'Ссылки для исполнителей'),
			'fromTaskPerformerLinks' => Yii::t('app', 'Ссылки от исполнителей'),
			'forTaskPerformerFiles' => Yii::t('app', 'Файлы для исполнителей'),
			'fromTaskPerformerFiles' => Yii::t('app', 'Файлы от исполнителей'),

			'forTaskPerformerFiles' => Yii::t('app', 'Файлы для исполнителей'),
            'fromTaskPerformerFiles' => Yii::t('app', 'Файлы от исполнителей'),
        ];
    }

    /**
     * Gets query for [[AcceptanceStatus]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAcceptanceStatus()
    {
        return $this->hasOne(TaskAcceptanceStatus::class, ['id' => 'acceptance_status_id'])->inverseOf('tasks');
    }

    /**
     * Gets query for [[Creator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'creator_id'])->inverseOf('tasks');
    }

    /**
     * Gets query for [[ForTaskPerformerFiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getForTaskPerformerFiles()
    {
        return $this->hasMany(ForTaskPerformerFile::class, ['task_id' => 'id'])->inverseOf('task');
    }

    /**
     * Gets query for [[ForTaskPerformerLinks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getForTaskPerformerLinks()
    {
        return $this->hasMany(ForTaskPerformerLink::class, ['task_id' => 'id'])->inverseOf('task');
    }

    /**
     * Gets query for [[FromTaskPerformerFiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFromTaskPerformerFiles()
    {
        return $this->hasMany(FromTaskPerformerFile::class, ['task_id' => 'id'])->inverseOf('task');
    }

    /**
     * Gets query for [[FromTaskPerformerLinks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFromTaskPerformerLinks()
    {
        return $this->hasMany(FromTaskPerformerLink::class, ['task_id' => 'id'])->inverseOf('task');
    }

    /**
     * Gets query for [[Modifier]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getModifier()
    {
        return $this->hasOne(User::class, ['id' => 'modifier_id'])->inverseOf('tasks0');
    }

    /**
     * Gets query for [[Performers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPerformers()
    {
        return $this->hasMany(User::class, ['id' => 'performer_id'])->via('taskWPerformerLinks');
    }

    /**
     * Gets query for [[Responsible]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponsible()
    {
        return $this->hasOne(User::class, ['id' => 'responsible_id'])->inverseOf('tasks1');
    }

    /**
     * Gets query for [[Setter]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSetter()
    {
        return $this->hasOne(User::class, ['id' => 'setter_id'])->inverseOf('tasks2');
    }

    /**
     * Gets query for [[Status]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(Status::class, ['id' => 'status_id'])->inverseOf('tasks');
    }

    /**
     * Gets query for [[TaskWPerformerLinks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskWPerformerLinks()
    {
        return $this->hasMany(TaskWPerformerLink::class, ['task_id' => 'id'])->inverseOf('task');
    }

}
