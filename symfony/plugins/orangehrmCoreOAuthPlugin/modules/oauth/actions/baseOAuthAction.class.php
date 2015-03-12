<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of indexAction
 *
 * @author orangehrm
 */

abstract class baseOAuthAction extends sfAction {

    protected $oauthServer = null;
    protected $oauthRequest = null;
    protected $oauthResponse = null;
    
    protected $oauthService = null;
    
    public function getOAuthService() {
        if (is_null($this->oauthService)) {
            $this->oauthService = new OAuthService();
        }
        return $this->oauthService;
    }

    public function getOAuthRequest() {
        if (is_null($this->oauthRequest)) {
            $this->oauthRequest = $this->getOAuthService()->getOAuthRequest();
        }
        return $this->oauthRequest;
    }

    public function getOAuthResponse() {
        if (is_null($this->oauthResponse)) {
            $this->oauthResponse = $this->getOAuthService()->getOAuthResponse();
        }
        return $this->oauthResponse;
    }

    public function getOAuthServer() {
        if (is_null($this->oauthServer)) {
            $this->oauthServer = $this->getOAuthService()->getOAuthServer();
        }
        return $this->oauthServer;
    }

}

?>
