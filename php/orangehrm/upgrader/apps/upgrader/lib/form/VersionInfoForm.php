<?php

class VersionInfo extends sfForm {

    public function configure() {
        
        $versionArray = array(null => '--'.'Select'.'--', 30 => '2.6.12.1', 29 => '2.6.12', 28 => '2.6.11.3');
        $this->setWidgets(array(
            'submitBy' => new sfWidgetFormInputHidden(array(), array('value' => 'databaseInfo')),
            'version' => new sfWidgetFormChoice(array('choices' => $versionArray)),
        ));
        
        $this->widgetSchema->setLabels(array(
            'version' => 'Current OrangeHRM Version'
        ));
        
        $this->widgetSchema->setNameFormat('versionInfo[%s]');
        
        $this->setValidators(array(
            'submitBy' => new sfValidatorString(array('required' => true)),
            'version' => new sfValidatorChoice(array('choices' => array_keys($versionArray), 'required' => true), array('required' => 'Select a Version'))
        ));
    }

}
