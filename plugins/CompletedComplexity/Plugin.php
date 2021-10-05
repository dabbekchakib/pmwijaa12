<?php
namespace Kanboard\Plugin\CompletedComplexity;

use Kanboard\Core\Plugin\Base;
use Kanboard\Core\Translator;

class Plugin extends Base
{
    /**
     *
     */
    public function initialize()
    {
        /***** AnalyticTimes *****/
        $this->hook->on('template:layout:js',
            array(
                'template' => 'plugins/CompletedComplexity/Assets/js/components/chart-project-completed-complexity.js'
            )
        );
        $this->template->hook->attach('template:analytic:sidebar', 'CompletedComplexity:analytic/sidebar');
        $this->template->hook->attach('template:analytic:completed_complexity', 'CompletedComplexity:analytic/completed_complexity');
        $this->helper->register('completedComplexity', '\Kanboard\Plugin\CompletedComplexity\Helper\AnalyticCompletedComplexity');
    }

    /**
     *
     */
    public function onStartup()
    {
        Translator::load($this->languageModel->getCurrentLanguage(), __DIR__.'/Locale');
    }

    /**
     * @return string
     */
    public function getPluginName()
    {
        return 'CompletedComplexity';
    }

    /**
     * @return string
     */
    public function getPluginDescription()
    {
        return t('Plugin to add tracking of Completed Complexity of tasks over dates in a date range. This chart shows the completed complexity (throughput) of tasks moved into the right-most column over the time interval.');
    }

    /**
     * @return string
     */
    public function getPluginAuthor()
    {
        return 'smacz';
    }

    /**
     * @return string
     */
    public function getPluginVersion()
    {
        return '0.3.0';
    }

    /**
     * @return string
     */
    public function getPluginHomepage()
    {
        return 'https://gitlab.com/smacz/kanboard-CompletedComplexity';
    }

    /**
     * @return string
     */
    public function getCompatibleVersion()
    {
        return '>=1.2.6';
    }
}
