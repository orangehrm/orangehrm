<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of issueTokenAction
 *
 * @author orangehrm
 */
class issueTokenAction extends baseOAuthAction {
    public function execute($request) {
        $server = $this->getOAuthServer();
        $oauthRequest = $this->getOAuthRequest();
        $oauthResponse = $this->getOAuthResponse();
        $server->handleTokenRequest($oauthRequest, $oauthResponse);
        $oauthResponse->send();
        return sfView::NONE;
    }
}

?>
