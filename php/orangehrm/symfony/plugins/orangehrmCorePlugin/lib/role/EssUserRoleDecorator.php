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
class EssUserRoleDecorator extends UserRoleDecorator {
    const ESS_USER = "ESS USER";
    const VIEW_MY_TIMESHEET = "./symfony/web/index.php/time/viewMyTimesheet";
    const PUNCH_ATTENDANCE_RECORD = "./symfony/web/index.php/attendance/punchIn";
    const VIEW_ATTENDANCE_RECORD_LINK="./symfony/web/index.php/attendance/viewMyAttendanceRecord";
    private $user;

    public function __construct(User $user) {

        $this->user = $user;
        parent::setEmployeeNumber($user->getEmployeeNumber());
        parent::setUserId($user->getUserId());
        parent::setUserTimeZoneOffset($user->getUserTimeZoneOffset());
    }

    public function getAccessibleTimeMenus() {

        $topmenuItem = new TopMenuItem();
        $topmenuItem->setDisplayName(__("Timesheets"));
        $topmenuItem->setLink(EssUserRoleDecorator::VIEW_MY_TIMESHEET);
        $tempArray = $this->user->getAccessibleTimeMenus();
        array_push($tempArray, $topmenuItem);

        $topMenuItem = new TopMenuItem();
        $topMenuItem->setDisplayName(__("Attendance"));
        $topMenuItem->setLink(EssUserRoleDecorator::PUNCH_ATTENDANCE_RECORD);
        array_push($tempArray, $topMenuItem);


        return $tempArray;
    }

    public function getAccessibleTimeSubMenus() {
        $topmenuItem = new TopMenuItem();
        $topmenuItem->setDisplayName(__("My Timesheets"));
        $topmenuItem->setLink(EssUserRoleDecorator::VIEW_MY_TIMESHEET);
        $tempArray = $this->user->getAccessibleTimeMenus();
        array_push($tempArray, $topmenuItem);
        return $tempArray;
    }

    public function getAccessibleAttendanceSubMenus() {

        $topMenuItem = new TopMenuItem();
        $topMenuItem->setDisplayName(__("My Records"));
        $topMenuItem->setLink(EssUserRoleDecorator::VIEW_ATTENDANCE_RECORD_LINK);
        $tempArray = $this->user->getAccessibleAttendanceSubMenus();
        array_push($tempArray, $topMenuItem);

        $topmenuItem = new TopMenuItem();
        $topmenuItem->setDisplayName(__("Punch In/Out"));
        $topmenuItem->setLink(EssUserRoleDecorator::PUNCH_ATTENDANCE_RECORD);

        array_push($tempArray, $topmenuItem);
        return $tempArray;
    }

    public function getAllowedActions($workFlow, $state) {

        $accessFlowStateMachineService = new AccessFlowStateMachineService();
        $allowedActionsForEssUser = $accessFlowStateMachineService->getAllowedActions($workFlow, $state, EssUserRoleDecorator::ESS_USER);

        $existingAllowedActions = $this->user->getAllowedActions($workFlow, $state);

        if (is_null($allowedActionsForEssUser)) {
            return $existingAllowedActions;
        }

        $allowedActionsList = array_unique(array_merge($allowedActionsForEssUser, $existingAllowedActions));

        return $allowedActionsList;
    }

    public function getNextState($workFlow, $state, $action) {

        $accessFlowStateMachineService = new AccessFlowStateMachineService();
        $tempNextState = $accessFlowStateMachineService->getNextState($workFlow, $state, EssUserRoleDecorator::ESS_USER, $action);
        $temp = $this->user->getNextState($workFlow, $state, $action);

        if (is_null($tempNextState)) {

            return $temp;
        }

        return $tempNextState;
    }

    public function getActionableAttendanceStates($actions) {

        $accessFlowStateMachinService = new AccessFlowStateMachineService();
        $actionableAttendanceStatesForEssUser = $accessFlowStateMachinService->getActionableStates(PluginWorkflowStateMachine::FLOW_ATTENDANCE, EssUserRoleDecorator::ESS_USER, $actions);


        $actionableAttendanceStates = $this->user->getActionableAttendanceStates($actions);

        if (is_null($actionableAttendanceStatesForEssUser)) {
            return $actionableAttendanceStates;
        }

        $actionableAttendanceStatesList = array_unique(array_merge($actionableAttendanceStatesForEssUser, $actionableAttendanceStates));
        return $actionableAttendanceStatesList;
    }

    public function isAllowedToDefineTimeheetPeriod() {
        return $this->user->isAllowedToDefineTimeheetPeriod();
    }

}
