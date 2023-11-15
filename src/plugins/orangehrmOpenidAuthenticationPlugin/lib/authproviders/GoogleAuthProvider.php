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
 * Description of GoogleAuthProvider
 */
class GoogleAuthProvider extends AbstractAuthProvider {
    const PROFILE_SCOPE = 'profile';
    const EMAIL_SCOPE = 'email';
    protected $loginService = null;
    protected $option = array();
    protected $authenticationMassage = '';
    /**
     * @param OpenidProvider $provider
     * @param AuthProviderExtraDetails $authProvider
     * @return array
     */
    public function validateUser($provider, $authProvider = null)
    {
        $gClient = new Google_Client();
        $gClient->setApplicationName('Login to Google');
        $gClient->setClientId($authProvider->getClientId());
        $gClient->setClientSecret($authProvider->getClientSecret());
        $gClient->setRedirectUri($provider->getProviderUrl());
        $gClient->setDeveloperKey($authProvider->getDeveloperKey());
        $gClient->addScope(array(self::EMAIL_SCOPE, self::PROFILE_SCOPE));
        $gClient->setPrompt('consent');
        $requestParameters = $this->getOption();

        if (isset($requestParameters['code'])) {
            $gClient->fetchAccessTokenWithAuthCode($requestParameters['code']);
        }
        if ($gClient->getAccessToken()) {
            $tokenData = $gClient->verifyIdToken();
            $username = $tokenData[self::EMAIL_SCOPE];
            $dataArray['providerid'] = $provider->getProviderId();
            $dataArray['useridentity'] = json_encode($gClient->getAccessToken());
            try {
                $success = $this->getOpenIdService()->setOpenIdCredentials($username, $dataArray);
                if ($success) {
                    $this->getLoginService()->addLogin();
                    $this->authenticationMassage=__('User has authentication!');
                    $flag = array(
                        'type' => 'true',
                        'message' =>  $this->authenticationMassage
                    );
                    return $flag;
                } else {
                    $this->authenticationMassage=__('Invalid Credentials : You Have No OpenID Account in OrangeHRM Try Login with OrangeHRM Credentials');
                    $flag = array(
                        'type' => 'false',
                        'message' =>  $this->authenticationMassage
                    );
                    return $flag;
                }
            } catch (AuthenticationServiceException $e) {

                $this->authenticationMassage = $e->getMessage();
                $flag = array(
                    'type' => 'false',
                    'message' => $this->authenticationMassage
                );
                return $flag;
            }
        } else {
            $authUrl = $gClient->createAuthUrl();
            header('Location: ' . $authUrl);
            exit();
        }
    }

    /**
     * @return LoginService
     */
    public function getLoginService()
    {
        if (is_null($this->loginService)) {
            $this->loginService = new LoginService();
        }
        return $this->loginService;
    }

    /**
     * @return array
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * @param GET $option
     */
    public function setOption($option)
    {
        $this->option = $option;
    }

}
