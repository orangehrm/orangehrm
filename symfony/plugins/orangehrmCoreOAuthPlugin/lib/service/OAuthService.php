<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

/**
 * Description of OAuthService
 *
 * @author orangehrm
 */
class OAuthService extends BaseService
{

    protected $oauthServer = null;
    protected $oauthRequest = null;
    protected $oauthResponse = null;
    protected $authenticationService;
    protected $oAuthClientDao;

    public function getOAuthRequest()
    {
        if (is_null($this->oauthRequest)) {
            $this->oauthRequest = OAuth2\Request::createFromGlobals();
        }
        return $this->oauthRequest;
    }

    public function getOAuthResponse()
    {
        if (is_null($this->oauthResponse)) {
            $this->oauthResponse = new OAuth2\Response();
        }
        return $this->oauthResponse;
    }

    public function getAuthenticationService()
    {
        if (!isset($this->authenticationService)) {
            $this->authenticationService = new AuthenticationService();
        }
        return $this->authenticationService;
    }

    /**
     * @return mixed
     */
    public function getOAuthClientDao()
    {
        if ($this->oAuthClientDao == null) {
            $this->oAuthClientDao = new OAuthClientDao();
        }
        return $this->oAuthClientDao;
    }

    /**
     * @param mixed $oAuthClientDao
     */
    public function setOAuthClientDao($oAuthClientDao)
    {
        $this->oAuthClientDao = $oAuthClientDao;
    }

    public function getOAuthServer()
    {
        if (is_null($this->oauthServer)) {
            $config = array(
                'client_table' => 'ohrm_oauth_client',
                'access_token_table' => 'ohrm_oauth_access_token',
                'refresh_token_table' => 'ohrm_oauth_refresh_token',
                'code_table' => 'ohrm_oauth_authorization_code',
                'user_table' => 'ohrm_oauth_user',
                'jwt_table' => 'ohrm_oauth_jwt',
                'scope_table'  => 'ohrm_oauth_scope',
            );
            $conn = Doctrine_Manager::connection()->getDbh();
            $storage = new OAuth2\Storage\Pdo($conn, $config);
            $server = new OAuth2\Server($storage);
            // $server->addGrantType(new OAuth2_GrantType_AuthorizationCode($storage));
            $server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));
            $server->addGrantType(new OAuth2\GrantType\UserCredentials(new OAuth2_Storage_OhrmUserCredentials()));
            $server->addGrantType(new OhrmRefreshToken($storage, ['always_issue_new_refresh_token' => true]));
            $server->setScopeUtil(new OAuth2\Scope($storage));

            $this->oauthServer = $server;
        }
        return $this->oauthServer;
    }

    public static function getState()
    {

    }

    public function validateRequest(sfWebRequest $request)
    {
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

    /**
     * List Clients
     *
     * @return mixed
     */
    public function listOAuthClients()
    {
        return $this->getOAuthClientDao()->listOAuthClients();
    }

    /**
     * Get client by id
     *
     * @param $id
     * @return mixed
     */
    public function getOAuthClient($id)
    {
        return $this->getOAuthClientDao()->getOAuthClient($id);
    }

    /**
     * Delete clients
     *
     * @param $ids ( array )
     * @return mixed
     */
    public function deleteOAuthClient($ids){
        return $this->getOAuthClientDao()->deleteOAuthClient($ids);
    }

}
