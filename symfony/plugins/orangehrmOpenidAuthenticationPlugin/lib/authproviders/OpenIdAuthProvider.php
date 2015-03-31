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
 * Description of OpenIdAuthProvider
 */
class OpenIdAuthProvider extends AbstractAuthProvider {

    public function __construct() {
        $domain = $this->getConfigDao()->getValue('domain.name');
        $this->openId = new LightOpenID($domain);
    }

    public function validateUser($provider, $authProvider = null) {

        if (!$this->openId->mode) {
            if ($provider instanceof OpenidProvider) {
                $_SESSION['providerid'] = $provider->getProviderId();
                $this->openId->identity = $provider->getProviderUrl();
                $this->openId->required = array('contact/email');
                $openIdUrl = $this->openId->authUrl();
                header('Location: ' . $openIdUrl);
                exit();
            }
        } elseif ($this->openId->mode == 'cancel') {
            $flag = array('type' => 'error', 'message' => 'User has canceled authentication!');
            return $flag;
        } else {
            if ($this->openId->validate()) {
                $dataArray = $this->openId->getAttributes();
                $username = $dataArray['contact/email'];
                $dataArray['providerid'] = $_SESSION['providerid'];
                $_SESSION['providerid'] = '';
                $dataArray['useridentity'] = $this->openId->identity;
                try {
                    $success = $this->getOpenIdService()->setOpenIdCredentials($username, $dataArray);
                    if ($success) {
                        $flag = array('type' => "true", 'message' => 'User has authentication!');
                        return $flag;
                    } else {
                        $flag = array('type' => "false", 'message' => 'User Account Not found');
                        return $flag;
                    }
                } catch (AuthenticationServiceException $ex) {
                    $this->getOpenIdService()->clearCredentials();
                    $flag = array('type' => 'error', 'message' => 'User Account Not found');
                    return $flag;
                }
            } else {
                $this->getOpenIdService()->clearCredentials();
                $flag = array('type' => 'error', 'message' => 'User Account Not found');
                return $flag;
            }
        }
    }
}
