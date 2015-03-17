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
 * Description of AddOpenIdProviderForm
 *
 * @author orangehrm
 */
class AddOpenIdProviderForm extends BaseOpenIdForm{
    protected $widgets = array();
    protected $validators = array();
    public function configure() {
          $this->setWidgets($this->getFromWidgets());
          $this->setValidators($this->getFromValidators());
          $this->widgetSchema->setNameFormat('openIdProvider[%s]');
    }
    
    protected function getFromWidgets() {
         $this->widgets = array(
             'name'=> new sfWidgetFormInput(),
             'url'=> new sfWidgetFormInput(),
             'status'=>  new sfWidgetFormInputHidden(),
             'id'=> new sfWidgetFormInputHidden()
         );
        return $this->widgets;
    }
    protected function getFromValidators() {
          $this->validators = array(
             'name'=> new sfValidatorString(array('required' => true, 'max_length' => 40, 'trim' => true)),
             'url'=> new sfValidatorString(array('required' => true)),
             'status'=>  new sfValidatorString(array('required' => false)),
             'id'=> new sfValidatorString(array('required' => false))
         );
        return $this->validators;
    }
    public function save() {

        $posts = $this->getValues();
        
        $providerId =$posts['id'];
        $flag='save';
        
        $provider = null;
        if(isset($providerId)& ($providerId!='')){
            $provider=$this->getOpenIdProviderService()->getOpenIdProvider($providerId);
            $flag='update';
        }else{
            $provider=new OpenidProvider();
            $provider->setStatus(1);
            $flag='save';
        }
        
        $provider->setProviderName($posts['name']);
        $provider->setProviderUrl($posts['url']);
        
        $this->getOpenIdProviderService()->saveOpenIdProvider($provider);
        return $flag;
    }
public function getOpenIdProviderListAsJson() {

        $list = array();
        $providerList = $this->getOpenIdProviderService()->listOpenIdProviders();
        foreach ($providerList as $provider) {
            $list[] = array('id' => $provider->getProviderId(), 'name' => $provider->getProviderName());
        }
        return json_encode($list);
    }
}

?>
