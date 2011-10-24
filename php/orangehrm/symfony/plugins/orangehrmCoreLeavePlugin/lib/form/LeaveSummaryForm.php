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

class LeaveSummaryForm extends sfForm {

    private $formWidgets = array();
    private $formValidators = array();
    public $leaveSummaryEditMode = false;
    public $pageNo = 1;
    public $pager;
    public $offset = 0;
    public $recordsCount;
    public $recordsLimit = 20;
    public $saveSuccess;
    public $userType;
    public $loggedUserId;
    public $subordinatesList;
    public $currentLeavePeriodId;
    private $leavePeriodService;
    private $searchParam = array();
    private $empId;
    private $employeeService;
    private $leaveTypeService;
    private $companyService;
    private $jobService;
    private $leaveSummaryService;
    private $leaveEntitlementService;

    public function configure() {

        $this->userType = $this->getOption('userType');
        $this->loggedUserId = $this->getOption('loggedUserId');
        $this->searchParam['employeeId'] = $this->getOption('employeeId');
        $this->searchParam['cmbWithTerminated'] = $this->getOption('cmbWithTerminated');
        $this->empId = $this->getOption('empId');

        $this->_setCurrentLeavePeriodId(); // This should be called before _setLeavePeriodWidgets()

        $formWidgets = array();
        $formValidators = array();

        /* Setting leave periods */
        $this->_setLeavePeriodWidgets();

        /* Setting leave types */
        $this->_setLeaveTypeWidgets();

        /* Setting records count */
        $this->_setRecordsCountWidgets();

        if ($this->userType == 'Admin' || $this->userType == 'Supervisor') {

            /* Setting locations */
            $this->_setLocationWidgets();

            /* Setting job titles */
            $this->_setJobTitleWidgets();

            /* Setting sub divisions */
            $this->_setSubDivisionWidgets();
            
            /* Setting terminated employee */
            $this->_setTerminatedEmployeeWidgets();

            /* Setting txtEmpName */
            $this->formWidgets['txtEmpName'] = new sfWidgetFormInput();
            $this->formValidators['txtEmpName'] = new sfValidatorString(array('required' => false));

            /* Setting cmbEmpId */
            $this->formWidgets['cmbEmpId'] = new sfWidgetFormInputHidden();
            $this->formValidators['cmbEmpId'] = new sfValidatorString(array('required' => false));
            
            /* Setting subjectedLeavePeriod */
            $this->formWidgets['hdnSubjectedLeavePeriod'] = new sfWidgetFormInputHidden();
            $this->formValidators['hdnSubjectedLeavePeriod'] = new sfValidatorString(array('required' => false));

            $employeeId = 0;
            $empName = "";
            if(!is_null($this->searchParam['employeeId'])) {
                $employeeId = $this->searchParam['employeeId'];
                $employeeService = $this->getEmployeeService();
                $employee = $employeeService->getEmployee($this->searchParam['employeeId']);
                $empName = $employee->getFirstName(). " " . $employee->getMiddleName() . " " . $employee->getLastName();
            }

            /* Setting default values */
            $this->setDefault('txtEmpName', $empName);
            $this->setDefault('cmbEmpId', $employeeId);
            $this->setDefault('cmbLeavePeriod', $this->currentLeavePeriodId);
            $this->setDefault('hdnSubjectedLeavePeriod', $this->_getLeavePeriod());
            
            if($this->searchParam['cmbWithTerminated'] == 'on') {
                $this->setDefault('cmbWithTerminated', true);
            }

        }

    	$this->setWidgets($this->formWidgets);
    	$this->setValidators($this->formValidators);
        $this->widgetSchema->setNameFormat('leaveSummary[%s]');

    }

    public function setEmployeeService(EmployeeService $employeeService) {
        $this->employeeService = $employeeService;
    }

    public function getEmployeeService() {
        if(is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }

    public function setRecordsLimitDefaultValue() {
        $this->setDefault('cmbRecordsCount', $this->recordsLimit);
    }

    private function _setLeavePeriodWidgets() {

        $leavePeriodService = $this->getLeavePeriodService();
        $leavePeriodList = $leavePeriodService->getLeavePeriodList();
        $choices = array();

        sfContext::getInstance()->getConfiguration()->loadHelpers('OrangeDate');

        foreach ($leavePeriodList as $leavePeriod) {

            $choices[$leavePeriod->getLeavePeriodId()] = set_datepicker_date_format($leavePeriod->getStartDate())
                                                         . " " .  __('to') . " "
                                                         . set_datepicker_date_format($leavePeriod->getEndDate());

        }

        if (empty($choices)) {
            $choices = array('0' => 'No Leave Periods');
        }

        $this->formWidgets['cmbLeavePeriod'] = new sfWidgetFormChoice(array('choices' => $choices));
        $this->formValidators['cmbLeavePeriod'] = new sfValidatorChoice(array('choices' => array_keys($choices)));

    }

    public function setLeaveTypeService(LeaveTypeService $leaveTypeService) {
        $this->leaveTypeService = $leaveTypeService;
    }

    public function getLeaveTypeService() {
        if(is_null($this->leaveTypeService)) {
            $this->leaveTypeService = new LeaveTypeService();
            $this->leaveTypeService->setLeaveTypeDao(new LeaveTypeDao());
        }
        return $this->leaveTypeService;
    }

    private function _setLeaveTypeWidgets() {

        $leaveTypeService = $this->getLeaveTypeService();
        $leaveTypeService->setLeaveTypeDao(new LeaveTypeDao());
        $leaveTypeList = $leaveTypeService->getLeaveTypeList();
        $choices = array('0' => __('All'));

        foreach ($leaveTypeList as $leaveType) {

            $choices[$leaveType->getLeaveTypeId()] = $leaveType->getLeaveTypeName();

        }

        $this->formWidgets['cmbLeaveType'] = new sfWidgetFormChoice(array('choices' => $choices));
        $this->formValidators['cmbLeaveType'] = new sfValidatorChoice(array('choices' => array_keys($choices)));

    }

    private function _setRecordsCountWidgets() {

        $choices = array('20' => 20, '50' => 50, '100' => 100, '200' => 200);

        $this->formWidgets['cmbRecordsCount'] = new sfWidgetFormChoice(array('choices' => $choices));
        $this->formValidators['cmbRecordsCount'] = new sfValidatorChoice(array('choices' => array_keys($choices)));

    }

    public function getCompanyService() {
        if(is_null($this->companyService)) {
            $this->companyService = new CompanyService();
            $this->companyService->setCompanyDao(new CompanyDao());
        }
        return $this->companyService;
    }

    public function setCompanyService(CompanyService $companyService) {
        $this->companyService = $companyService;
    }

    private function _setLocationWidgets() {

        $companyService = $this->getCompanyService();
        $locationList = $companyService->getCompanyLocation();
        $choices = array('0' => __('All'));

        foreach ($locationList as $location) {

            $choices[$location->getLocCode()] = $location->getLocName();

        }

        $this->formWidgets['cmbLocation'] = new sfWidgetFormChoice(array('choices' => $choices));
        $this->formValidators['cmbLocation'] = new sfValidatorChoice(array('choices' => array_keys($choices)));

    }

    private function _setSubDivisionWidgets() {

        $companyService = $this->getCompanyService();

        $subUnitList = array(0 => __("All"));
        $tree = $companyService->getSubDivisionTree();

        foreach($tree as $node) {

            // Add nodes, indenting correctly. Skip root node
            if ($node->getId() != 1) {
                if($node->depth == "") {
                    $node->depth = 1;
                }
                $indent = str_repeat('&nbsp;&nbsp;', $node->depth - 1);
                $subUnitList[$node->getId()] = $indent . $node->getTitle();
            }
        }

        $this->formWidgets['cmbSubDivision'] = new sfWidgetFormChoice(array('choices' => $subUnitList));
        $this->formValidators['cmbSubDivision'] = new sfValidatorChoice(array('choices' => array_keys($subUnitList)));

    }
    
    private function _setTerminatedEmployeeWidgets() {

        $this->formWidgets['cmbWithTerminated'] = new sfWidgetFormInputCheckbox(array('value_attribute_value' => 'on'));
        $this->formValidators['cmbWithTerminated'] = new sfValidatorString(array('required' => false));

    }

    public function getJobService() {
        if(is_null($this->jobService)) {
            $this->jobService = new JobService();
            $this->jobService->setJobDao(new JobDao());
        }
        return $this->jobService;
    }

    public function setJobService(JobService $jobService) {
        $this->jobService = $jobService;
    }

    private function _setJobTitleWidgets() {

        $jobService = $this->getJobService();
        $jobList = $jobService->getJobTitleList();
        $choices = array('0' => __('All'));

        foreach ($jobList as $job) {

            $choices[$job->getId()] = $job->getName();

        }

        $this->formWidgets['cmbJobTitle'] = new sfWidgetFormChoice(array('choices' => $choices));
        $this->formValidators['cmbJobTitle'] = new sfValidatorChoice(array('choices' => array_keys($choices)));

    }

    /**
     * Is leave type editable? 
     * Always returns true in core module. Can be overridden to
     * support none editable leave types
     */
    protected function isLeaveTypeEditable($leaveTypeId) {
        return true;
    }

    public function getEmployeeListAsJson() {

        $jsonArray	=	array();
        $employeeService = $this->getEmployeeService();

        if ($this->userType == 'Admin') {
            $employeeList = $employeeService->getEmployeeList();
        } elseif ($this->userType == 'Supervisor') {

            $employeeList = $employeeService->getSupervisorEmployeeChain($this->loggedUserId);
            $loggedInEmployee = $employeeService->getEmployee($this->loggedUserId);
            array_push($employeeList, $loggedInEmployee);

        } else {

            $employeeList = array();
        }
        $employeeUnique = array();
        foreach($employeeList as $employee) {
            if(!isset($employeeUnique[$employee->getEmpNumber()])) {
                $name = $employee->getFullName();

                $employeeUnique[$employee->getEmpNumber()] = $name;
                $jsonArray[] = array('name'=>$name, 'id' => $employee->getEmpNumber());
            }
        }

		$jsonString = json_encode($jsonArray);

        return $jsonString;

    }

    public function setPager(sfWebRequest $request) {

        if ($request->isMethod('post')) {

            if ($request->getParameter('hdnAction') == 'search') {
                $this->pageNo = 1;
            } elseif ($request->getParameter('pageNo')) {
                $this->pageNo = $request->getParameter('pageNo');
            }

        } else {
            $this->pageNo = 1;
        }


        $this->pager = new SimplePager('LeaveSummary', $this->recordsLimit);
        $this->pager->setPage($this->pageNo);
        $this->pager->setNumResults($this->recordsCount);
        $this->pager->init();
        $offset = $this->pager->getOffset();
        $offset = empty($offset)?0:$offset;
        $this->offset = $offset;

    }

    public function getLeaveSummaryRecordsCount() {

        $leaveSummaryService = $this->getLeaveSummaryService();
        $recordsCount = $leaveSummaryService->fetchRawLeaveSummaryRecordsCount($this->getSearchClues());

        return $recordsCount;

    }

    public function getLeaveSummaryService() {
        if(is_null($this->leaveSummaryService)) {
            $this->leaveSummaryService = new LeaveSummaryService();
            $this->leaveSummaryService->setLeaveSummaryDao(new LeaveSummaryDao());
        }
        return $this->leaveSummaryService;
    }

    public function setLeaveSummaryService(LeaveSummaryService $leaveSummaryService) {
        $this->leaveSummaryService = $leaveSummaryService;
    }


    public function setLeaveEntitlementService(LeaveEntitlementService $leaveEntitlementService) {
        $this->leaveEntitlementService = $leaveEntitlementService;
    }

    public function getLeaveEntitlementService() {
        if(is_null($this->leaveEntitlementService)) {
            $this->leaveEntitlementService = new LeaveEntitlementService();
            $this->leaveEntitlementService->setLeaveEntitlementDao(new LeaveEntitlementDao());
        }
        return $this->leaveEntitlementService;
    }
    
    public function saveEntitlements($request) {

        $hdnEmpId = $request->getParameter('hdnEmpId');
        $hdnLeaveTypeId = $request->getParameter('hdnLeaveTypeId');
        $hdnLeavePeriodId = $request->getParameter('hdnLeavePeriodId');
        $txtLeaveEntitled = $request->getParameter('txtLeaveEntitled');
        $count = count($txtLeaveEntitled);

        $leaveEntitlementService = $this->getLeaveEntitlementService();
        $leaveSummaryData = $request->getParameter('leaveSummary');

        for ($i=0; $i<$count; $i++) {

            $leavePeriodId = empty($hdnLeavePeriodId[$i]) ? $leaveSummaryData['hdnSubjectedLeavePeriod'] : $hdnLeavePeriodId[$i];

            $leaveEntitlementService->saveEmployeeLeaveEntitlement($hdnEmpId[$i], 
                $hdnLeaveTypeId[$i], $leavePeriodId, $txtLeaveEntitled[$i],
                true);
            
        }

        $this->saveSuccess = true;

    }

    public function getSearchClues() {

        if ($this->getValues()) {
            
            return $this->_adjustSearchClues($this->getValues());

        } else {

            $clues['cmbLeavePeriod'] = $this->currentLeavePeriodId;
            $clues['cmbEmpId'] = 0;
            if(!is_null($this->searchParam['employeeId'])) {
                $clues['cmbEmpId'] = $this->searchParam['employeeId'];
            }

            $clues['cmbLeaveType'] = 0;
            $clues['cmbLocation'] = 0;
            $clues['cmbSubDivision'] = 0;
            $clues['cmbJobTitle'] = 0;
            $clues['cmbWithTerminated'] = 0;

            return $this->_adjustSearchClues($clues);

        }

    }

    private function _adjustSearchClues($clues) {

        if ($this->userType == 'Admin') {

            $clues['userType'] = 'Admin';
            return $clues;

        } elseif ($this->userType == 'Supervisor') {

            $clues['userType'] = 'Supervisor';
            $clues['subordinates'] = $this->_getSubordinatesIds();
            return $clues;

        } else {

            $clues['userType'] = 'ESS';
            $clues['cmbEmpId'] = $this->loggedUserId;
            return $clues;

        }

    }
        
    private function _getSubordinatesList() {

        if (!empty($this->subordinatesList)) {

            return $this->subordinatesList;

        } else {

            $employeeService = new EmployeeService();
            $employeeService->setEmployeeDao(new EmployeeDao());
            $this->subordinatesList = $employeeService->getSupervisorEmployeeChain($this->loggedUserId);

            return $this->subordinatesList;

        }

    }

    private function _getSubordinatesIds() {

        $ids = array();

        foreach ($this->_getSubordinatesList() as $employee) {
        
            $ids[] = $employee->getEmpNumber();

        }

        $ids[] = $this->loggedUserId;

        return $ids;

    }

    private function _getLeavePeriod() {

        if ($this->getValue('cmbLeavePeriod')) {

            return $this->getValue('cmbLeavePeriod');
            
        } else {

            return $this->currentLeavePeriodId;

        }

    }

    private function _setCurrentLeavePeriodId() {

        $leavePeriodService = $this->getLeavePeriodService();
        $this->currentLeavePeriodId = (!$leavePeriodService->getCurrentLeavePeriod() instanceof LeavePeriod)?0:$leavePeriodService->getCurrentLeavePeriod()->getLeavePeriodId();
    }

	/**
     * Returns LeavePeriodService
	 * @return LeavePeriodService
	 */
    public function getLeavePeriodService() {
        if(is_null($this->leavePeriodService)) {
            $this->leavePeriodService = new LeavePeriodService();
            $this->leavePeriodService->setLeavePeriodDao(new LeavePeriodDao());
        }
        return $this->leavePeriodService;
    }

	/**
     * Sets LeavePeriodService
	 * @param LeavePeriodService $leavePeriodService
	 */
    public function setLeavePeriodService(LeavePeriodService $leavePeriodService) {
        $this->leavePeriodService = $leavePeriodService;
    }

}
