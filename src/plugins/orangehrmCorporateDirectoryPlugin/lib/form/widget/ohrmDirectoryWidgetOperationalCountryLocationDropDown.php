<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class ohrmDirectoryWidgetOperationalCountryLocationDropDown extends ohrmReportWidgetOperationalCountryLocationDropDown{
    
    protected function configure($options = array(), $attributes = array()) {

        parent::configure($options, $attributes);

        // Parent requires the 'choices' option.
        $this->addOption('choices', array());
        $this->addOption('set_all_option_value', true);
       
        $this->addOption('all_option_value', '-1');
        $this->addOption('show_all_locations', false);
    }
    
}
