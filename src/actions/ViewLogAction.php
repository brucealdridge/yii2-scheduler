<?php
/**
 * @copyright Copyright(c) 2016 Webtools Ltd
 * @copyright Copyright(c) 2018 Thamtech, LLC
 * @link https://github.com/thamtech/yii2-scheduler
 * @license https://opensource.org/licenses/MIT
**/

namespace thamtech\scheduler\actions;

use Yii;
use yii\base\Action;
use thamtech\scheduler\models\SchedulerLog;

/**
 * View a log entry.
 */
class ViewLogAction extends Action
{
    /**
     * @var string the view file to be rendered. If not set, it will take the value of [[id]].
     * That means, if you name the action as "index" in "SchedulerController", then the view name
     * would be "index", and the corresponding view file would be "views/scheduler/index.php".
     */
    public $view;

    /**
     * Runs the action
     *
     * @return string result content
     */
    public function run($id)
    {
        $model = SchedulerLog::findOne($id);

        if (!$model) {
            throw new \yii\web\HttpException(404, 'The requested page does not exist.');
        }

        return $this->controller->render($this->view ?: $this->id, [
            'model' => $model,
        ]);
    }
}
