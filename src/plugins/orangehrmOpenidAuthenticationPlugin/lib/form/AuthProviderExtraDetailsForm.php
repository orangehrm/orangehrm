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
 * Description of AuthProviderExtraDetailsForm
 */
class AuthProviderExtraDetailsForm extends AddOpenIdProviderForm {

    protected $authProviderExtraDetailsService;

    public function getAuthProviderExtraDetailsService() {
        if(is_null($this->authProviderExtraDetailsService)){
            $this->authProviderExtraDetailsService = new AuthProviderExtraDetailsService();
        }
        return $this->authProviderExtraDetailsService;
    }

    protected function getFromWidgets() {
        $this->widgets = parent::getFromWidgets();
        $this->widgets['type'] = new sfWidgetFormSelect(array('choices' => $this->getAuthenticationTypeList()));
        $this->widgets['clientId'] = new sfWidgetFormInput();
        $this->widgets['clientSecret'] = new sfWidgetFormInput();
        $this->widgets['developerKey'] = new sfWidgetFormInput();
        return $this->widgets;
    }

    protected function getFromValidators() {
        $this->validators = parent::getFromValidators();
        $this->validators['type'] = new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->getAuthenticationTypeList())));
        $this->validators['clientId'] = new sfValidatorString(array('required' => false));
        $this->validators['clientSecret'] = new sfValidatorString(array('required' => false));
        $this->validators['developerKey'] = new sfValidatorString(array('required' => false));
        return $this->validators;
    }

    public function getAuthenticationTypeList() {
        $authenticationType = array(
            AuthProviderExtraDetails::OPEN_ID => 'OpenId',
            AuthProviderExtraDetails::GOOGLE_PLUS => 'Google'
        );
        return $authenticationType;
    }

    public function save() {
        $postValues = $this->getValues();
        $providerId = $postValues['id'];
        $flag = 'save';

        $provider = null;
        if (isset($providerId) & ($providerId != '')) {
            $provider = $this->getOpenIdProviderService()->getOpenIdProvider($providerId);
            $flag = 'update';
        } else {
            $provider = new OpenidProvider();
            $provider->setStatus(1);
            $flag = 'save';
        }

        $provider->setProviderName($postValues['name']);
        $provider->setProviderUrl($postValues['url']);

        $savedProvider = $this->getOpenIdProviderService()->saveOpenIdProvider($provider);
        if ($savedProvider instanceof OpenidProvider) {
            $authProviderExtraDetails = $this->getAuthProviderExtraDetailsService()->getAuthProviderDetailsByProviderId($savedProvider->getProviderId());
            if (!($authProviderExtraDetails instanceof AuthProviderExtraDetails)) {
                $authProviderExtraDetails = new AuthProviderExtraDetails();
            }
            $authProviderExtraDetails->setProviderId($savedProvider->getProviderId());
            $authProviderExtraDetails->setProviderType($postValues['type']);
            $authProviderExtraDetails->setClientId($postValues['clientId']);
            $authProviderExtraDetails->setClientSecret($postValues['clientSecret']);
            $authProviderExtraDetails->setDeveloperKey($postValues['developerKey']);

            $this->getAuthProviderExtraDetailsService()->saveAuthProviderExtraDetails($authProviderExtraDetails);
        }
        return $flag;
    }

}
