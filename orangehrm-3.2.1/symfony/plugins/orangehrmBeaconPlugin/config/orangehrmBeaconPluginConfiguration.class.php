<?php

class orangehrmBeaconPluginConfiguration extends sfPluginConfiguration {

    public function initialize() {

        $enabledModules = sfConfig::get('sf_enabled_modules');
        if (is_array($enabledModules)) {
            sfConfig::set('sf_enabled_modules', array_merge($enabledModules, array('communication')));
        }
    }

}
