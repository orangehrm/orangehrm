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
        


        if ($_SESSION['isAdmin'] == "Yes") {
            $userRoleArray['isAdmin'] = true;
        } else {
            $userRoleArray['isAdmin'] = false;
        }

        $userRoleArray['isSupervisor'] = $_SESSION['isSupervisor'];

        if ($_SESSION['empNumber'] == null) {
            $userRoleArray['isEssUser'] = false;
        } else {
            $userRoleArray['isEssUser'] = true;
        }

        if ($_SESSION['isProjectAdmin']) {
            $userRoleArray['isProjectAdmin'] = true;
        } else {
            $userRoleArray['isProjectAdmin'] = false;
        }

        $userObj = new User();
        $userObj->setEmployeeNumber($_SESSION['empNumber']);
        $userObj->setUserId($_SESSION['user']);
        $userObj->setUserTimeZoneOffset($_SESSION['userTimeZoneOffset']);

        $simpleUserRoleFactory = new SimpleUserRoleFactory();
        $decoratedUser = $simpleUserRoleFactory->decorateUserRole($userObj, $userRoleArray);

        $this->getContext()->getUser()->setAttribute("user", $decoratedUser);

        $filterChain->execute();
    }

}

?>
