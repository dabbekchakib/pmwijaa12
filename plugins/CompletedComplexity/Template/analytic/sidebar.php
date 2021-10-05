<?php
/**
 * Created by yvalentin.
 * https://yohannvalentin.com
 *
 * Date: 12/11/18
 */
?>

<!--TODO check if project has automatic action: task.move.column -  \Kanboard\Action\SubtaskTimerMoveTaskColumn -->
<li <?= $this->app->checkMenuSelection('AnalyticCompletedComplexityController', 'completedComplexity') ?>>
    <?= $this->modal->replaceLink(
        t('Completed Complexity'),
        'AnalyticCompletedComplexityController',
        'completedComplexity',
        array(
            'plugin' => 'CompletedComplexity',
            'project_id' => $project['id']
        )
    )
    ?>
</li>
