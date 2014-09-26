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
class viewAttendanceRecordAction extends baseAttendanceAction {

    private $employeeService;

    public function getEmployeeService() {

        if (is_null($this->employeeService)) {

            $this->employeeService = new EmployeeService();
        }

        return $this->employeeService;
    }

    public function setEmployeeService(EmployeeService $employeeService) {

        $this->employeeService = $employeeService;
    }

    public function execute($request) {

        $loggedInEmpNumber = $this->getContext()->getUser()->getEmployeeNumber();
        
        $userRoleManager = $this->getContext()->getUserRoleManager();
        
        $this->parmetersForListCompoment = array();
        $this->showEdit = false;

        $this->attendancePermissions = $this->getDataGroupPermissions('attendance_records');


        if (!$this->attendancePermissions->canRead()) {
            return $this->renderText(__("You are not allowed to view this page") . "!");
        }

        $this->trigger = $request->getParameter('trigger');

        if ($this->trigger) {
            $this->showEdit = true;
        }

        $this->date = $request->getParameter('date');
        $this->employeeId = $request->getParameter('employeeId');
        $this->employeeService = $this->getEmployeeService();
        $values = array('date' => $this->date, 'employeeId' => $this->employeeId, 'trigger' => $this->trigger);
        $this->form = new AttendanceRecordSearchForm(array(), $values);
        $this->actionRecorder = "viewEmployee";

        $isPaging = $request->getParameter('pageNo');

        $pageNumber = $isPaging;

        $noOfRecords = $noOfRecords = sfConfig::get('app_items_per_page');
        $offset = ($pageNumber >= 1) ? (($pageNumber - 1) * $noOfRecords) : ($request->getParameter('pageNo', 1) - 1) * $noOfRecords;

        $records = array();

        if ($this->attendancePermissions->canRead()) {
            $this->_setListComponent($records, $noOfRecords, $pageNumber, null, $this->showEdit);
        }

        if (!$this->trigger) {

            if ($request->isMethod('post')) {

                $this->form->bind($request->getParameter('attendance'));


                if ($this->form->isValid()) {
                    $this->allowedToDelete = array();
                    $this->allowedActions = array();

                    $this->allowedActions['Delete'] = false;
                    $this->allowedActions['Edit'] = false;
                    $this->allowedActions['PunchIn'] = false;
                    $this->allowedActions['PunchOut'] = false;

                    $post = $this->form->getValues();

                    if (!$this->employeeId) {
                        $empData = $post['employeeName'];
                        $this->employeeId = $empData['empId'];
                    }
                    if (!$this->date) {
                        $this->date = $post['date'];
                    }

                    if ($this->employeeId) {
                        $this->showEdit = true;
                    }

                    $isPaging = $request->getParameter('hdnAction') == 'search' ? 1 : $request->getParameter('pageNo', 1);

                    $pageNumber = $isPaging;

                    $noOfRecords = sfConfig::get('app_items_per_page');
                    $offset = ($pageNumber >= 1) ? (($pageNumber - 1) * $noOfRecords) : ($request->getParameter('pageNo', 1) - 1) * $noOfRecords;

                    $empRecords = array();
                    if (!$this->employeeId) {
//                        $empRecords = $this->employeeService->getEmployeeList('firstName', 'ASC', false);
                        $empRecords = UserRoleManagerFactory::getUserRoleManager()->getAccessibleEntities('Employee');
                        $count = count($empRecords);
                    } else {
                        $empRecords = $this->employeeService->getEmployee($this->employeeId);
                        $empRecords = array($empRecords);
                        $count = 1;
                    }

                    $records = array();
                    foreach ($empRecords as $employee) {
                        $hasRecords = false;

                        $attendanceRecords = $employee->getAttendanceRecord();
                        $total = 0;
                        foreach ($attendanceRecords as $attendance) {
                            $from = $this->date . " " . "00:" . "00:" . "00";
                            $end = $this->date . " " . "23:" . "59:" . "59";
                            if (strtotime($attendance->getPunchInUserTime()) >= strtotime($from) && strtotime($attendance->getPunchInUserTime()) <= strtotime($end)) {
                                if ($attendance->getPunchOutUtcTime()) {
                                    $total = $total + round((strtotime($attendance->getPunchOutUtcTime()) - strtotime($attendance->getPunchInUtcTime())) / 3600, 2);
                                }
                                $records[] = $attendance;
                                $hasRecords = true;
                            }
                        }

                        if ($hasRecords) {
                            $last = end($records);
                            $last->setTotal($total);
                        } else {
                            $attendance = new AttendanceRecord();
                            $attendance->setEmployee($employee);
                            $attendance->setTotal('---');
                            $records[] = $attendance;
                        }
                    }
                    
                    $params = array();
                    $this->parmetersForListCompoment = $params;

                    $rolesToExclude = array();
                    $rolesToInclude = array();
                    
                    if ($this->employeeId == $loggedInEmpNumber && $userRoleManager->essRightsToOwnWorkflow()) {
                        $rolesToInclude = array('ESS');
                    }
                    
                    $actions = array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_OUT_TIME, PluginWorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME);
                    $actionableStates = $userRoleManager->getActionableStates(WorkflowStateMachine::FLOW_ATTENDANCE, 
                            $actions, $rolesToExclude, $rolesToInclude, array('Employee' => $this->employeeId));
                    $recArray = array();

                    if ($records != null) {
                        if ($actionableStates != null) {
                            foreach ($actionableStates as $state) {
                                foreach ($records as $record) {
                                    if ($state == $record->getState()) {
                                            $this->allowedActions['Edit'] = true;
                                        break;
                                    }
                                }
                            }
                        }

                        $actions = array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_DELETE);
                        $actionableStates = $userRoleManager->getActionableStates(WorkflowStateMachine::FLOW_ATTENDANCE, 
                            $actions, $rolesToExclude, $rolesToInclude, array('Employee' => $this->employeeId));

                        if ($actionableStates != null) {
                            foreach ($actionableStates as $state) {
                                foreach ($records as $record) {
                                    if ($state == $record->getState()) {
                                            $this->allowedActions['Delete'] = true;
                                        break;
                                    }
                                }
                            }
                        }

                        foreach ($records as $record) {
                            $this->allowedToDelete[] = $userRoleManager->isActionAllowed(WorkflowStateMachine::FLOW_ATTENDANCE, $record->getState(), PluginWorkflowStateMachine::ATTENDANCE_ACTION_DELETE, array(), array(), array('Employee' => $this->employeeId));
                            $recArray[] = $record;
                        }
                    } else {
                        $attendanceRecord = null;
                    }

                    /** 
                     * TODO: Following code looks overly complicated. Simplify
                     */
                    $actions = array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_IN, PluginWorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_OUT);
                    $allowedActionsList = array();
                    $actionableStates = $userRoleManager->getActionableStates(WorkflowStateMachine::FLOW_ATTENDANCE, 
                            $actions, $rolesToExclude, $rolesToInclude, array('Employee' => $this->employeeId));

                    if ($actionableStates != null) {
                        if (!empty($recArray)) {
                            $lastRecordPunchOutTime = $recArray[count($records) - 1]->getPunchOutUserTime();
                            if (empty($lastRecordPunchOutTime)) {
                                $attendanceRecord = "";
                            } else {
                                $attendanceRecord = null;
                            }
                        }

                        foreach ($actionableStates as $actionableState) {
  
                            $allowedActionsArray = $userRoleManager->getAllowedActions(WorkflowStateMachine::FLOW_ATTENDANCE, 
                                $actionableState, array(), array(), array('Employee' => $this->employeeId));
                            
                            if (!is_null($allowedActionsArray)) {

                                $allowedActionsList = array_unique(array_merge(array_keys($allowedActionsArray), $allowedActionsList));
                            }
                        }

                        if ((is_null($attendanceRecord)) && (in_array(WorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_IN, $allowedActionsList))) {
                                $this->allowedActions['PunchIn'] = true;
                        }
                        if ((!is_null($attendanceRecord)) && (in_array(WorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_OUT, $allowedActionsList))) {
                                $this->allowedActions['PunchOut'] = true;
                        }
                    }
                    if ($this->employeeId == '') {
                        $this->showEdit = FALSE;
                    }
                    
                    $this->_setListComponent($records, $noOfRecords, $pageNumber, $count, $this->showEdit, $this->allowedActions);
                }
            }
        }
    }

    private function _setListComponent($records, $noOfRecords, $pageNumber, $count = null, $showEdit = null, $allowedActions = null) {

        $configurationFactory = new AttendanceRecordHeaderFactory();
        $userRoleManager = $this->getContext()->getUserRoleManager();
        $loggedInEmpNumber = $this->getUser()->getEmployeeNumber();

        $notSelectable = array();
        foreach ($records as $record) {
            if (!$userRoleManager->isActionAllowed(WorkflowStateMachine::FLOW_ATTENDANCE, 
                    $record->getState(), WorkflowStateMachine::ATTENDANCE_ACTION_DELETE, 
                    array(), array(), array('Employee' => $this->employeeId))) {          
                $notSelectable[] = $record->getId();
            }
        }

        $buttons = array();
        $canSelect = false;
        if (isset($allowedActions)) {
            if (isset($showEdit) && $showEdit) {
                if ($allowedActions['Edit']) :
                    $buttons['Edit'] = array('label' => __('Edit'), 'type' => 'button',);
                endif;
                if ($allowedActions['PunchIn']) :
                    $buttons['PunchIn'] = array('label' => __('Add Attendance Records'), 'type' => 'button', 'class' => 'punch');
                endif;
                if ($allowedActions['PunchOut']) :
                    $buttons['PunchOut'] = array('label' => __('Add Attendance Records'), 'type' => 'button', 'class' => 'punch');
                endif;
            }
            if ($allowedActions['Delete']) :
                $canSelect = true;
                $buttons['Delete'] = array('label' => __('Delete'),
                    'type' => 'submit',
                    'data-toggle' => 'modal',
                    'data-target' => '#dialogBox',
                    'class' => 'delete');
            endif;
        }
        $configurationFactory->setRuntimeDefinitions(array(
            'buttons' => $buttons,
            'unselectableRowIds' => $notSelectable,
            'hasSelectableRows' => $canSelect
        ));

        ohrmListComponent::setActivePlugin('orangehrmAttendancePlugin');
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setListData($records);
        ohrmListComponent::setPageNumber($pageNumber);
        ohrmListComponent::setItemsPerPage($noOfRecords);
        ohrmListComponent::setNumberOfRecords($count);
    }
    
}

