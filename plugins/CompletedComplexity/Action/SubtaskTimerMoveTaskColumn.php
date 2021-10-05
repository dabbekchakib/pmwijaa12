<?php

namespace Kanboard\Plugin\CompletedComplexity\Action;

use Kanboard\Model\TaskModel;
use Kanboard\Model\SubtaskModel;

/**
 * Create a subtask and activate the timer when moving a task to another column.
 *
 * @package Kanboard\Action
 * @author  yvalentin
 */
class SubtaskTimerMoveTaskColumn extends \Kanboard\Action\SubtaskTimerMoveTaskColumn
{
    /**
     * Execute the action (append to the task description).
     * SubTask is create with user session (not with creator of the task, on original action on extends class)
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool            True if the action was executed or false when not executed
     */
    public function doAction(array $data)
    {
        $userId = $this->userSession->getId();
        $subtaskID = $this->subtaskModel->create(array(
            'title' => $this->getParam('subtask'),
            'user_id' => $userId,
            'task_id' => $data['task']['id'],
            'status' => SubtaskModel::STATUS_INPROGRESS,
        ));

        if ($subtaskID !== false) {
            return $this->subtaskTimeTrackingModel->toggleTimer($subtaskID, $userId, SubtaskModel::STATUS_INPROGRESS);
        }

        return false;
    }
}
