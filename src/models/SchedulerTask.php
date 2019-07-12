<?php
/**
 * @copyright Copyright(c) 2016 Webtools Ltd
 * @copyright Copyright(c) 2018 Thamtech, LLC
 * @link https://github.com/thamtech/yii2-scheduler
 * @license https://opensource.org/licenses/MIT
**/

namespace thamtech\scheduler\models;

use Yii;
use yii\helpers\Inflector;

/**
 * This is the model class for table "scheduler_task".
 */
class SchedulerTask extends \thamtech\scheduler\models\base\SchedulerTask
{
    const STATUS_INACTIVE = 0;
    const STATUS_PENDING = 10;
    const STATUS_DUE = 20;
    const STATUS_RUNNING = 30;
    const STATUS_OVERDUE = 40;
    const STATUS_ERROR = 50;

    public $active = true;

    /**
     * @var array
     */
    private static $_statuses = [
        self::STATUS_INACTIVE => 'Inactive',
        self::STATUS_PENDING => 'Pending',
        self::STATUS_DUE => 'Due',
        self::STATUS_RUNNING => 'Running',
        self::STATUS_OVERDUE => 'Overdue',
        self::STATUS_ERROR => 'Error',
    ];

    /**
     * Return Taskname
     *
     * @return string
     */
    public function __toString()
    {
        return Inflector::camel2words($this->name);
    }

    /**
     *
     * @param string $name
     *
     * @param Task $task
     *
     * @return array|null|SchedulerTask|\yii\db\ActiveRecord
     */
    public static function createTaskModel($name, $task)
    {
        $model = SchedulerTask::find()
            ->where(['name' => $name])
            ->one();

        if (!$model) {
            $model = new SchedulerTask();
            $model->name = $name;
            $model->display_name = $task->displayName;
            $model->next_run = $task->getNextRunDate();
            $model->last_run = NULL;
            $model->status_id = self::STATUS_PENDING;
            $model->active = $task->active;
        }

        $model->description = $task->description;
        $model->schedule = $task->schedule;
        $model->save(false);

        return $model;
    }

    /**
     * @return string|null
     */
    public function getStatus()
    {
        return isset(self::$_statuses[$this->status_id]) ? self::$_statuses[$this->status_id] : null;
    }


    /**
     * Update the status of the task based on various factors.
     */
    public function updateStatus()
    {
        $status = $this->status_id;
        $isDue = in_array(
            $status,
            [
                self::STATUS_PENDING,
                self::STATUS_DUE,
                self::STATUS_OVERDUE,
            ]
        ) && strtotime($this->next_run) <= time();

        if ($isDue && $this->started_at == null) {
            $status = self::STATUS_DUE;
        } elseif ($this->started_at !== null) {
            $status = self::STATUS_RUNNING;
        } elseif ($this->status_id == self::STATUS_ERROR) {
            $status = $this->status_id;
        } elseif (!$isDue) {
            $status = self::STATUS_PENDING;
        }

        if (!$this->active) {
            $status = self::STATUS_INACTIVE;
        }

        $this->status_id = $status;
    }

    public function beforeSave($insert)
    {
        $this->updateStatus();
        return parent::beforeSave($insert);
    }
}
