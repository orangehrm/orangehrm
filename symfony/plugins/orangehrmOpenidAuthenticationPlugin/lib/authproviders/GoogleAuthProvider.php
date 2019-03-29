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
        $gClient->setApplicationName('Login to Google +');
        $gClient->setClientId($authProvider->getClientId());
        $gClient->setClientSecret($authProvider->getClientSecret());
        $gClient->setRedirectUri($provider->getProviderUrl());
        $gClient->setDeveloperKey($authProvider->getDeveloperKey());
        $gClient->addScope(array(self::EMAIL_SCOPE, self::PROFILE_SCOPE));
        $requestParameters = $this->getOption();

        if (isset($requestParameters['code'])) {
            $gClient->fetchAccessTokenWithAuthCode($requestParameters['code']);
        }
        if ($gClient->getAccessToken()) {
            $tokenData = $gClient->verifyIdToken();
            $username = $tokenData[self::EMAIL_SCOPE];
            $dataArray['providerid'] = $provider->getProviderId();
            $dataArray['useridentity'] = json_encode($gClient->getAccessToken());
            $success = $this->getOpenIdService()->setOpenIdCredentials($username, $dataArray);
            if ($success) {
                $this->getLoginService()->addLogin();
                $this->authenticationMassage=__('User has authentication!');
                $flag = array(
                    'type' => 'true',
                    'message' =>  $this->authMassage
                );
                return $flag;
            } else {
                $this->authenticationMassage=__('Invalid Credentials : you Have No OpenID account in Orangehrm try loging with OrangeHRM credentials');
                $flag = array(
                    'type' => 'false',
                    'message' =>  $this->authenticationMassage
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
