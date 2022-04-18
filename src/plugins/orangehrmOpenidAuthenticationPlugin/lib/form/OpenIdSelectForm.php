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
