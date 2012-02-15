<?php

/**
 * viewLeaveRequestAction
 *
 * @author sujith
 */
class viewLeaveRequestAction extends sfAction {
    const MODE_SUPERVISOR_DETAILED_LIST = 'detailed_supervisor_list';
    const MODE_HR_ADMIN_DETAILED_LIST = 'detailed_hr_admin_list';
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

        $user = $this->getUser();
        $loggedInEmpNumber = $this->getUser()->getAttribute('auth.empNumber');

        if ($loggedInEmpNumber === $requesterEmpNumber) {
            $mode = LeaveListForm::MODE_MY_LEAVE_DETAILED_LIST;
        } else if ($user->getAttribute('auth.isAdmin') == 'Yes') {
            $mode = LeaveListForm::MODE_HR_ADMIN_DETAILED_LIST;
        } else if ($user->getAttribute('auth.isSupervisor')) {
            $mode = LeaveListForm::MODE_SUPERVISOR_DETAILED_LIST;
        } else {
            $mode = LeaveListForm::MODE_MY_LEAVE_DETAILED_LIST;
        }

        return $mode;
    }

    protected function getTitle($mode, $employee, $leaveList) {

        if ($mode === self::MODE_SUPERVISOR_DETAILED_LIST || $mode === self::MODE_HR_ADMIN_DETAILED_LIST) {
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
        $this->message = $this->getUser()->getFlash('message', '');
        $this->messageType = $this->getUser()->getFlash('messageType', '');
        $this->leaveRequestId = $request->getParameter('id');


        $leaveRequest = $this->getLeaveRequestService()->fetchLeaveRequest($this->leaveRequestId);
        $employee = $leaveRequest->getEmployee();

        $this->mode = $this->getMode($employee->getEmpNumber());

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