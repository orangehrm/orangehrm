<?php

class validateCredentialsAction extends sfAction {

    protected $authenticationService;
    protected $homePageService;

    public function execute($request) {
        
        if ($request->isMethod(sfWebRequest::POST)) {
            
            $username = $request->getParameter('txtUsername');
            $password = $request->getParameter('txtPassword');
            $additionalData = array(
                'timeZoneOffset' => $request->getParameter('hdnUserTimeZoneOffset', 0),
            );

            try {

                $success = $this->getAuthenticationService()->setCredentials($username, $password, $additionalData);
                
                if ($success) {                    
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
            $this->authenticationService->setAuthenticationDao(new AuthenticationDao());
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
    

}
