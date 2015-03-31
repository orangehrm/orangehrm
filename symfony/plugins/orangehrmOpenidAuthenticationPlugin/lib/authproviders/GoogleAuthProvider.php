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

    public function validateUser($provider, $authProvider = null) {

        $gClient = new Google_Client();
        $gClient->setApplicationName('Login to Google +');
        $gClient->setClientId($authProvider->getClientId());
        $gClient->setClientSecret($authProvider->getClientSecret());
        $gClient->setRedirectUri($provider->getProviderUrl());
        $gClient->setDeveloperKey($authProvider->getDeveloperKey());
        $gClient->addScope(array(self::EMAIL_SCOPE,self::PROFILE_SCOPE));

        $gService = new Google_Service_Plus($gClient);

        if (isset($_GET['code'])) {
            $gClient->authenticate($_GET['code']);
        }

        if ($gClient->getAccessToken()) {
            $user = $gService->people->get("me");
            $emails = $user->getEmails();
            foreach ($emails as $email) {
                if ($email->getType() == 'account') {
                    $primaryEmail = $email->getValue();
                }
            }
            $username = $primaryEmail;
            $dataArray['providerid'] = $provider->getProviderId();
            $dataArray['useridentity'] = $gClient->getAccessToken();
            $success = $this->getOpenIdService()->setOpenIdCredentials($username, $dataArray);
            if ($success) {
                $flag = array('type' => 'true', 'message' => 'User has authentication!');
                return $flag;
            } else {
                $flag = array('type' => 'false', 'message' => 'User Account Not found');
                return $flag;
            }
        } else {
            $authUrl = $gClient->createAuthUrl();
            header('Location: ' . $authUrl);
            exit();
        }
    }

}
