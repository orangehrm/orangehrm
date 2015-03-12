<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AuthService
 *
 * @author orangehrm
 */
class AuthService extends AuthenticationService {
    public function setLoggedInUser(SystemUser $user){
        $this->setBasicUserAttributes($user);
        $this->setBasicUserAttributesToSession($user);
        $this->setRoleBasedUserAttributes($user);
        $this->setRoleBasedUserAttributesToSession($user);
    }
}
