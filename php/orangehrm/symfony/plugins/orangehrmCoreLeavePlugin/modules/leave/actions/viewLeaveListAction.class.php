<?php

/**
 * viewLeaveListAction
 *
 * @author sujith
 */
class viewLeaveListAction extends sfAction {

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

        $user = $this->getUser();

        if ($user->getAttribute('auth.isAdmin') == 'Yes') {
            $mode = LeaveListForm::MODE_ADMIN_LIST;
        } else if ($user->getAttribute('auth.isSupervisor')) {
            $mode = LeaveListForm::MODE_SUPERVISOR_LIST;
        } else {
            $mode = LeaveListForm::MODE_MY_LEAVE_LIST;
        }

        // If my leave list was requested and user has a valid
        // employee number, switch to my leave list.
//        if ($mode != LeaveListForm::MODE_MY_LEAVE_LIST &&
//                $this->requestedMode == LeaveListForm::MODE_MY_LEAVE_LIST &&
//                !empty($empNumber)) {
//
//            $mode = LeaveListForm::MODE_MY_LEAVE_LIST;
//        }
        
        return $mode;
    }

    public function execute($request) {

        $this->mode = $mode = $this->getMode();

        $this->form = $this->getLeaveListForm($mode);
        $values = array();
        $page = 1;
        
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
              $this->_setFilters($mode, $values);                            
        } else {
              $values = $this->_getFilters($mode);
              $this->form->setDefaults($values);
              $page = $request->getParameter('pageNo', null);
              if (empty($page)) {
                  $page = $this->_getPage($mode);
              }
        }


        $subunitId = $this->_getFilterValue($values, 'cmbSubunit', null);
        $statuses = $this->_getFilterValue($values, 'chkSearchFilter', array());
        $terminatedEmp = $this->_getFilterValue($values, 'cmbWithTerminated', null);
        $fromDate = $this->_getFilterValue($values, 'calFromDate', null);
        $toDate = $this->_getFilterValue($values, 'calToDate', null);
        $employeeName = $this->_getFilterValue($values, 'txtEmployee', null);
        

        // Check for request from pim: 'txtEmpID' will be available as a get parameter.
        $empNumber = $request->getGetParameter('txtEmpID');
        $leavePeriodId = $request->getGetParameter('leavePeriodId');
        $leaveTypeId = $request->getGetParameter('leaveTypeId');
        if (!empty($empNumber)) {
            $employee = $this->getEmployeeService()->getEmployee($empNumber);
            
            // set default to employee name field.
            if (!empty($employee)) {
                $employeeName = $employee->getFullName();
                $terminationId = $employee->getTerminationId();
                
                $this->form->setDefault('txtEmployee', $employeeName);
                $values['txtEmployee'] = $employeeName;
                
                if (!empty($terminationId)) {
                    $terminatedEmp = 'on';
                    $values['cmbWithTerminated'] = $terminatedEmp;
                }
                if (!empty($leavePeriodId)) {
                   $leavePeriod = $this->getLeavePeriodService()->readLeavePeriod($leavePeriodId);
                   if($leavePeriod instanceof LeavePeriod){
                       $values['calFromDate'] = $leavePeriod->getStartDate();
                       $values['calToDate'] = $leavePeriod->getEndDate();
                   }
                }
                if (!empty($leaveTypeId)) {
                   $values['leaveTypeId'] = $leaveTypeId;
                }
                $this->_setFilters($mode, $values);
            }
        }

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
        $this->form->setEmployeeListAsJson($this->getEmployeeListAsJson());
        
        $this->message = $message;
        $this->messageType = $messageType;
        $this->baseUrl = $mode == LeaveListForm::MODE_MY_LEAVE_LIST ? 'leave/viewMyLeaveList' : 'leave/viewLeaveList';

        $this->_setPage($mode, $page);
        
        $this->setListComponent($list, $recordCount, $page);

        $this->setTemplate('viewLeaveList');
    }
    
    protected function searchLeaveRequests($searchParams, $page) {
        $result = $this->getLeaveRequestService()->searchLeaveRequests($searchParams, $page);
        return $result;
    }
    
    protected function setListComponent($leaveList, $count, $page) {
        
        ohrmListComponent::setConfigurationFactory($this->getListConfigurationFactory());
        ohrmListComponent::setActivePlugin('orangehrmCoreLeavePlugin');
        ohrmListComponent::setListData($leaveList);
        ohrmListComponent::setItemsPerPage(sfConfig::get('app_items_per_page'));
        ohrmListComponent::setNumberOfRecords($count);      
        ohrmListComponent::setPageNumber($page);
    }
    
    protected function getListConfigurationFactory() {
        LeaveListConfigurationFactory::setListMode($this->mode);
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
        $employeeFilter = null;
        
        if ($mode == LeaveListForm::MODE_MY_LEAVE_LIST) {
            
            $employeeFilter = $loggedInEmpNumber;
        } else if ($mode == LeaveListForm::MODE_ADMIN_LIST) {
            
            if (!empty($empNumber)) {
                $employeeFilter = $empNumber;
            }
        } else if ($mode == LeaveListForm::MODE_SUPERVISOR_LIST) {
            
            $employeeFilter = array();
            
            $subordinates = $this->getEmployeeService()->getSupervisorEmployeeChain($loggedInEmpNumber, true);
            
            foreach ($subordinates as $subordinate) {
                $subordinateId = $subordinate->getEmpNumber();
                
                if (empty($empNumber) || ($empNumber == $subordinateId)) {
                    $employeeFilter[] = $subordinateId;
                }
            }
        }
        return $employeeFilter;
    }

    private function getEmployeeListAsJson() {

        $jsonArray = array();
        $employeeService = new EmployeeService();
        $employeeList = array();

        if (Auth::instance()->hasRole(Auth::ADMIN_ROLE)) {
            $employeeList = $employeeService->getEmployeeList('empNumber', 'ASC', false);
        }

        if ($_SESSION['isSupervisor'] && trim(Auth::instance()->getEmployeeNumber()) != "") {
            $employeeList = $employeeService->getSupervisorEmployeeChain(Auth::instance()->getEmployeeNumber());
        }
        $employeeUnique = array();
        foreach ($employeeList as $employee) {
            if (!isset($employeeUnique[$employee->getEmpNumber()])) {
                $name = $employee->getFullName();

                $employeeUnique[$employee->getEmpNumber()] = $name;
                $jsonArray[] = array('name' => $name, 'id' => $employee->getEmpNumber());
            }
        }

        $jsonString = json_encode($jsonArray);

        return $jsonString;
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
     *
     * @param array $filters
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
        $filter = $this->getUser()->getAttribute($mode . '.filters', null, 'leave_list');
        $filter['calFromDate'] = set_datepicker_date_format($filter['calFromDate']);
        $filter['calToDate'] = set_datepicker_date_format($filter['calToDate']);

        return $filter;
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

}