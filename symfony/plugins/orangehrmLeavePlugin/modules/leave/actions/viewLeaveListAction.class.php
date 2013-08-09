<?php

class viewLeaveListAction extends baseLeaveAction {

    protected $leavePeriodService;
    protected $employeeService;
    protected $leaveRequestService;
    protected $requestedMode;

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

    protected function getMode() {
        $mode = LeaveListForm::MODE_ADMIN_LIST;

        return $mode;
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

    public function execute($request) {

        $this->mode = $mode = $this->getMode();
        $this->essMode = $this->isEssMode();
        $this->leaveListPermissions = $this->getPermissions();
        $this->commentPermissions = $this->getCommentPermissions();
        $this->leavecommentForm = new LeaveCommentForm(array(),array(),true);
        $this->form = $this->getLeaveListForm($mode);
        $values = array();

        $page = $request->getParameter('hdnAction') == 'search' ? 1 : $request->getParameter('pageNo', 1);

        // Check for parametes sent from direct links
        // (PIM: 'txtEmpID' will be available as a get parameter)
        // (Leave Summary Links: leavePeriodId, leaveTypeId and status)
        $empNumber = $request->getParameter('empNumber');

        $fromDateParam = $request->getParameter('fromDate');
        $toDateParam = $request->getParameter('toDate');
        $leaveTypeId = $request->getParameter('leaveTypeId');
        $leaveStatusId = $request->getParameter('status');
        $stdDate = $request->getParameter('stddate');

        if ($request->isMethod('post')) {

            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {
                $values = $this->form->getValues();
                $this->_setFilters($mode, $values);
            } else {

                if ($this->form->hasErrors()) {
                    echo $this->form->renderGlobalErrors();
                    foreach ($this->form->getWidgetSchema()->getPositions() as $widgetName) {
                        echo $widgetName . '--[' . $this->form[$widgetName]->renderError() . "]<br/>";
                    }
                }
            }
        } else if ($request->hasParameter('reset')) {
            $values = $this->form->getDefaults();

            // Defaults have localized dates, therefore, convert to standard format 
            $values['calFromDate'] = $this->_getStandardDate($values['calFromDate']);
            $values['calToDate'] = $this->_getStandardDate($values['calToDate']);
            $this->_setFilters($mode, $values);
        } else {

            // If request was link from another page:
            if (!empty($empNumber)) {
                $employee = $this->getEmployeeService()->getEmployee($empNumber);

                // set default to employee name field.
                if (!empty($employee)) {

                    // Get Default settings and override as needed.
                    $values = $this->form->getDefaults();

                    // Defaults have localized dates, therefore, convert to standard format 
                    $values['calFromDate'] = $this->_getStandardDate($values['calFromDate']);
                    $values['calToDate'] = $this->_getStandardDate($values['calToDate']);

                    $employeeName = $employee->getFullName();
                    $terminationId = $employee->getTerminationId();
                    $empData = array('empName' => $employeeName, 'empId' => $employee->getEmpNumber());

                    $this->form->setDefault('txtEmployee', $empData);
                    $values['txtEmployee'] = $empData;

                    if (!empty($terminationId)) {
                        $terminatedEmp = 'on';
                        $values['cmbWithTerminated'] = $terminatedEmp;
                        $this->form->setDefault('cmbWithTerminated', $terminatedEmp);
                    }
                    if (!empty($fromDateParam) && !empty($toDateParam)) {

                        if (empty($stdDate) || $stdDate != 1) {
                            $values['calFromDate'] = $this->_getStandardDate($fromDateParam);
                            $values['calToDate'] = $this->_getStandardDate($toDateParam);
                        } else {
                            $values['calFromDate'] = $fromDateParam;
                            $values['calToDate'] = $toDateParam;
                        }
                        $this->form->setDefault('calFromDate', set_datepicker_date_format($values['calFromDate']));
                        $this->form->setDefault('calToDate', set_datepicker_date_format($values['calToDate']));
                    }

                    if (!empty($leaveTypeId)) {
                        $values['leaveTypeId'] = $leaveTypeId;
                    }
                    if (!empty($leaveStatusId)) {
                        $values['chkSearchFilter'] = $leaveStatusId;
                        $this->form->setDefault('chkSearchFilter', array($leaveStatusId));
                    }

                    $this->_setFilters($mode, $values);
                }
            } else {

                // (paging, direct access of leave list)
                $values = $this->_getFilters($mode);
                $this->form->setDefaults($values);

                // Session filters have dates in standard format. Therefore, convert to localized format for
                // display
                $this->form->setDefault('calFromDate', set_datepicker_date_format($values['calFromDate']));
                $this->form->setDefault('calToDate', set_datepicker_date_format($values['calToDate']));

                $page = $request->getParameter('pageNo', null);
                if (empty($page)) {
                    $page = $this->_getPage($mode);
                }
            }
        }

        $subunitId = $this->_getFilterValue($values, 'cmbSubunit', null);
        $statuses = $this->_getFilterValue($values, 'chkSearchFilter', array());
        $terminatedEmp = $this->_getFilterValue($values, 'cmbWithTerminated', null);
        $fromDate = $this->_getFilterValue($values, 'calFromDate', null);
        $toDate = $this->_getFilterValue($values, 'calToDate', null);
        $empData = $this->_getFilterValue($values, 'txtEmployee', null);
        $employeeName = $empData['empName'];

        $message = $this->getUser()->getFlash('message', '');
        $messageType = $this->getUser()->getFlash('messageType', '');

        $employeeFilter = $this->getEmployeeFilter($mode, $empNumber);

        $searchParams = new ParameterObject(array(
                    'dateRange' => new DateRange($fromDate, $toDate),
                    'statuses' => $statuses,
                    'leaveTypeId' => $leaveTypeId,
                    'employeeFilter' => $employeeFilter,
                    'noOfRecordsPerPage' => sfConfig::get('app_items_per_page'),
                    'cmbWithTerminated' => $terminatedEmp,
                    'subUnit' => $subunitId,
                    'employeeName' => $employeeName
                ));


        $result = $this->searchLeaveRequests($searchParams, $page);
        $list = $result['list'];
        $recordCount = $result['meta']['record_count'];

        if ($recordCount == 0 && $request->isMethod("post")) {
            $message = __('No Records Found');
            $messageType = 'notice';
        }

        $list = empty($list) ? null : $list;
        $this->form->setList($list);
        //$this->form->setEmployeeList($this->getEmployeeList());

        $this->message = $message;
        $this->messageType = $messageType;
        $this->baseUrl = $mode == LeaveListForm::MODE_MY_LEAVE_LIST ? 'leave/viewMyLeaveList' : 'leave/viewLeaveList';

        $this->_setPage($mode, $page);

        if ($this->leaveListPermissions->canRead()) {
            $this->setListComponent($list, $recordCount, $page);
        }
        $balanceRequest = array();

        foreach ($list as $row) {
            $dates = $row->getLeaveStartAndEndDate();
            $balanceRequest[] = array($row->getEmpNumber(), $row->getLeaveTypeId(), $dates[0], $dates[1]);
        }

        $this->balanceQueryData = json_encode($balanceRequest);

        $this->setTemplate('viewLeaveList');
    }

    protected function searchLeaveRequests($searchParams, $page) {
        $result = $this->getLeaveRequestService()->searchLeaveRequests($searchParams, $page, false, false, true, true);
        return $result;
    }

    protected function setListComponent($leaveList, $count, $page) {

        ohrmListComponent::setConfigurationFactory($this->getListConfigurationFactory());
        ohrmListComponent::setActivePlugin('orangehrmLeavePlugin');
        ohrmListComponent::setListData($leaveList);
        ohrmListComponent::setItemsPerPage(sfConfig::get('app_items_per_page'));
        ohrmListComponent::setNumberOfRecords($count);
        ohrmListComponent::setPageNumber($page);
    }

    protected function getListConfigurationFactory() {
        $loggedInEmpNumber = $this->getUser()->getAttribute('auth.empNumber');
        LeaveListConfigurationFactory::setListMode($this->mode);
        LeaveListConfigurationFactory::setLoggedInEmpNumber($loggedInEmpNumber);
        $configurationFactory = new LeaveListConfigurationFactory();

        return $configurationFactory;
    }

    protected function getLeaveListForm($mode) {
        $this->form = new LeaveListForm($mode);
        return $this->form;
    }

    /**
     * Get employee number search filter
     * 
     * @param string $mode Leave list mode.
     * @param int $empNumber employee number
     * @return mixed Array of employee numbers or an employee number.
     */
    protected function getEmployeeFilter($mode, $empNumber) {

        $loggedInEmpNumber = $this->getUser()->getAttribute('auth.empNumber');

        // default filter to null. Will fetch all employees
        $employeeFilter = null;

        if ($mode == LeaveListForm::MODE_MY_LEAVE_LIST) {

            $employeeFilter = $loggedInEmpNumber;
        } else {
            $manager = $this->getContext()->getUserRoleManager();
            $requiredPermissions = array(
                BasicUserRoleManager::PERMISSION_TYPE_ACTION => array('view_leave_list'));

            $accessibleEmpIds = $manager->getAccessibleEntityIds('Employee', null, null, array(), array(), $requiredPermissions);

            if (empty($empNumber)) {
                $employeeFilter = $accessibleEmpIds;
            } else {
                if (in_array($empNumber, $accessibleEmpIds)) {
                    $employeeFilter = $empNumber;
                } else {
                    // Requested employee is not accessible. 
                    $employeeFilter = array();
                }
            }
        }

        return $employeeFilter;
    }

    protected function getEmployeeList() {

        $employeeService = new EmployeeService();
        $employeeList = array();

        if (Auth::instance()->hasRole(Auth::ADMIN_ROLE)) {
            $properties = array("empNumber", "firstName", "middleName", "lastName", 'termination_id');
            $employeeList = UserRoleManagerFactory::getUserRoleManager()->getAccessibleEntityProperties('Employee', $properties);
        }

        if ($_SESSION['isSupervisor'] && trim(Auth::instance()->getEmployeeNumber()) != "") {
            $employeeList = $employeeService->getSubordinateList(Auth::instance()->getEmployeeNumber());
        }

        return $employeeList;
    }

    /**
     * Set's the current page number in the user session.
     * @param $page int Page Number
     * @return None
     */
    protected function _setPage($mode, $page) {
        $this->getUser()->setAttribute($mode . '.page', $page, 'leave_list');
    }

    /**
     * Get the current page number from the user session.
     * @return int Page number
     */
    protected function _getPage($mode) {
        return $this->getUser()->getAttribute($mode . '.page', 1, 'leave_list');
    }

    /**
     * Remember filter values in session.
     * 
     * Dates are expected in standard date format (yy-dd-mm, 2012-21-02).
     * 
     * @param mode Leave list mode. One of (LeaveListForm::MODE_ADMIN_LIST,
     *                                      LeaveListForm::MODE_MY_LEAVE_LIST)                            
     * @param array $filters Filters
     * @return unknown_type
     */
    protected function _setFilters($mode, array $filters) {
        return $this->getUser()->setAttribute($mode . '.filters', $filters, 'leave_list');
    }

    /**
     *
     * @return unknown_type
     */
    protected function _getFilters($mode) {
        return $this->getUser()->getAttribute($mode . '.filters', null, 'leave_list');
    }

    protected function _getFilterValue($filters, $parameter, $default = null) {
        $value = $default;
        if (isset($filters[$parameter])) {
            $value = $filters[$parameter];
        }

        return $value;
    }

    protected function _isRequestFromLeaveSummary($request) {

        $txtEmpID = $request->getGetParameter('txtEmpID');

        if (!empty($txtEmpID)) {
            return true;
        }

        return false;
    }

    protected function _getStandardDate($localizedDate) {
        $localizationService = new LocalizationService();
        $format = sfContext::getInstance()->getUser()->getDateFormat();
        $trimmedValue = trim($localizedDate);
        $result = $localizationService->convertPHPFormatDateToISOFormatDate($format, $trimmedValue);
        return $result;
    }
    
    protected function getPermissions(){
        return $this->getDataGroupPermissions('leave_list', false);
    }
    
    protected function getCommentPermissions(){
        return $this->getDataGroupPermissions('leave_list_comments', false);
    }    

}
