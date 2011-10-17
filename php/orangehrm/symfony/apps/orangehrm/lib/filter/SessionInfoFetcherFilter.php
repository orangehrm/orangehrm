<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TemporaryLoginFilter
 *
 * @author orangehrm
 */
class SessionInfoFetcherFilter extends sfFilter {

    public function execute($filterChain) {

        if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == "Yes") {
            $userRoleArray['isAdmin'] = true;
        } else {
            $userRoleArray['isAdmin'] = false;
        }

        if (isset($_SESSION['isSupervisor'])) {
            $userRoleArray['isSupervisor'] = $_SESSION['isSupervisor'];
        }

        if (isset($_SESSION['isHiringManager'])) {
            $userRoleArray['isHiringManager'] = $_SESSION['isHiringManager'];
        }

        if (isset($_SESSION['isInterviewer'])) {
            $userRoleArray['isInterviewer'] = $_SESSION['isInterviewer'];
        }

        if (isset($_SESSION['empNumber']) && $_SESSION['empNumber'] == null) {
            $userRoleArray['isEssUser'] = false;
        } else {
            $userRoleArray['isEssUser'] = true;
        }

        if (isset($_SESSION['isProjectAdmin']) && $_SESSION['isProjectAdmin']) {
            $userRoleArray['isProjectAdmin'] = true;
        } else {
            $userRoleArray['isProjectAdmin'] = false;
        }

        $userObj = new User();
        if (isset($_SESSION['empNumber'])) {
            $userObj->setEmployeeNumber($_SESSION['empNumber']);
        }

        if (isset($_SESSION['user'])) {
            $userObj->setUserId($_SESSION['user']);
        }

        if (isset($_SESSION['userTimeZoneOffset'])) {
            $userObj->setUserTimeZoneOffset($_SESSION['userTimeZoneOffset']);
        } else {
            $userObj->setUserTimeZoneOffset(0);
        }

        $simpleUserRoleFactory = new SimpleUserRoleFactory();
        $decoratedUser = $simpleUserRoleFactory->decorateUserRole($userObj, $userRoleArray);

        $this->getContext()->getUser()->setAttribute("user", $decoratedUser);

        $filterChain->execute();
    }

}
