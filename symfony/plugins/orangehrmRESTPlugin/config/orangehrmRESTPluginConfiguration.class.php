<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once __DIR__ . '/../../../lib/vendor/autoload.php';
/**
 * Description of orangehrmRESTPluginConfiguration
 *
 * @author orangehrm
 */
class orangehrmRESTPluginConfiguration extends sfPluginConfiguration {
    public function initialize() {
        $enabledModules = sfConfig::get('sf_enabled_modules');  
        if (is_array($enabledModules)) {  
            sfConfig::set('sf_enabled_modules',
                array_merge(sfConfig::get('sf_enabled_modules'), array('baseapi','apiv1pim','apiv1leave','apiv1admin','apiv1time','apiv1attendance','apiv1performance','apiv1integration','apiv1user','apiv1public'))
            );
        }  
    }
}

