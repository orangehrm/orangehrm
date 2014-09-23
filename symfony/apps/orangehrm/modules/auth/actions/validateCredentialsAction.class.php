<?php

class validateCredentialsAction extends sfAction {

    protected $authenticationService;
    protected $homePageService;
    protected $beaconCommunicationService;
    protected $loginService;
    
    /**
     * 
     * @return BeaconCommunicationsService
     */
    public function getBeaconCommunicationService() {
        if(is_null($this->beaconCommunicationService)) {
            $this->beaconCommunicationService = new BeaconCommunicationsService();            
        }
        return $this->beaconCommunicationService;
    }
    
    public function getLoginService() {
        if(is_null($this->loginService)) {
            $this->loginService = new LoginService();
        }
        return $this->loginService;
    }

    public function execute($request) {

        if ($request->isMethod(sfWebRequest::POST)) {
            $loginForm = new LoginForm();
            $csrfToken = $request->getParameter('_csrf_token');
            if ($csrfToken != $loginForm->getCSRFToken()) {
                $this->getUser()->setFlash('message', __('Csrf token validation failed'), true);
                $this->forward('auth', 'retryLogin');
            }
            $username = $request->getParameter('txtUsername');
            $password = $request->getParameter('txtPassword');
            $additionalData = array(
                'timeZoneOffset' => $request->getParameter('hdnUserTimeZoneOffset', 0),
            );

            try {

                $success = $this->getAuthenticationService()->setCredentials($username, $password, $additionalData);

                if ($success) {
                    
                    $this->getBeaconCommunicationService()->setBeaconActivation();
                    $this->getLoginService()->addLogin();
                    $this->redirect($this->getHomePageService()->getPathAfterLoggingIn($this->getContext()));
                    
                } else {
                    $this->getUser()->setFlash('message', __('Invalid credentials'), true);
                    $this->forward('auth', 'retryLogin');
                }
            } catch (AuthenticationServiceException $e) {

                $this->getUser()->setFlash('message', $e->getMessage(), false);
                $this->forward('auth', 'login');
            }
        }

        return sfView::NONE;
    }

    /**
     *
     * @return AuthenticationService 
     */
    public function getAuthenticationService() {
        if (!isset($this->authenticationService)) {
            $this->authenticationService = new AuthenticationService();
        }
        return $this->authenticationService;
    }

    public function getHomePageService() {

        if (!$this->homePageService instanceof HomePageService) {
            $this->homePageService = new HomePageService($this->getUser());
        }

        return $this->homePageService;
    }

    public function setHomePageService($homePageService) {
        $this->homePageService = $homePageService;
    }
    
    public function getForm() {
        return null;
    }

}
