<?php

class validateCredentialsAction extends sfAction {

    protected $authenticationService;
    protected $systemUserService;
    protected $loginService;

   

    public function getLoginService() {
        if (is_null($this->loginService)) {
            $this->loginService = new LoginService();
        }
        return $this->loginService;
    }
    
    
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
                    $this->getLoginService()->addLogin();
                    $paramString = $this->_getParameterString($request, true);
                    //$this->redirect('oauth/authorize'. $paramString);
                    $url = url_for('oauth/authorize') . $paramString;
                    $logger = Logger::getLogger('login');
                    $loggedInUserId = $this->getAuthenticationService()->getLoggedInUserId();
                    $loggedInUser = $this->getSystemUserService()->getSystemUser($loggedInUserId);
                    $logger->info($loggedInUserId.', '.$loggedInUser->getUserName(). ', '.$_SERVER['REMOTE_ADDR']);   
                    $this->redirect($url);
                } else {
                    $paramString = $this->_getParameterString($request);
                    $this->getUser()->setFlash('message', __('Invalid credentials'), true);
                    $this->redirect('oauth/login' . $paramString);
                    
                }
            } catch (AuthenticationServiceException $e) {
                $this->getUser()->setFlash('message', $e->getMessage(), true);
                $paramString = $this->_getParameterString($request);
                $this->redirect('oauth/login' . $paramString);
            }
        }

        return sfView::NONE;
    }
    
    private function _getParameterString($request, $urlencode = false){
        if($urlencode){
            echo $request->getParameter('redirect_uri') . "<br/>";
            $redirect_uri = urlencode(base64_decode($request->getParameter('redirect_uri')));
        } else {
            $redirect_uri = $request->getParameter('redirect_uri');
        }        
        $paramString = "?redirect_uri=$redirect_uri";
        $parameters = array('response_type', 'client_id', 'state');
        foreach ($parameters as $parameterName) {
            $parameterValue = $request->getParameter($parameterName);
            if(empty($parameterValue)){
                continue;
            }
            $str = $parameterName . '=' . urlencode($parameterValue);
            $paramString .= '&' . $str;
        }
        return $paramString;
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
    
    public function getSystemUserService() {
        if (is_null($this->systemUserService)) {
            $this->systemUserService = new SystemUserService();
        }
        return $this->systemUserService;
    }

    public function setSystemUserService($systemUserService) {
        $this->systemUserService = $systemUserService;
    }

}
