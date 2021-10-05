<?php

namespace Kanboard\Plugin\CompletedComplexity\Controller;

use Kanboard\Controller\AnalyticController;
use Kanboard\Filter\TaskProjectFilter;
use Kanboard\Model\TaskModel;


/**
 * @plugin CompletedComplexity
 *
 * @package Kanboard\Plugin\CompletedComplexity\Controller
 * @author  yvalentin
 */
class AnalyticCompletedComplexityController extends AnalyticController {

    /**
     * Show comparison between actual and estimated hours chart by Dates
     *
     * @access public
     */
    public function completedComplexity()
    {
        $this->commonAggregateMetrics('CompletedComplexity:analytic/completed_complexity', 'score', t('Completed Complexity'));
    }

    /**
     * Common method for CFD and Burdown chart
     *
     * @access private
     * @param string $template
     * @param string $column
     * @param string $title
     */
    private function commonAggregateMetrics($template, $column, $title)
    {
        $project = $this->getProject();
        list($from, $to, $first_day) = $this->getDates();

        $displayGraph = $this->projectDailyColumnStatsModel->countDays($project['id'], $from, $to) >= 2;
        $metrics = $displayGraph ? $this->projectDailyColumnStatsModel->getAggregatedMetrics($project['id'], $from, $to, $column) : array();

        $this->response->html($this->helper->layout->analytic($template, array(
            'values'        => array(
                'from'      => $from,
                'to'        => $to,
                'first_day' => $first_day,
            ),
            'display_graph' => $displayGraph,
            'metrics'       => $metrics,
            'project'       => $project,
            'title'         => $title,
        )));
    }

    private function getDates()
    {
        $values = $this->request->getValues();

        $from = $this->request->getStringParam('from', date('Y-m-d', strtotime('-3week')));
        $to = $this->request->getStringParam('to', date('Y-m-d'));
        $first_day = $this->request->getStringParam('first_day', 'Monday');

        if (! empty($values)) {
            $from = $this->dateParser->getIsoDate($values['from']);
            $to = $this->dateParser->getIsoDate($values['to']);
            $first_day = $values['first_day'];
        }

        return array($from, $to, $first_day);
    }
}
