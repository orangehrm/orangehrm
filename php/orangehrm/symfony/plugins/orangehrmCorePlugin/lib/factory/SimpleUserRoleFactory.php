<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SimpleUserRoleFactory
 *
 * @author orangehrm
 */
class SimpleUserRoleFactory {

    public function decorateUserRole($userObj, $userRoleArray) {

        if ($userRoleArray['isAdmin'] && (!$userRoleArray['isEssUser'])) {

            return new AdminUserRoleDecorator($userObj);
        } elseif ($userRoleArray['isEssUser'] && $userRoleArray['isAdmin']) {

            $essUserRole = new EssUserRoleDecorator($userObj);
            $adminUserRole = new AdminUserRoleDecorator($essUserRole);
            return $adminUserRole;
        } elseif ($userRoleArray['isEssUser'] && $userRoleArray['isSupervisor']) {

            $essUserRole = new EssUserRoleDecorator($userObj);
            $superivorUserRole = new SupervisorUserRoleDecorator($essUserRole);
            return $superivorUserRole;
        } elseif ($userRoleArray['isEssUser']) {
           
            $essUserRole = new EssUserRoleDecorator($userObj);
            
            return $essUserRole;
        } else {
            return null;
        }
    }

}

