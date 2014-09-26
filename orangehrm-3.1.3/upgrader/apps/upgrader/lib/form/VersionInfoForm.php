<?php

class VersionInfoForm extends sfForm {

    public function configure() {
        
        $this->setWidgets(array(
            'submitBy' => new sfWidgetFormInputHidden(array(), array('value' => 'selectVersion')),
            'version' => new sfWidgetFormChoice(array('choices' => $this->getVersionWidgetOptions()))
        ));
        
        $this->widgetSchema->setLabels(array(
            'version' => 'Current OrangeHRM Version'
        ));
        
        $this->widgetSchema->setNameFormat('versionInfo[%s]');
        
        $this->setValidators(array(
            'submitBy' => new sfValidatorString(array('required' => true)),
            'version' => new sfValidatorChoice(array('choices' => array_keys($this->getVersionWidgetOptions()), 'required' => true), array('required' => 'Select a Version'))
        ));
    }
    
    protected function getVersionWidgetOptions() {
        
        $widgetVersions = array('-1' => '-- ' . __('Select') . ' --');
        $upgradeUtility = new UpgradeUtility();
        $versionArray   = array_keys($upgradeUtility->getVersionAndIncrementerNumbers());
        
        foreach ($versionArray as $version) {
        
            $widgetVersions[$version] = $version;
            
        }
        
        return $widgetVersions;
        
    }

}
