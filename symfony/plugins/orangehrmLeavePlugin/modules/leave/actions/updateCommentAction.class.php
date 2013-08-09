<?php

/**
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * You should have received a copy of the OrangeHRM Enterprise  proprietary license file along
 * with this program; if not, write to the OrangeHRM Inc. 538 Teal Plaza, Secaucus , NJ 0709
 * to get the file.
 *
 */

/**
 * updateComment
 */
class updateCommentAction extends baseLeaveAction {
    
    protected $employeeService;
    protected $leaveRequestService;
    
    /**
     *
     * @return EmployeeService
     */
    public function getEmployeeService() {
        if (!($this->employeeService instanceof EmployeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }

    /**
     *
     * @param EmployeeService $service 
     */
    public function setEmployeeService(EmployeeService $service) {
        $this->employeeService = $service;
    }
    
    /**
     *
     * @return LeaveRequestService
     */
    public function getLeaveRequestService() {
        if (is_null($this->leaveRequestService)) {
            $leaveRequestService = new LeaveRequestService();
            $leaveRequestService->setLeaveRequestDao(new LeaveRequestDao());
            $this->leaveRequestService = $leaveRequestService;
        }

        return $this->leaveRequestService;
    }

    /**
     *
     * @param LeaveRequestService $leaveRequestService
     * @return void
     */
    public function setLeaveRequestService(LeaveRequestService $leaveRequestService) {
        $this->leaveRequestService = $leaveRequestService;
    }    
    
    public function execute($request) {
        
        $leaveRequestService = $this->getLeaveRequestService();
        $leaveRequestId = trim($request->getParameter("leaveRequestId"));
        $leaveId = trim($request->getParameter("leaveId"));
        $comment = trim($request->getParameter("leaveComment"));
        
        $user = $this->getUser();
        $loggedInUserId = $user->getAttribute('auth.userId');
        $loggedInEmpNumber = $user->getAttribute('auth.empNumber');

        if (!empty($loggedInEmpNumber)) {
            $employee = $this->getEmployeeService()->getEmployee($loggedInEmpNumber);
            $createdBy = $employee->getFullName();
        } else {
            $createdBy = $user->getAttribute('auth.firstName');
        }
        
        $savedComment = NULL;

                    
        if ($leaveRequestId != "") {
            
            $leaveRequest = $this->getLeaveRequestService()->fetchLeaveRequest($leaveRequestId);            
            $permissions = $this->getCommentPermissions($loggedInEmpNumber == $leaveRequest->getEmpNumber());

            if ($permissions->canCreate()) {
                $form = new LeaveCommentForm( array(),array(),true);

                if ($form->getCSRFToken() == $request->getParameter("token")) {
                    $savedComment = $leaveRequestService->saveLeaveRequestComment($leaveRequestId, 
                        $comment, $createdBy, $loggedInUserId, $loggedInEmpNumber);
                }
            }
        }

        if ($leaveId != "") {
            $leave = $this->getLeaveRequestService()->readLeave($leaveId);
            
            $permissions = $this->getCommentPermissions($loggedInEmpNumber == $leave->getEmpNumber());
            if ($permissions->canCreate()) {
            
                $form = new LeaveCommentForm( array(),array(),true);

                if ($form->getCSRFToken() == $request->getParameter("token")) {
                $savedComment = $leaveRequestService->saveLeaveComment($leaveId, 
                    $comment, $createdBy, $loggedInUserId, $loggedInEmpNumber);
                }
            }
        }
        
        if (!empty($savedComment)) {
                $created = new DateTime($savedComment->getCreated());
                $createdAt = set_datepicker_date_format($created->format('Y-m-d')) . ' ' . $created->format('H:i');
                
                $returnText = $createdAt . ' ' . $savedComment->getCreatedByName() . "\n\n" .
                        $savedComment->getComments();            
        } else {
            $returnText = 0;
        }

        if ($this->getUser()->hasFlash('myLeave')) {
            $this->getUser()->setFlash('myLeave', true);
        }
        
        return $this->renderText($returnText);
    }

    protected function isEssMode() {
        $userMode = 'ESS';

        if ($_SESSION['isSupervisor']) {
            $userMode = 'Supervisor';
        }

        if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 'Yes') {
            $userMode = 'Admin';
        }

        return ($userMode == 'ESS');
    }
    
    protected function getCommentPermissions($self){
        return $this->getDataGroupPermissions('leave_list_comments', $self);
    }    

}