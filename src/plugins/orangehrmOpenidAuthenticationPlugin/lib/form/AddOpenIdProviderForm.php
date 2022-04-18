<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
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
