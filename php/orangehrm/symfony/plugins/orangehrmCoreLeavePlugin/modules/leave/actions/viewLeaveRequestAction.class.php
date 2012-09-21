<?php

/**
 * viewLeaveRequestAction
 *
 * @author sujith
 */
class viewLeaveRequestAction extends sfAction {

    const MODE_ADMIN_DETAILED_LIST = 'detailed_hr_admin_list';
    const MODE_MY_LEAVE_DETAILED_LIST = 'my_leave_detailed_list';

    private $leaveRequestService;

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

    protected function getMode($requesterEmpNumber) {

        $loggedInEmpNumber = $this->getUser()->getAttribute('auth.empNumber');

        if ($loggedInEmpNumber === $requesterEmpNumber) {
            $mode = self::MODE_MY_LEAVE_DETAILED_LIST;
        } else {
            $manager = $this->getContext()->getUserRoleManager();
            $accessible = $manager->isEntityAccessible('Employee', $requesterEmpNumber);
            if ($accessible) {
                $mode = self::MODE_ADMIN_DETAILED_LIST;
            } else {
                $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
            }
            
        }

        return $mode;
    }
    
    protected function isEssMode($requesterEmpNumber) {
         $userMode = 'ESS';
         
        if ($_SESSION['isSupervisor']) {
            if($this->getMode($requesterEmpNumber) == self::MODE_MY_LEAVE_DETAILED_LIST) {
                $userMode = 'ESS';
            } else {
                $userMode = 'Supervisor';
            }
        }

        if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 'Yes') {
            if ($this->getMode($requesterEmpNumber) == self::MODE_MY_LEAVE_DETAILED_LIST) {
                $userMode = 'ESS';
            } else {
                $userMode = 'Admin';
            }
        }
        
        return ($userMode == 'ESS');
    }

    protected function getTitle($mode, $employee, $leaveList) {

        if ($mode === self::MODE_ADMIN_DETAILED_LIST) {
            $range = $this->getDateRangeString($leaveList);
            $title = __('Leave Request (%date_range%) %name%', array('%date_range%' => $range, '%name%' => $employee->getFullName()));
        } elseif ($mode === self::MODE_MY_LEAVE_DETAILED_LIST) {
            // Do this for 
            $title = __('My Leave Details');
        }

        return $title;
    }

    /**
     * 
     * @return string
     */
    protected function getDateRangeString($leaveList) {
        $range = '';
        $count = count($leaveList);
        if ($count == 1) {
            $range = set_datepicker_date_format($leaveList[0]->getLeaveDate());
        } else if ($count > 1) {
            $range = set_datepicker_date_format($leaveList[0]->getLeaveDate());
            $range .= " " . __('to') . " ";
            $range .= set_datepicker_date_format($leaveList[$count - 1]->getLeaveDate());
        }

        return $range;
    }

    public function execute($request) {

        $this->backUrl = stripos($request->getReferer(), 'viewMyLeaveList') === FALSE ?
                'leave/viewLeaveList' : 'leave/viewMyLeaveList';
        
        if ($this->getUser()->hasFlash('myLeave')) {
            $myLeave = $this->getUser()->getFlash('myLeave');
            if ($myLeave) {
                $this->backUrl = 'leave/viewMyLeaveList';
            }
        }
 
        if ($this->backUrl === 'leave/viewMyLeaveList') {
            $this->getUser()->setFlash('myLeave', true);
        }
        
        $this->message = $this->getUser()->getFlash('message', '');
        $this->messageType = $this->getUser()->getFlash('messageType', '');
        $this->leaveRequestId = $request->getParameter('id');


        $leaveRequest = $this->getLeaveRequestService()->fetchLeaveRequest($this->leaveRequestId);
        $employee = $leaveRequest->getEmployee();

        $this->mode = $this->getMode($employee->getEmpNumber());
        $this->essMode = $this->isEssMode($employee->getEmpNumber());

        $list = $this->getLeaveRequestService()->searchLeave($this->leaveRequestId);

        $this->title = $this->getTitle($this->mode, $employee, $list);
        $this->baseUrl = 'leave/viewLeaveRequest';

        $this->setListComponent($list);
        $this->setTemplate('viewLeaveRequest');
    }

    protected function setListComponent($leaveList) {

        ohrmListComponent::setConfigurationFactory($this->getListConfigurationFactory());
        ohrmListComponent::setActivePlugin('orangehrmCoreLeavePlugin');
        ohrmListComponent::setListData($leaveList);
        ohrmListComponent::setItemsPerPage(sfConfig::get('app_items_per_page'));
        ohrmListComponent::setNumberOfRecords(count($leaveList));
    }

    protected function getListConfigurationFactory() {
        DetailedLeaveListConfigurationFactory::setListMode($this->mode);
        $configurationFactory = new DetailedLeaveListConfigurationFactory();

        return $configurationFactory;
    }

}