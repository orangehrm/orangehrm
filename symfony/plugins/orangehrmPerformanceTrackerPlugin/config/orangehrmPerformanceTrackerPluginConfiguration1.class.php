<?php

class orangehrmPerformanceTrackerPluginConfiguration1 extends sfPluginConfiguration {

    public function initialize() {
        $enabledModules = sfConfig::get('sf_enabled_modules');
       if (is_array($enabledModules)) {
            sfConfig::set('sf_enabled_modules', array_merge(sfConfig::get('sf_enabled_modules'), array('performanceTracker')));
        }
    }

}

