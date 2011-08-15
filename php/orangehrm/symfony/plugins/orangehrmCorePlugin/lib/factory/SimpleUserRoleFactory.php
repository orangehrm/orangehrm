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

        if ($userRoleArray['isEssUser']) {
            $userObj = new EssUserRoleDecorator($userObj);
        }

        if ($userRoleArray['isProjectAdmin']) {
            $userObj = new ProjectAdminUserRoleDecorator($userObj);
        }

        if ($userRoleArray['isSupervisor']) {
            $userObj = new SupervisorUserRoleDecorator($userObj);
        }

        if ($userRoleArray['isAdmin']) {
            $userObj = new AdminUserRoleDecorator($userObj);
        }
        return $userObj;
    }

}

