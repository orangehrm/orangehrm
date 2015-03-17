<?php

/**
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
            AuthProviderExtraDetails::GOOGLE_PLUS => 'Google+'
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
