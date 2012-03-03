<?php

abstract class UserRoleDecorator extends User {

    protected function isPluginAvailable($pluginName){
        $file = sfConfig::get('sf_plugins_dir') . "/$pluginName/";
        
        if (is_dir($file)){      
            return true;           
        } else {
            return false;
        }
    }
}
