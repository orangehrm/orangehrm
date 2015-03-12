<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of authorizeAction
 *
 * @author orangehrm
 */
class authorizeAction extends baseOAuthAction {

    protected $authenticationService;
    
    public function getAuthenticationService(){
        if (!($this->authenticationService instanceof AuthenticationService)) {
            $this->authenticationService = new AuthenticationService();
        }
        return $this->authenticationService;
    }

    public function execute($request) {
        $server = $this->getOAuthServer();
        $oauthRequest = $this->getOAuthRequest();
        $oauthResponse = $this->getOAuthResponse();
        if ($request->isMethod(sfWebRequest::POST)) {    
            $userId = $this->getAuthenticationService()->getLoggedInUserId();
            $authorized = (bool) $request->getParameter('authorize');
            $server->handleAuthorizeRequest($oauthRequest, $oauthResponse, $authorized, $userId);
            $oauthResponse->send();
            throw new sfStopException();
        }
        if (!$server->validateAuthorizeRequest($oauthRequest, $oauthResponse)) {
            $oauthResponse->send();
            throw new sfStopException();
        }
        $currentUserId = $this->getAuthenticationService()->getLoggedInUserId();

        if (empty($currentUserId)) {
            $paramString = $this->_getParameterString($request);
            // redirecting to login page
            $this->redirect('oauth/login' . $paramString);
        }
        $this->client_id = $request->getParameter('client_id');
        // If you get here form in the success file will be rendered.
        $this->action = url_for('oauth/authorize') . $this->_getParameterString($request, false);
    }

    private function _getParameterString($request, $encode = true) {
        if($encode){
            $redirect_uri_orig = $request->getParameter('redirect_uri');
            $redirect_uri = base64_encode(urldecode($redirect_uri_orig));
        } else {
            $redirect_uri = $request->getParameter('redirect_uri');
        }        
        $paramString = "?redirect_uri=$redirect_uri";
        $parameters = array('response_type', 'client_id', 'state');
        foreach ($parameters as $parameterName) {
            $parameterValue = $request->getParameter($parameterName);
            if (empty($parameterValue)) {
                continue;
            }
            $str = $parameterName . '=' . $parameterValue;
            $paramString .= '&' . $str;
        }
        return $paramString;
    }
    
    public function isSecure() {
        return false;
    }

}

?>
