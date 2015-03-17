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
 * Description of openIdCredentialsAction
 *
 * @author orangehrm
 */
class openIdCredentialsAction extends baseOpenIdAction {

    protected $configDao;
    protected $openIdService;
    protected $providerid;
    protected $homePageService;
    protected $systemUserService;
    private $authProviderExtraDetailsService;

    public function getAuthProviderExtraDetailsService() {
        if (is_null($this->authProviderExtraDetailsService)) {
            $this->authProviderExtraDetailsService = new AuthProviderExtraDetailsService();
        }
        return $this->authProviderExtraDetailsService;
    }

    public function execute($request) {
        try {
            $sfUser = sfContext::getInstance()->getUser();
            $form = new OpenIdSelectForm();
            if ($request->isMethod(sfWebRequest::POST)) {
                $form->bind($request->getPostParameters());
                if ($form->isValid()) {
//                $form->bind($request->getParameter($form->getName()));
                $providerId = $request->getParameter('openIdProvider');
                $sfUser->setAttribute('auth.providerId', $providerId);
                $provider = $this->getOpenIdProviderService()->getOpenIdProvider($providerId);
                $authProviderDetails = $this->getAuthProviderExtraDetailsService()->getAuthProviderDetailsByProviderId($providerId);
                if (($authProviderDetails instanceof AuthProviderExtraDetails)) {
                    $providerType = $authProviderDetails->getProviderType();
                    $sfUser->setAttribute('auth.providerType', $providerType);
                    $flag = $this->getAuthProviderObj($providerType, $provider, $authProviderDetails);
                } else {
                    $providerType = AuthProviderExtraDetails::OPEN_ID;
                    $sfUser->setAttribute('auth.providerType', $providerType);
                    $openIdAuthProvider = new OpenIdAuthProvider();
                    $flag = $openIdAuthProvider->validateUser($provider);
                }
            } else {
                $this->getUser()->setFlash('warning', __(TopLevelMessages::FORM_VALIDATION_ERROR));
                $this->redirect($request->getReferer());
            }
            } else {
                $providerType = $sfUser->getAttribute('auth.providerType');
                $providerId = $sfUser->getAttribute('auth.providerId');
                $provider = $this->getOpenIdProviderService()->getOpenIdProvider($providerId);
                $authProviderDetails = $this->getAuthProviderExtraDetailsService()->getAuthProviderDetailsByProviderId($providerId);
                $flag = $this->getAuthProviderObj($providerType, $provider, $authProviderDetails);
            }
            $this->showFlashMessage($flag);
        } catch (ErrorException $e) {
            echo $e->getMessage();
        }
        return sfView::NONE;
    }

    public function getConfigDao() {
        if ($this->configDao == null) {
            $this->configDao = new ConfigDao();
        }
        return $this->configDao;
    }

    public function setConfigDao($dao) {
        $this->configDao = $dao;
    }

    public function getOpenIdService() {
        if ($this->openIdService == null) {
            $service = new OpenIdAuthenticationService();
            $service->setOpenIdAuthenticationDao(new OpenIdAuthenticationDao());
            $this->openIdService = $service;
        }
        return $this->openIdService;
    }

    public function setOpenIdService($service) {
        $this->openIdService = $service;
    }

    public function getHomePageService() {

        if (!$this->homePageService instanceof OpenIdHomePageService) {
            $this->homePageService = new OpenIdHomePageService();
        }

        return $this->homePageService;
    }

    public function setHomePageService($homePageService) {
        $this->homePageService = $homePageService;
    }

    public function getSystemUserService() {
        if (is_null($this->systemUserService)) {
            $this->systemUserService = new SystemUserService();
        }
        return $this->systemUserService;
    }

    public function setSystemUserService($systemUserService) {
        $this->systemUserService = $systemUserService;
    }

    public function showFlashMessage($flag) {
        if ($flag['type'] == 'true') {
            $logger = Logger::getLogger('login');
            $loggedInUserId = $this->getOpenIdService()->getLoggedInUserId();
            $loggedInUser = $this->getSystemUserService()->getSystemUser($loggedInUserId);
            $logger->info($loggedInUserId . ', ' . $loggedInUser->getUserName() . ', ' . $_SERVER['REMOTE_ADDR']);
            $this->redirect('pim/viewMyDetails');
        } elseif (($flag['type'] == 'false') || ($flag['type'] == 'error')) {
            $this->getUser()->setFlash('message', __($flag['message']), true);
            $this->redirect('auth/login');
        }
    }

    public function getAuthProviderObj($providerType, $provider, $authProviderDetails) {
        switch ($providerType) {
            case AuthProviderExtraDetails::OPEN_ID:
                $openIdAuthProvider = new OpenIdAuthProvider();
                $flag = $openIdAuthProvider->validateUser($provider);
                break;
            case AuthProviderExtraDetails::GOOGLE_PLUS:
                $googleAuthProvider = new GoogleAuthProvider();
                $flag = $googleAuthProvider->validateUser($provider, $authProviderDetails);
                break;
            default :
                $flag = array('type' => 'false', 'message' => '');
                break;
        }
        return $flag;
    }

}
