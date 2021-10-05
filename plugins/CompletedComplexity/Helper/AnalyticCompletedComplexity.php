<?php

namespace Kanboard\Plugin\CompletedComplexity\Helper;

use Kanboard\Core\Base;
use Kanboard\Model\TaskModel;

/**
 * Analytics Times Comparison
 *
 * @package  AnalyticCompletedComplexity
 * @author   smacz
 */
class AnalyticCompletedComplexity extends Base
{

    /**
     * Build report
     *
     * @param $project_id
     * @param $model
     * @param $column
     *
     * @return array
     */
    public function build($project_id, $model, $column)
    {
        $rows = $this->db->table(TaskModel::TABLE)
                         ->columns('SUM(time_estimated) AS time_estimated', 'SUM(time_spent) AS time_spent', 'is_active', $column)
                         ->eq('project_id', $project_id)
                         ->groupBy('is_active', $column)
                         ->findAll();

//        ->gte('day', $from)
//        ->lte('day', $to)

        $metrics    = [];
        $names  = [];

        foreach ($rows as $row) {
            if (!array_key_exists($row[$column],$names)) {
                $name = $this->$model->getNameById($row[$column]);
                $names[$row[$column]] = empty($name) ? t("Default") : $name;
                $metrics[$names[$row[$column]]]['open']['time_spent'] = 0;
                $metrics[$names[$row[$column]]]['open']['time_estimated'] = 0;
                $metrics[$names[$row[$column]]]['closed']['time_spent'] = 0;
                $metrics[$names[$row[$column]]]['closed']['time_estimated'] = 0;
            }
            $key = $row['is_active'] == TaskModel::STATUS_OPEN ? 'open' : 'closed';
            $metrics[$names[$row[$column]]][$key]['time_spent'] = round((float) $row['time_spent'],2);
            $metrics[$names[$row[$column]]][$key]['time_estimated'] = round((float) $row['time_estimated'],2);
        }

        return array($metrics, $names);
    }

    /**
     * @param $project_id
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return array
     */
    public function buildByDates($project_id, \DateTime $from, \DateTime $to)
    {
        $rows = $this->db->table(TaskModel::TABLE)
                         ->columns(
                             'tasks.id',
                             'subtasks.id as s_id',
                             'subtask_time_tracking.id as stt_id',
                             'is_active',
                             'category_id',
                             'swimlane_id',
                             'tasks.time_spent',
                             'subtasks.time_spent as s_time_spent',
                             'subtask_time_tracking.time_spent as stt_time_spent',
                             'subtask_time_tracking.start as stt_start',
                             'subtask_time_tracking.end as stt_end'
                         )
                         ->eq('project_id', $project_id)
                         ->join(SubtaskModel::TABLE, 'task_id', 'id')
                         ->join(SubtaskTimeTrackingModel::TABLE, 'subtask_id', 'id', SubtaskModel::TABLE)
                        ->orderBy('tasks.id')
                         ->findAll();

        $tasks = [];
        $categories = [];
        $swimlanes = [];
        $tmp = [];
        foreach ($rows as $row) {
            $task = $row['id'];
            $subtask = $row['s_id'];
            $stt = $row['stt_id'];
            // Has sub tasks with time tracking
            if (!is_null($task) && !is_null($subtask) && !is_null($stt)) {
                // set Datetime
                $sttStart = (new \DateTime())->setTimestamp($row['stt_start']);
                $sttEnd = (new \DateTime())->setTimestamp($row['stt_end']);
                // check if start after the date from and  end date before date to
                if ($this->isInDate($sttStart, $sttEnd, $from, $to)) {
                    // recalculate time spent
                    // Get time spent for the period form
                    $startCal = $sttStart <= $from ? $from : $sttStart;
                    $endCal = $sttEnd > $to ? $to : $sttEnd;
                    // Override stt_time_spent
                    $row['stt_time_spentt'] = $this->dateParser->getHours($startCal, $endCal);
                    // regroup data by task id and subTask for debug
                    $tmp[$row['id']][$row['s_id']][$row['stt_id']] = $row;
                    // Get value of spentTime to recalculate
                    $this->initAndAdd($tasks, $task, $row);

                    if (!array_key_exists($row['category_id'],$categories)) {
                        $categories[$row['category_id']] = $this->categoryModel->getNameById($row['category_id']);
                    }
                    if (!array_key_exists($row['swimlane_id'],$swimlanes)) {
                        $swimlanes[$row['swimlane_id']] = $this->swimlaneModel->getNameById($row['swimlane_id']);
                    }
                }
            }
        }
        return array($tasks, $categories, $swimlanes);
    }

    /**
     * @param array $tasks
     * @param $taskId
     * @param array $row
     */
    private function initAndAdd(array &$tasks, $taskId, array $row)
    {
        if(!array_key_exists($taskId, $tasks)) {
            $tasks[$taskId]['open']['stt_time_spent'] = 0;
            $tasks[$taskId]['closed']['stt_time_spent'] = 0;
            $tasks[$taskId]['open']['time_spent'] = 0;
            $tasks[$taskId]['closed']['time_spent'] = 0;
            $tasks[$taskId]['swimlane_id'] = $row['swimlane_id'];
            $tasks[$taskId]['category_id'] = $row['category_id'];
        }
        $isActive = $row['is_active'] == TaskModel::STATUS_OPEN ? 'open' : 'closed';
        $tasks[$taskId][$isActive]['stt_time_spent'] += $row['stt_time_spent'];
        $tasks[$taskId][$isActive]['time_spent'] = round((float) $row['time_spent'],2);
    }

    private function isInDate(\DateTime $sttStart, \DateTime $sttEnd, \DateTime $from, \DateTime $to)
    {
        return (
            // stt is In the $from boundary
            (($sttStart <= $from) && ($sttEnd > $from))
            // stt is In the $to boundary
           || (($sttStart < $to) && ($sttEnd >= $to))
           // stt is In the $from and $to boundary
           || (($sttStart >= $from) && ($sttEnd < $to ))
        );
    }
}
