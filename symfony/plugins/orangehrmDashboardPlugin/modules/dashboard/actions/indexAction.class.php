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
 * Description of indexAction
 */
class indexAction extends BaseDashboardAction {

    public function execute($request) {
        $this->settings = $this->setAllowedGroups();
    }

    public function setAllowedGroups() {
        $allowedGroups = array();
        $roleIds = $this->getLoggedInUserRoleIds();
        $quicklaunch = $this->getQuickLaunch();
        if (is_array($quicklaunch)) {
            $allowedRoleIds = $quicklaunch['attributes']['permissions'];
            if (count(array_intersect($roleIds, $allowedRoleIds)) > 0) {
                $allowedGroups[] = $quicklaunch;
            }
        }
        $graphPanel = $this->getGrapghPanel();
        if (is_array($graphPanel)) {
            $allowedRoleIds = $graphPanel['attributes']['permissions'];
            if (count(array_intersect($roleIds, $allowedRoleIds)) > 0) {
                $allowedGroups[] = $graphPanel;
            }
        }
        return $allowedGroups;
    }

    public function getQuickLaunch() {
        $quicklaunch = array(
            'attributes' => array('title' => "",
                'width' => 1100,
                'height' => 120,
                'permissions' => array(1, 2, 3, 4, 5, 6, 7)),
            'name' => 'QuickLaunch',
            'panels' => array(array(
                    'name' => 'Quick Launch',
                    'attributes' => array(
                        'width' => 'auto',
                        'height' => 90.8,
                        'module_name' => 'dashboard',
                        'action_name' => 'quickLaunchPanel',
                        'top' => 4,
                        'left' => 5,
                        'ohrm_module' => 'time,leave',
                        'loadDefault' => 'true'
                    ))
        ));

        return $quicklaunch;
    }

    public function getGrapghPanel() {
        $isAdmin = $this->getUser()->getAttribute('user')->isAdmin();
        if ($isAdmin) {
            $allowedPanels = array(array(
                    'name' => 'Employee Distribution by Subunit',
                    'attributes' => array(
                        'width' => 300,
                        'height' => 281,
                        'module_name' => 'dashboard',
                        'action_name' => 'employeeDistribution',
                        'top' => 0,
                        'left' => 0,
                        'ohrm_module' => 'pim',
                        'loadDefault' => 'false'
                    )),
                array(
                    'name' => 'Legend',
                    'attributes' => array(
                        'width' => 300,
                        'height' => 281,
                        'module_name' => 'dashboard',
                        'action_name' => 'baseLegend',
                        'top' => 0,
                        'left' => 13,
                        'ohrm_module' => 'pim',
                        'loadDefault' => 'true'
                    )),
                array(
                    'name' => 'Pending Leave Requests',
                    'attributes' => array(
                        'width' => 300,
                        'height' => 281,
                        'module_name' => 'dashboard',
                        'action_name' => 'pendingLeaveRequests',
                        'top' => 0,
                        'left' => 0,
                        'ohrm_module' => 'leave',
                        'loadDefault' => 'false'
            )));
        } else {
            $allowedPanels = array(array(
                    'name' => 'Pending Leave Requests',
                    'attributes' => array(
                        'width' => 300,
                        'height' => 281,
                        'module_name' => 'dashboard',
                        'action_name' => 'pendingLeaveRequests',
                        'top' => 0,
                        'left' => 0,
                        'ohrm_module' => 'leave',
                        'loadDefault' => 'false'
            )));
        }

        $graphPanel = array(
            'attributes' => array('title' => "",
                'width' => 1100,
                'height' => 300,
                'permissions' => array(1, 3)),
            'name' => 'Graphs',
            'panels' => $allowedPanels
        );

        return $graphPanel;
    }

    public function getLoggedInUserRoleIds() {
        $userId = $this->getUser()->getAttribute('auth.userId');
        $systemUser = $this->getSystemUserService()->getSystemUser($userId);
        $allowedUserRoles = $this->getUserRoles($systemUser);
        $roleIds = array();
        foreach ($allowedUserRoles as $allowedUserRole) {
            $roleIds[] = strval($allowedUserRole->getId());
        }
        return $roleIds;
    }

    public function getUserRoles($user) {

        $roles = array($user->getUserRole());

        // Check for supervisor:
        $empNumber = $user->getEmpNumber();
        if (!empty($empNumber)) {

            if ($user->getUserRole()->getName() != 'ESS') {
                $roles[] = $this->getSystemUserService()->getUserRole('ESS');
            }

            if ($this->getUser()->getAttribute('auth.isProjectAdmin')) {
                $roles[] = $this->getSystemUserService()->getUserRole('ProjectAdmin');
            }

            if ($this->getUser()->getAttribute('auth.isHiringManager')) {
                $roles[] = $this->getSystemUserService()->getUserRole('HiringManager');
            }

            if ($this->getUser()->getAttribute('auth.isInterviewer')) {
                $roles[] = $this->getSystemUserService()->getUserRole('Interviewer');
            }

            if ($this->getUser()->getAttribute('auth.isSupervisor')) {
                $roles[] = $this->getSystemUserService()->getUserRole('Supervisor');
            }
        }

        return $roles;
    }

}
