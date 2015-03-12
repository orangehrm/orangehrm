<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of orangehrmCoreOAuthPluginConfiguration
 *
 * @author orangehrm
 */
class orangehrmCoreOAuthPluginConfiguration extends sfPluginConfiguration {
    public function initialize() {  
        $enabledModules = sfConfig::get('sf_enabled_modules');  
        if (is_array($enabledModules)) {  
            sfConfig::set('sf_enabled_modules', array_merge(sfConfig::get('sf_enabled_modules'), array('oauth')));  
        }  
    }
}

?>
