<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BeaconRegistrationForm
 *
 * @author chathura
 */
class BeaconRegistrationForm extends sfForm{
    
    private $beaconConfig;
    
    /**
     * 
     * @return BeaconConfigurationService
     */
    public function getBeaconConfigurationService() {
        if(is_null($this->beaconConfig)) {
            $this->beaconConfig = new BeaconConfigurationService();
        }
        return $this->beaconConfig;        
    }
    
    public function configure() {
        $this->setWidgets(array(
            'registration' => new sfWidgetFormInputCheckbox()
        ));
        $this->setValidators(array(
            'registration'=> new sfValidatorString(array('required'=>false))
        ));
        
        $this->setDefault('registration', true);
        
        $this->getWidgetSchema()->setLabels(array(
            'registration' => __('I would like to send usage data to OrangeHRM')
        ));
        $this->widgetSchema->setNameFormat('register[%s]');
    }
    
    public function save() {
        $result = array();
        $this->getBeaconConfigurationService()->setBeaconActivationAcceptanceStatus($this->getValue('registration'));
        if($this->getBeaconConfigurationService()->getBeaconActivationAcceptanceStatus()==$this->getValue('registration')) {
            $result['messageType'] = 'success';
            $result['message'] = TopLevelMessages::SAVE_SUCCESS;
        } else {
            $result['messageType'] = 'error';
            $result['message'] = TopLevelMessages::SAVE_FAILURE;
        }
        
        return $result;
    }
}
