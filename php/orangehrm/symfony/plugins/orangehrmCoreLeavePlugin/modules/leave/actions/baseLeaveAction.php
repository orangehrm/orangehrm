<?php

abstract class baseLeaveAction extends sfAction {

    public $form;
    
    protected $workWeekService;
    protected $leaveEntitlementService;
    protected $leaveTypeService;
    
    /**
     *
     * @return WorkWeekService
     */
    protected function getWorkWeekService() {
        if (!($this->workWeekService instanceof WorkWeekService)) {
            $this->workWeekService = new WorkWeekService();
        }
        return $this->workWeekService;
    }
    
    /**
     *
     * @param WorkWeekService $service 
     */
    protected function setWorkWeekService(WorkWeekService $service) {
        $this->workWeekService = $service;
    }
    
    /**
     *
     * @return LeaveEntitlementService
     */
    protected function getLeaveEntitlementService() {
        if (!($this->leaveEntitlementService instanceof LeaveEntitlementService)) {
            $this->leaveEntitlementService = new LeaveEntitlementService();
        }
        return $this->leaveEntitlementService;
    }
    
    /**
     *
     * @param LeaveEntitlementService $service 
     */
    protected function setLeaveEntitlementService(LeaveEntitlementService $service) {
        $this->leaveEntitlementService = $service;
    }
    
    /**
     *
     * @return LeaveTypeService
     */
    protected function getLeaveTypeService() {
        if (!($this->leaveTypeService instanceof LeaveTypeService)) {
            $this->leaveTypeService = new LeaveTypeService();
        }
        return $this->leaveTypeService;
    }
    
    /**
     *
     * @param LeaveTypeService $service 
     */
    protected function setLeaveTypeService(LeaveTypeService $service) {
        $this->leaveTypeService = $service;
    }

    /**
     * Returns Logged in user details
     * @return array
     * @todo Refactor this method to use auth classes instead of directly accessing the session
     */
    protected function getLoggedInUserDetails() {
        $userDetails['userType'] = 'ESS';

        /* Value 0 is assigned for default admin */
        $userDetails['loggedUserId'] = (empty($_SESSION['empNumber'])) ? 0 : $_SESSION['empNumber'];
        $userDetails['empId'] = (empty($_SESSION['empID'])) ? 0 : $_SESSION['empID'];

        if ($_SESSION['isSupervisor']) {
            $userDetails['userType'] = 'Supervisor';
        }

        if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 'Yes') {
            $userDetails['userType'] = 'Admin';
        }
        
        return $userDetails;
    }

}

