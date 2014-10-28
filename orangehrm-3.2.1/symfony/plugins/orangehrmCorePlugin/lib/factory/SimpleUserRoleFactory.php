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

        if (isset($userRoleArray['isEssUser']) && $userRoleArray['isEssUser']) {
            $userObj = new EssUserRoleDecorator($userObj);
        }

        if (isset($userRoleArray['isProjectAdmin']) && $userRoleArray['isProjectAdmin']) {
            $userObj = new ProjectAdminUserRoleDecorator($userObj);
        }

        if (isset($userRoleArray['isSupervisor']) && $userRoleArray['isSupervisor']) {
            $userObj = new SupervisorUserRoleDecorator($userObj);
        }

        if (isset($userRoleArray['isAdmin']) && $userRoleArray['isAdmin']) {
            $userObj = new AdminUserRoleDecorator($userObj);
        }
        if (isset($userRoleArray['isInterviewer']) && $userRoleArray['isInterviewer']) {
            $userObj = new InterviewerUserRoleDecorator($userObj);
        }
        if (isset($userRoleArray['isHiringManager']) && $userRoleArray['isHiringManager']) {
            $userObj = new HiringManagerUserRoleDecorator($userObj);
        }
        
        $sfUser = sfContext::getInstance()->getUser();
        
        // Consider user defined user roles as admin
        if (!$sfUser->getAttribute('auth.userRole.predefined')) {
            $userObj = new UserDefinedUserRoleDecorator($userObj);
            $userObj->setUserRoleName($sfUser->getAttribute('auth.userRole'));
        }
        
        $userId = $userObj->getUserId();
        
        return $userObj;
    }

}

