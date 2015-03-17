<?php

/*
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM) 
 * System that captures all the essential functionalities required for any enterprise. 
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com 
 * 
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any 
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc 
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the 
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain 
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property 
 * rights to any design, new software, new protocol, new interface, enhancement, update, 
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for 
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are 
 * reserved to OrangeHRM Inc. 
 * 
 * Please refer http://www.orangehrm.com/Files/OrangeHRM_Commercial_License.pdf for the license which includes terms and conditions on using this software. 
 *  
 */

/**
 * Description of OpenIdSelectForm
 *
 * @author orangehrm
 */
class OpenIdSelectForm extends BaseOpenIdForm{
    protected $widgets = array();
    protected $validators = array();
    public function configure() {
          $this->setWidgets($this->getFromWidgets());
          $this->setValidators($this->getFromValidators());
          
    }
    
    protected function getFromWidgets() {
         $this->widgets = array(
             'openIdProvider'=> new sfWidgetFormSelect(array('choices' => $this->getOpenIdProviderList()))
             
         );
        return $this->widgets;
    }
    protected function getFromValidators() {
          $this->validators = array(
             'openIdProvider'=> new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->getOpenIdProviderList())))
         );
        return $this->validators;
    }
     
public function getOpenIdProviderList() {
       $providerList = array('' => '-- ' . __('Select') . ' --');
       $listPro=$this->getOpenIdProviderService()->listOpenIdProviders();
       foreach ($listPro as $key => $value) {
          $providerList[$value->getProviderId()] = $value->getProviderName();
       }
       return($providerList);
    }

}

?>
