<?php
/**
 * viewLeaveListAction
 *
 * @author sujith
 */
class viewLeaveListAction extends sfAction {

    private $leavePeriodService;
    private $employeeService;
    private $leaveRequestService;

    /**
     * Returns Leave Period
     * @return LeavePeriodService
     */
    public function getLeavePeriodService() {

        if (is_null($this->leavePeriodService)) {
            $leavePeriodService = new LeavePeriodService();
            $leavePeriodService->setLeavePeriodDao(new LeavePeriodDao());
            $this->leavePeriodService = $leavePeriodService;
        }

        return $this->leavePeriodService;
    }

    public function getEmployeeService() {
        if (is_null($this->employeeService)) {
            $empService = new EmployeeService();
            $empService->setEmployeeDao(new EmployeeDao());
            $this->employeeService = $empService;
        }
        return $this->employeeService;
    }

    /**
     * Sets EmployeeService
     * @param EmployeeService $service
     */
    public function setEmployeeService(EmployeeService $service) {
        $this->employeeService = $service;
    }
    
    public function execute($request) {
        $this->setTemplate('viewLeaveList');
        $this->_setLoggedInUserDetails();

        if($this->userType == 'ESS') {
            $this->forward('leave', 'viewMyLeaveList');
        }
        
        $fromDate = $request->getPostParameter('calFromDate', null);
        $toDate = $request->getPostParameter('calToDate', null);
        $subunitId = $request->getPostParameter('cmbSubunit', null);
        $statuses = $request->getParameter('chkSearchFilter', array());

        $leavePeriodId = $request->getParameter('leavePeriodId', null);
        $leaveTypeId = $request->getParameter('leaveTypeId', null);
        $employeeId = $request->getParameter('employeeId', null);
        $employeeId = (empty($employeeId))? $request->getParameter("txtEmpID"):'';

        $statuses = (trim($request->getParameter('status') != ""))?array($request->getParameter('status')):$statuses;

        $page = $request->getParameter('page', 1);
        $message = $this->getUser()->getFlash('message', '');
        $messageType = $this->getUser()->getFlash('messageType', '');

        $leavePeriod = $this->getLeavePeriodService()->getCurrentLeavePeriod();

        if(trim($leavePeriodId) != "") {
            $leavePeriod = $this->getLeavePeriodService()->readLeavePeriod($leavePeriodId);
        } else {
            $leavePeriodId = $leavePeriod->getLeavePeriodId();
        }
        $employee = null;
        $overrideShowBackButton = false;
        $leaveRequest = null;

        $id = (int) $request->getParameter('id');

        if (empty($id)) {

            $mode = LeaveListForm::MODE_DEFAULT_LIST;

            $employeeService = $this->getEmployeeService();
            $employeeFilter = null;

            if (trim($employeeId) == "") {

                if ($this->userType == "Supervisor") {
                        $employeeFilter = $employeeService->getSupervisorEmployeeChain(Auth::instance()->getEmployeeNumber());
                }

                $employeeFilter = $employeeService->filterEmployeeListBySubUnit($employeeFilter, $subunitId);

            } else {
                $employeeFilter = $employeeService->getEmployee($employeeId);
                //this is a dirty workaround but witout modyfying searchLeaveRequests of Dao it is difficult
                if(!$employeeFilter instanceof Employee) {
                    $employeeFilter = new Employee();
                    $employeeFilter->setEmpNumber(0);
                }
                $employee = $employeeFilter;
                if(!empty($subunitId) && $subunitId > 0) {
                    $employeeFilter = $employeeService->filterEmployeeListBySubUnit(array(0 => $employee), $subunitId);
                }
                $overrideShowBackButton = true;
            }

            $dateRange = new DateRange($fromDate, $toDate);

            $searchParams = new ParameterObject(array(
                'dateRange' => $dateRange,
                'statuses' => $statuses,
                'employeeFilter' => $employeeFilter,
                'leavePeriod' => $leavePeriodId,
                'leaveType' => $leaveTypeId
            ));

            $result = $this->getLeaveRequestService()->searchLeaveRequests($searchParams, $page);
            $list = $result['list'];
            $recordCount = $result['meta']['record_count'];

            if ($recordCount == 0 && $request->isMethod("post")) {
                $message = __('No Records Found');
                $messageType = 'notice';
            }

            $this->pager = new SimplePager('LeaveList', sfConfig::get('app_items_per_page'));

            $this->pager->setPage($page);
            $this->pager->setNumResults($recordCount);
            $this->pager->init();

        } else {

            $mode = LeaveListForm::MODE_HR_ADMIN_DETAILED_LIST;
            $employee = $this->getLeaveRequestService()->fetchLeaveRequest($id)->getEmployee();
            $list = $this->getLeaveRequestService()->searchLeave($id);
            $leaveRequest = $this->getLeaveRequestService()->fetchLeaveRequest($id);
        }

        $leaveListForm = $this->getLeaveListForm($mode, $leavePeriod, $employee, $request, $this->loggedUserId, $leaveRequest);

        $list = (count($list)==0)?null:$list;
        $leaveListForm->setList($list);
        $leaveListForm->setShowBackButton($overrideShowBackButton);
        $leaveListForm->setEmployeeListAsJson($this->getEmployeeListAsJson());

        $this->leaveRequestId = $id;
        $this->form = $leaveListForm;
        $this->quotaArray = $this->form->getQuotaArray($list);
        $this->mode = $mode;
        $this->message = $message;
        $this->messageType = $messageType;
        $this->baseUrl = 'leave/viewLeaveList';
        $this->pagingUrl = '@leave_request_list';
        $this->page = $page;
    }

    protected function getLeaveListForm($mode, $leavePeriod, $employee, $request, $loggedInUserId, $leaveRequest) {
        $this->form = new LeaveListForm($mode, $leavePeriod, $employee, $request, $loggedInUserId, $leaveRequest);
        return $this->form;
    }

    private function _setLoggedInUserDetails() {

        $this->userType = 'ESS';

        if (!empty($_SESSION['empNumber'])) {
            $this->loggedUserId = $_SESSION['empNumber'];
        } else {
            $this->loggedUserId = 0; // Means default admin
        }

        if ($_SESSION['isSupervisor']) {
            $this->userType = 'Supervisor';
        }

        if(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']=='Yes') {
            $this->userType = 'Admin';
        }

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

    private function getEmployeeListAsJson() {

        $jsonArray	=	array();
        $escapeCharSet = array(38, 39, 34, 60, 61,62, 63, 64, 58, 59, 94, 96);
        $employeeService = new EmployeeService();
        $employeeList = array();

        if (Auth::instance()->hasRole(Auth::ADMIN_ROLE)) {
            $employeeList = $employeeService->getEmployeeList();
        }

        if ($_SESSION['isSupervisor'] && trim(Auth::instance()->getEmployeeNumber()) != "") {
            $employeeList = $employeeService->getSupervisorEmployeeChain(Auth::instance()->getEmployeeNumber());
        }
        $employeeUnique = array();
        foreach($employeeList as $employee) {
            if(!isset($employeeUnique[$employee->getEmpNumber()])) {
                $name = $employee->getFullName();

                foreach($escapeCharSet as $char) {
                    $name = str_replace(chr($char), (chr(92) . chr($char)), $name);
                }
                $employeeUnique[$employee->getEmpNumber()] = $name;
                array_push($jsonArray,"{name:\"".$name."\",id:\"".$employee->getEmpNumber()."\"}");
            }
        }

        $jsonString = " [".implode(",",$jsonArray)."]";
        return $jsonString;
    }
}
?>
