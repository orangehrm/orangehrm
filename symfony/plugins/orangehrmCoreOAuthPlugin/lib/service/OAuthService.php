<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OAuthService
 *
 * @author orangehrm
 */

require_once __DIR__ . '/../vendor/OAuth2/Autoloader.php';

class OAuthService extends BaseService {
    
    protected $oauthServer = null;
    protected $oauthRequest = null;
    protected $oauthResponse = null;
    protected $authenticationService;
    
    public function __construct() {
        self::initService();
    }
    
    public static function initService(){
        OAuth2_Autoloader::register();
    }

    public function getOAuthRequest() {
        if (is_null($this->oauthRequest)) {
            $this->oauthRequest = OAuth2_Request::createFromGlobals();
        }
        return $this->oauthRequest;
    }

    public function getOAuthResponse() {
        if (is_null($this->oauthResponse)) {
            $this->oauthResponse = new OAuth2_Response();
        }
        return $this->oauthResponse;
    }
    
    public function getAuthenticationService() {
        if (!isset($this->authenticationService)) {
            $this->authenticationService = new AuthenticationService();
        }
        return $this->authenticationService;
    }

    public function getOAuthServer() {
        if (is_null($this->oauthServer)) {
            $config = array(
                'client_table' => 'ohrm_oauth_client',
                'access_token_table' => 'ohrm_oauth_access_token',
                'refresh_token_table' => 'ohrm_oauth_refresh_token',
                'code_table' => 'ohrm_oauth_authorization_code',
                'user_table' => 'ohrm_oauth_user',
                'jwt_table' => 'ohrm_oauth_jwt'
            );
            $conn = Doctrine_Manager::connection()->getDbh();
            $storage = new OAuth2_Storage_Pdo($conn, $config);
            $server = new OAuth2_Server($storage);
           // $server->addGrantType(new OAuth2_GrantType_AuthorizationCode($storage));
            //$server->addGrantType(new OAuth2_GrantType_ClientCredentials($storage));
            $server->addGrantType(new OAuth2_GrantType_UserCredentials(new OAuth2_Storage_OhrmUserCredentials()));
            $server->addGrantType(new OAuth2_GrantType_RefreshToken($storage));// or any grant type you like!
            
            $this->oauthServer = $server;
        }
        return $this->oauthServer;
    }
    
    public static function getState() {
        
    }
    
    public function validateRequest(sfWebRequest $request){
        $server = $this->getOAuthServer();
        $oauthRequest = $this->getOAuthRequest();
        $oauthResponse = $this->getOAuthResponse();
        
        if (!$server->verifyResourceRequest($oauthRequest, $oauthResponse)) {
            $server->getResponse()->send();
            throw new sfStopException();
        }
        
        $tokenData = $server->getAccessTokenData($oauthRequest, $oauthResponse);
        $userId = $tokenData['user_id'];
        
        $userService = new SystemUserService();
        $user = $userService->getSystemUser($userId);
        
        $authService = new AuthService();
        $authService->setLoggedInUser($user);
        $this->getAuthenticationService()->setCredentialsForUser($user, array());
        
    }
}

?>
