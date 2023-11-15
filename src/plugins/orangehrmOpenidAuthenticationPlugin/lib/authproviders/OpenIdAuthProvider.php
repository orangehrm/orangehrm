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
