<?php

/**
 * Storage for validate OrangeHRM user credentials
 */
class OAuth2_Storage_OhrmUserCredentials implements OAuth2_Storage_UserCredentialsInterface
{
    protected $authenticationService;
    protected $systemUserService;
    
    public function getSystemUserService() {
        if (!isset($this->systemUserService)) {
            $this->systemUserService = new SystemUserService();
        }
        return $this->systemUserService;
    }
    
    public function getAuthenticationService() {
        if (!isset($this->authenticationService)) {
            $this->authenticationService = new AuthenticationService();
        }
        return $this->authenticationService;
    }
    
    /* UserCredentialsInterface */
    public function checkUserCredentials($username, $password)
    {
        $isAuthenticated = false;
        if ($username && $password) {
            $isAuthenticated = $this->getAuthenticationService()->setCredentials($username, $password, array());
        }
        return $isAuthenticated;
    }

    public function getUserDetails($username)
    {
        $userDetails = null;
        $user = $this->getSystemUserService()->isExistingSystemUser($username, null);
        if ($user) {
            $userDetails = array (
                'user_id'  => $user->getId(),
                'user_name' => $user->getUserName(),
            );
        }
        return $userDetails;
    }
}