<?php if (! $is_ajax): ?>
    <div class="page-header">
        <h2><?= t('Completed Complexity') ?></h2>
    </div>
<?php endif ?>

<?php if (! $display_graph): ?>
    <p class="alert"><?= t('You need at least 2 days of data to show the chart.') ?></p>
<?php else: ?>
    <?= $this->app->component('chart-project-analytics-completed-complexity', array(
        'metrics' => $metrics,
        'labelTotal' => t('Total for all columns'),
        'dateFormat' => e('%%Y-%%m-%%d'),
        'first_day' => $values['first_day'],
    )) ?>
<?php endif ?>

<hr/>

<form method="post" class="form-inline" action="<?= $this->url->href(
        'AnalyticCompletedComplexityController',
        'completedComplexity',
        array('plugin' => 'CompletedComplexity', 'project_id' => $project['id']))
    ?>"
    autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->date(t('Start date'), 'from', $values) ?>
    <?= $this->form->date(t('End date'), 'to', $values) ?>
    <?= $this->form->label(t('First Day of the Week'), 'first_day') ?>
    <?= $this->form->select('first_day', array_combine($this->helper->dt->getWeekDays(), $this->helper->dt->getWeekDays()), $values, array()) ?>
    <?= $this->modal->submitButtons(array('submitLabel' => t('Execute'))) ?>
</form>

<p class="alert alert-info"><?= t('This chart shows the completed complexity (throughput) of tasks moved into the right-most column over the time interval.') ?></p>
