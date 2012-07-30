<?php

/*
 *
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
 *
 */

class viewLeaveSummaryAction extends sfAction implements ohrmExportableAction {

    protected $employeeService;

    /**
     * @param sfForm $form
     */
    public function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }

    /**
     * Get EmployeeService
     * @return EmployeeService object
     */
    public function getEmployeeService() {
        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
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
     * Get instance of form used by this action.
     * Allows subclasses to override the form class used in the action.
     *
     * See: sfForm::__construct for parameter description
     */
    protected function getFormInstance($defaults = array(), $options = array(), $CSRFSecret = null) {
        return new LeaveSummaryForm($defaults, $options, $CSRFSecret);
    }

    public function execute($request) {
        $userDetails = $this->getLoggedInUserDetails();

        $this->templateMessage = $this->getUser()->getFlash('templateMessage', array('', ''));

        $searchParam = array();
        $searchParam['employeeId'] = (trim($request->getParameter("employeeId")) != "") ? trim($request->getParameter("employeeId")) : null;
        if (!is_null($searchParam['employeeId'])) {
            $terminationId = $this->getEmployeeService()->getEmployee($searchParam['employeeId'])->getTerminationId();
            if (!empty($terminationId)) {
                $searchParam['cmbWithTerminated'] = 'on';
            } else {
                $searchParam['cmbWithTerminated'] = 0;
            }
        }
        $params = array_merge($searchParam, $userDetails);

        $this->setForm($this->getFormInstance(array(), $params, true));
        $this->setLeaveSummaryRecordsLimit($request);
        $this->form->setRecordsLimitDefaultValue();

        if ($request->isMethod(sfRequest::POST)) {
            $this->searchFlag = 1;
            $this->form->bind($request->getParameter($this->form->getName()));
        }

        $this->form->setPager($request);

        $screenPermissions = $this->getContext()->get('screen_permissions');        
        $dataGroupPermissions = $this->getContext()->getUserRoleManager()->getDataGroupPermissions('leave_summary');
        
        $this->permissions = $dataGroupPermissions->andWith($screenPermissions);
        
        LeaveSummaryConfigurationFactory::setPermissions($this->permissions);

        $leaveSummaryService = new LeaveSummaryService();
        $leaveSummaryDao = new LeaveSummaryDao();
        $configurationFactory = new LeaveSummaryConfigurationFactory();

        $leaveSummaryService->setLeaveSummaryDao($leaveSummaryDao);

        $clues = $this->form->getSearchClues();
        $clues['loggedUserId'] = $userDetails['loggedUserId'];
        if (!is_null($searchParam['employeeId'])) {
            $terminationId = $this->getEmployeeService()->getEmployee($searchParam['employeeId'])->getTerminationId();
            $empName = $this->getEmployeeService()->getEmployee($searchParam['employeeId'])->getFirstAndLastNames();            
            if (!empty($empName)) {
                $clues['txtEmpName'] = $empName;
                $this->form->setDefault('txtEmpName', array('empName' => $empName, 'empId' => $searchParam['employeeId']));
            }
            
            if (!empty($terminationId)) {
                $clues['cmbWithTerminated'] = 'on';
                $this->form->setDefault('cmbWithTerminated', true);
            } else {
                $this->form->setDefault('cmbWithTerminated', false);
                $clues['cmbWithTerminated'] = 0;
            }
        }

        $noOfRecords = isset($clues['cmbRecordsCount']) ? (int) $clues['cmbRecordsCount'] : $this->form->recordsLimit;
        $pageNo = $request->getParameter('hdnAction') == 'search' ? 1 : $request->getParameter('pageNo', 1);
        $offset = ($pageNo - 1) * $noOfRecords;
        
        $this->leavePeriodId = $clues['cmbLeavePeriod'];
        $listData = $leaveSummaryService->searchLeaveSummary($clues, $offset, $noOfRecords);
        $totalRecordsCount = $leaveSummaryService->searchLeaveSummaryCount($clues);
        $this->form->recordsCount = $totalRecordsCount;
        
        $listComponentParameters = new ListCompnentParameterHolder();
        $listComponentParameters->populateByArray(array(
            'configurationFactory' => $configurationFactory,
            'listData' => $listData,
            'noOfRecords' => $noOfRecords,
            'totalRecordsCount' => $totalRecordsCount,
            'pageNumber' => $pageNo,
        ));
        $this->initializeListComponent($listComponentParameters);

        $this->initilizeDataRetriever($configurationFactory, $leaveSummaryService, 'searchLeaveSummary', array($this->form->getSearchClues(),
            0,
            $totalRecordsCount
        ));

        if (isset($this->form->recordsCount) && $this->form->recordsCount == 0 && isset($this->searchFlag) && $this->searchFlag == 1) {
            $this->templateMessage = array('NOTICE', __(TopLevelMessages::NO_RECORDS_FOUND));
        }

    }

    /**
     *
     * @param ListCompnentParameterHolder $parameters
     */
    protected function initializeListComponent(ListCompnentParameterHolder $parameters) {
        ohrmListComponent::setConfigurationFactory($parameters->getConfigurationFactory());
        ohrmListComponent::setActivePlugin('orangehrmCoreLeavePlugin');
        ohrmListComponent::setListData($parameters->getListData());
        ohrmListComponent::setItemsPerPage($parameters->getNoOfRecords());
        ohrmListComponent::setNumberOfRecords($parameters->getTotalRecordsCount());
        ohrmListComponent::$pageNumber = $parameters->getPageNumber();
    }

    /**
     * Returns Logged in user details
     * @return array
     */
    protected function getLoggedInUserDetails() {
        $userDetails = array();

        /* Value 0 is assigned for default admin */
        $userDetails['loggedUserId'] = (empty($_SESSION['empNumber'])) ? 0 : $_SESSION['empNumber'];
        $userDetails['empId'] = (empty($_SESSION['empID'])) ? 0 : $_SESSION['empID'];
        
        return $userDetails;
    }

    protected function setLeaveSummaryRecordsLimit($request) {

        $params = $request->getParameter('leaveSummary');

        if (isset($params['cmbRecordsCount'])) {
            $this->form->recordsLimit = $params['cmbRecordsCount'];
            $this->getUser()->setAttribute('leaveSummaryLimit', $this->form->recordsLimit);
        } elseif ($this->getUser()->hasAttribute('leaveSummaryLimit')) {
            $this->form->recordsLimit = $this->getUser()->getAttribute('leaveSummaryLimit');
        }
    }

    /**
     * Sets user details for testing purposes
     */
    public function setUserDetails($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function initilizeDataRetriever(ohrmListConfigurationFactory $configurationFactory, BaseService $dataRetrievalService, $dataRetrievalMethod, array $dataRetrievalParams) {
        $dataRetriever = new ExportDataRetriever();
        $dataRetriever->setConfigurationFactory($configurationFactory);
        $dataRetriever->setDataRetrievalService($dataRetrievalService);
        $dataRetriever->setDataRetrievalMethod($dataRetrievalMethod);
        $dataRetriever->setDataRetrievalParams($dataRetrievalParams);

        $this->getUser()->setAttribute('persistant.exportDataRetriever', $dataRetriever);
        $this->getUser()->setAttribute('persistant.exportFileName', 'leave-summary');
        $this->getUser()->setAttribute('persistant.exportDocumentTitle', 'Leave Summary');
        $this->getUser()->setAttribute('persistant.exportDocumentDescription', 'Generated at ' . date('Y-m-d H:i'));
    }

}

