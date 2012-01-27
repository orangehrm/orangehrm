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
 * Description of viewMyLeaveListAction
 *
 */
class viewMyLeaveListAction extends sfAction {

    private $leaveRequestService;
    private $leavePeriodService;
    private $employeeService;

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

    public function execute($request) {

        sfContext::getInstance()->getConfiguration()->loadHelpers('Url');

        $this->setTemplate('viewLeaveList');

        $id = (int) $request->getParameter('id');
        $mode = empty($id) ? LeaveListForm::MODE_MY_LEAVE_LIST : LeaveListForm::MODE_MY_LEAVE_DETAILED_LIST;

        if ($this->_isRequestFromLeaveSummary($request)) {
            $this->_setFilters($mode, $request->getGetParameters());
        }

        if ($request->isMethod('post')) {
            $this->_setFilters($mode, $request->getPostParameters());
        }

        // Reset filters if requested to
        if ($request->hasParameter('reset')) {
            $this->_setFilters($mode, array());
            $this->isMyLeaveListDefaultView = true;
        } else {
            $this->isMyLeaveListDefaultView = false;
        }

        $filters = $this->_getFilters($mode);

        $isPaging = $request->getParameter('pageNo');

        $pageNumber = $isPaging;
        if (!is_null($this->getUser()->getAttribute('myLeaveListPageNumber')) && !($pageNumber >= 1)) {
            $pageNumber = $this->getUser()->getAttribute('myLeaveListPageNumber');
        }
        $this->getUser()->setAttribute('myLeaveListPageNumber', $pageNumber);

        $noOfRecordsPerPage = sfConfig::get('app_items_per_page');

        $leavePeriodId = $this->_getFilterValue($filters, 'leavePeriodId', null);
        $fromDate = $this->_getFilterValue($filters, 'calFromDate', null);
        $toDate = $this->_getFilterValue($filters, 'calToDate', null);
        $statuses = $this->_getFilterValue($filters, 'chkSearchFilter', array());

        $message = $this->getUser()->getFlash('message', '');
        $messageType = $this->getUser()->getFlash('messageType', '');

        $leaveTypeId = trim($this->_getFilterValue($filters, 'leaveTypeId'));
        $statuses = (trim($this->_getFilterValue($filters, 'status') != "")) ? array($this->_getFilterValue($filters, 'status')) : $statuses;

        $leavePeriod = $this->getLeavePeriodService()->getCurrentLeavePeriod();
        if (trim($leavePeriodId) != "") {
            $leavePeriod = $this->getLeavePeriodService()->readLeavePeriod($leavePeriodId);
        } else {
            $leavePeriodId = $leavePeriod->getLeavePeriodId();
        }
        $employeeService = $this->getEmployeeService();
        $empNumber = Auth::instance()->getEmployeeNumber();
        $employee = $employeeService->getEmployee($empNumber);

        $recordCount = 0;

        if ($mode == LeaveListForm::MODE_MY_LEAVE_LIST) {

            $dateRange = new DateRange($fromDate, $toDate);

            $searchParams = new ParameterObject(array(
                        'dateRange' => $dateRange,
                        'statuses' => $statuses,
                        'leavePeriod' => $leavePeriodId,
                        'employeeFilter' => $empNumber,
                        'noOfRecordsPerPage' => $noOfRecordsPerPage
                    ));

            if (!empty($leaveTypeId)) {
                $searchParams->setParameter('leaveType', $leaveTypeId);
            }

            $result = $this->getLeaveRequestService()->searchLeaveRequests($searchParams, $pageNumber, false, true);
            $list = $result['list'];
            $recordCount = $result['meta']['record_count'];

            $this->page = $page;
            $this->recordCount = $recordCount;

            if ($recordCount == 0 && $request->isMethod("post")) {
                $message = 'No Records Found';
                $messageType = 'notice';
            }
        } else {

            $mode = LeaveListForm::MODE_MY_LEAVE_DETAILED_LIST;
            $employee = $this->getLeaveRequestService()->fetchLeaveRequest($id)->getEmployee();
            $list = $this->getLeaveRequestService()->searchLeave($id);
            $this->leaveRequestId = $id;
        }

        $leaveListForm = new LeaveListForm($mode, $leavePeriod, $employee, $filters);

        $leaveListForm->setList($list);
        $this->form = $leaveListForm;
        $this->mode = $mode;
        $this->message = $message;
        $this->messageType = $messageType;
        $this->baseUrl = 'leave/viewMyLeaveList';
        $this->pagingUrl = '@my_leave_request_list';

        if ($mode === LeaveListForm::MODE_MY_LEAVE_LIST) {
            LeaveListConfigurationFactory::setListMode(LeaveListForm::MODE_MY_LEAVE_LIST);
            $configurationFactory = new LeaveListConfigurationFactory();

            $configurationFactory->getHeader(0)->setElementProperty(array(
                'labelGetter' => array('getLeaveDateRange'),
                'placeholderGetters' => array('id' => 'getLeaveRequestId'),
                'urlPattern' => public_path('index.php/leave/viewMyLeaveList/id/{id}'),
            ));

            $configurationFactory->getHeader(4)->setElementProperty(array(
                'labelGetter' => array('getStatus'),
                'placeholderGetters' => array('id' => 'getLeaveRequestId'),
                'urlPattern' => public_path('index.php/leave/viewMyLeaveList/id/{id}'),
            ));

            $methodName = 'searchLeaveRequests';
            $params = array($searchParams, $page, 'list');
        } elseif ($mode === LeaveListForm::MODE_MY_LEAVE_DETAILED_LIST) {
            DetailedLeaveListConfigurationFactory::setListMode(LeaveListForm::MODE_MY_LEAVE_DETAILED_LIST);
            $configurationFactory = new DetailedLeaveListConfigurationFactory();
            $methodName = 'searchLeave';
            $params = array($id);
        } else {
            // TODO: Warn
        }

        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setListData($list);

        ohrmListComponent::setPageNumber($pageNumber);
        ohrmListComponent::setItemsPerPage($noOfRecordsPerPage);
        ohrmListComponent::setNumberOfRecords($recordCount);

        $this->initilizeDataRetriever($configurationFactory, $this->getLeaveRequestService(), $methodName, $params, $employee);
    }

    public function initilizeDataRetriever(ohrmListConfigurationFactory $configurationFactory, BaseService $dataRetrievalService, $dataRetrievalMethod, array $dataRetrievalParams, $employee) {
        $dataRetriever = new ExportDataRetriever();
        $dataRetriever->setConfigurationFactory($configurationFactory);
        $dataRetriever->setDataRetrievalService($dataRetrievalService);
        $dataRetriever->setDataRetrievalMethod($dataRetrievalMethod);
        $dataRetriever->setDataRetrievalParams($dataRetrievalParams);

        $this->getUser()->setAttribute('persistant.exportDataRetriever', $dataRetriever);
        $this->getUser()->setAttribute('persistant.exportFileName', 'my-leave-list');
        $this->getUser()->setAttribute('persistant.exportDocumentTitle', 'Leave List');
        $this->getUser()->setAttribute('persistant.exportDocumentDescription', 'of ' . $employee->getFullName());
    }

    protected function _isRequestFromLeaveSummary($request) {

        $txtEmpID = $request->getGetParameter('txtEmpID');

        if (!empty($txtEmpID)) {
            return true;
        }

        return false;
    }

    /**
     *
     * @param array $filters
     * @return unknown_type
     */
    protected function _setFilters($mode, array $filters) {
        return $this->getUser()->setAttribute($mode . '.filters', $filters, 'leave_module');
    }

    /**
     *
     * @return unknown_type
     */
    protected function _getFilters($mode) {
        return $this->getUser()->getAttribute($mode . '.filters', null, 'leave_module');
    }

    protected function _getFilterValue($filters, $parameter, $default = null) {
        $value = $default;
        if (isset($filters[$parameter])) {
            $value = $filters[$parameter];
        }

        return $value;
    }

}

