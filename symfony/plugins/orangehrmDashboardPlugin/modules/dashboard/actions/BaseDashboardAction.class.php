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
 * Description of BaseDashboardAction
 */
abstract class BaseDashboardAction extends sfAction {

    private $dashboardService;
    private $graphService;
    private $leaveRequestService;
    private $systemUserService;
    private $userRoleManager;

    public function getUserRoleManager() {
        if(is_null($this->userRoleManager)){
            $this->userRoleManager = new BasicUserRoleManager();
        }
        return $this->userRoleManager;
    }

    public function getSystemUserService() {
        if (is_null($this->systemUserService)) {
            $this->systemUserService = new SystemUserService();
        }
        return $this->systemUserService;
    }

    public function getLeaveRequestService() {
        if (is_null($this->leaveRequestService)) {
            $this->leaveRequestService = new LeaveRequestService();
        }
        return $this->leaveRequestService;
    }

    public function getGraphService() {
        if (is_null($this->graphService)) {
            $this->graphService = new GraphService();
        }
        return $this->graphService;
    }

    public function getLoggedInUserDetails() {
        $userDatails = array();
        $userDatails['userType'] = 'ESS';
        $userDetails['loggedUserEmpId'] = $this->getUser()->getAttribute('auth.empNumber', 0);
        if ($this->getUser()->getAttribute('auth.isSupervisor')) {
            $userDetails['userType'] = 'Supervisor';
        }
        if ($this->getUser()->getAttribute('auth.isAdmin')) {
            $userDetails['userType'] = 'Admin';
        }
        return $userDetails;
    }

    public function getDashboardService() {
        if (is_null($this->dashboardService)) {
            $this->dashboardService = new DashboardService();
        }
        return $this->dashboardService;
    }

}
