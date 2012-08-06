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

    private $searchParam = array();
    private $empId;
    private $employeeService;
    private $leaveTypeService;
    private $companyService;
    private $leaveSummaryService;
    private $leaveEntitlementService;
    private $companyStructureService;
    private $jobTitleService;

    protected $leavePeriodService;
    protected $locationChoices = null;
    protected $jobTitleChoices = null;
    protected $subDivisionChoices = null;
    protected $leaveTypeChoices = null;
    protected $leavePeriodChoices;
    protected $locationService;
    protected $employeeList = array();
    protected $employeeIdList = array();
    protected $hasAdministrativeFilters = false;

    public $leaveSummaryEditMode = false;
    public $pageNo = 1;
    public $pager;
    public $offset = 0;
    public $recordsCount = 0;
    public $recordsLimit = 20;
    public $saveSuccess;
    public $loggedUserId;
    public $userType;
    public $subordinatesList;
    public $currentLeavePeriodId;

    public function getJobTitleService() {
        if (!($this->jobTitleService instanceof JobTitleService)) {
            $this->jobTitleService = new JobTitleService();
        }
        return $this->jobTitleService;
    }
    
    public function setJobTitleService($jobTitleService) {
        $this->jobTitleService = $jobTitleService;
    }

    public function configure() {

        $this->loggedUserId = $this->getOption('loggedUserId');
        $this->userType = $this->getUserType();
        $this->searchParam['employeeId'] = $this->getOption('employeeId');
        $this->searchParam['cmbWithTerminated'] = $this->getOption('cmbWithTerminated');
        $this->empId = $this->getOption('empId');
        
        $this->setupEmployeeList();

        $this->setCurrentLeavePeriodId(); // This should be called before _setLeavePeriodWidgets()

        if ($this->hasAdministrativeFilters()) {

            $employeeId = 0;
            $empName = "";
            if (!is_null($this->searchParam['employeeId'])) {
                $employeeId = $this->searchParam['employeeId'];
                $employeeService = $this->getEmployeeService();
                $employee = $employeeService->getEmployee($this->searchParam['employeeId']);
                $empName = $employee->getFullName();
            }

            /* Setting default values */
            $this->setDefault('txtEmpName', array('empName' => $empName, 'empId' => $employeeId));
            $this->setDefault('cmbLeavePeriod', $this->currentLeavePeriodId);
            $this->setDefault('hdnSubjectedLeavePeriod', $this->_getLeavePeriod());

            if ($this->searchParam['cmbWithTerminated'] == 'on') {
                $this->setDefault('cmbWithTerminated', true);
            }
        }

        $this->setWidgets($this->getFormWidgets());
        $this->setValidators($this->getFormValidators());

        $this->getWidgetSchema()->setNameFormat('leaveSummary[%s]');
        $this->getWidgetSchema()->setLabels($this->getFormLabels());

        sfWidgetFormSchemaFormatterBreakTags::setNoOfColumns(2);
        $this->getWidgetSchema()->setFormFormatterName('BreakTags');
    }

    public function setEmployeeService(EmployeeService $employeeService) {
        $this->employeeService = $employeeService;
    }

    public function getEmployeeService() {
        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }

    public function setRecordsLimitDefaultValue() {
        $this->setDefault('cmbRecordsCount', $this->recordsLimit);
    }

    private function getLeavePeriodChoices() {

        if (is_null($this->leavePeriodChoices)) {
            $leavePeriodList = $this->getLeavePeriodService()->getLeavePeriodList();

            $this->leavePeriodChoices = array();

            sfContext::getInstance()->getConfiguration()->loadHelpers('OrangeDate');

            foreach ($leavePeriodList as $leavePeriod) {
                $this->leavePeriodChoices[$leavePeriod->getLeavePeriodId()] = set_datepicker_date_format($leavePeriod->getStartDate())
                        . ' ' . __('to') . ' '
                        . set_datepicker_date_format($leavePeriod->getEndDate());
            }

            if (empty($this->leavePeriodChoices)) {
                $this->leavePeriodChoices = array('0' => 'No Leave Periods');
            }
        }

        return $this->leavePeriodChoices;
    }

    public function setLeaveTypeService(LeaveTypeService $leaveTypeService) {
        $this->leaveTypeService = $leaveTypeService;
    }

    public function getLeaveTypeService() {
        if (!($this->leaveTypeService instanceof LeaveTypeService)) {
            $this->leaveTypeService = new LeaveTypeService();
        }
        return $this->leaveTypeService;
    }

    /**
     * 
     * @return array
     */
    protected function getLeaveTypeChoices() {
        if (!($this->leaveTypeChoices)) {
            $leaveTypeList = $this->getLeaveTypeService()->getLeaveTypeList();

            $this->leaveTypeChoices = array('0' => __('All'));

            foreach ($leaveTypeList as $leaveType) {
                $this->leaveTypeChoices[$leaveType->getLeaveTypeId()] = $leaveType->getLeaveTypeName();
            }
        }

        return $this->leaveTypeChoices;
    }

    /**
     *
     * @return array
     */
    private function getRecordsPerPageChoices() {
        return array('20' => 20, '50' => 50, '100' => 100, '200' => 200);
    }

    public function getLocationService() {
        if (!($this->locationService instanceof LocationService)) {
            $this->locationService = new LocationService();
        }
        return $this->locationService;
    }

    public function setCompanyService(CompanyService $companyService) {
        $this->companyService = $companyService;
    }

    protected function getLocationChoices() {

        if (is_null($this->locationChoices)) {
            $locationList = $this->getLocationService()->getLocationList();

            $this->locationChoices = array('0' => __('All'));

            foreach ($locationList as $location) {
                $this->locationChoices[$location->getId()] = $location->getName();
            }
        }

        return $this->locationChoices;
    }

    public function getCompanyStructureService() {
        if (is_null($this->companyStructureService)) {
            $this->companyStructureService = new CompanyStructureService();
            $this->companyStructureService->setCompanyStructureDao(new CompanyStructureDao());
        }
        return $this->companyStructureService;
    }

    public function setCompanyStructureService(CompanyStructureService $companyStructureService) {
        $this->companyStructureService = $companyStructureService;
    }

    /**
     *
     * @return array
     */
    private function getJobTitleChoices() {

        if (is_null($this->jobTitleChoices)) {
            $jobTitleList = $this->getJobTitleService()->getJobTitleList();

            $this->jobTitleChoices = array('0' => __('All'));

            foreach ($jobTitleList as $jobTitle) {
                $this->jobTitleChoices[$jobTitle->getId()] = $jobTitle->getJobTitleName();
            }
        }

        return $this->jobTitleChoices;
    }

    /**
     * Is leave type editable? 
     * Always returns true in core module. Can be overridden to
     * support none editable leave types
     */
    protected function isLeaveTypeEditable($leaveTypeId) {
        return true;
    }

    protected function setupEmployeeList() {

        $employeeList = array();
        $idList = array();
        
        $userRoleManager = UserRoleManagerFactory::getUserRoleManager();
        $properties = array("empNumber","firstName", "middleName", "lastName", 'termination_id');
        
        $requiredPermissions = array(
            BasicUserRoleManager::PERMISSION_TYPE_DATA_GROUP => array(
                'leave_summary' => 
                    new ResourcePermission(true, false, false, false)));
        
        $employeeList = $userRoleManager->getAccessibleEntityProperties('Employee', 
                $properties, null, null, array(), array(), $requiredPermissions);
        
        $employeeIdList = $userRoleManager->getAccessibleEntityIds('Employee', 
                null, null, array(), array(), $requiredPermissions);

        $this->hasAdministrativeFilters = count($employeeList) > 0;
        
        $hasSelf = false;
        if(in_array($this->loggedUserId, $employeeIdList)) {
            $hasSelf = true;
        }
        
        if (!$hasSelf) {

            $employeeService = $this->getEmployeeService();
            $loggedInEmployee = $employeeService->getEmployee($this->loggedUserId);
            if ($loggedInEmployee instanceof Employee) {
                $employeeIdList[] = $this->loggedUserId;
                $empProperties = array('empNumber' => $loggedInEmployee->getEmpNumber(), 'firstName' => $loggedInEmployee->getFirstName(), 
                	'middleName' => $loggedInEmployee->getMiddleName(), 'lastName' => $loggedInEmployee->getLastName());
                $employeeList[$loggedInEmployee->getEmpNumber()] = $empProperties;
            }
        }
        
        $this->employeeList = $employeeList;
        $this->employeeIdList = $employeeIdList;

        return $employeeList;
        
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
        $offset = empty($offset) ? 0 : $offset;
        $this->offset = $offset;
    }

    public function getLeaveSummaryRecordsCount() {

        $leaveSummaryService = $this->getLeaveSummaryService();
        $recordsCount = $leaveSummaryService->searchLeaveSummaryCount($this->getSearchClues());

        return $recordsCount;
    }

    public function getLeaveSummaryService() {
        if (is_null($this->leaveSummaryService)) {
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
        if (is_null($this->leaveEntitlementService)) {
            $this->leaveEntitlementService = new LeaveEntitlementService();
            $this->leaveEntitlementService->setLeaveEntitlementDao(new LeaveEntitlementDao());
        }
        return $this->leaveEntitlementService;
    }

    public function getSearchClues() {

        if ($this->getValues()) {
            return $this->adjustSearchClues($this->getValues());
        } else {

            $clues['cmbLeavePeriod'] = $this->currentLeavePeriodId;
            $clues['cmbEmpId'] = 0;
            if (!is_null($this->searchParam['employeeId'])) {
                $clues['cmbEmpId'] = $this->searchParam['employeeId'];
            }

            $clues['cmbLeaveType'] = 0;
            $clues['cmbLocation'] = 0;
            $clues['cmbSubDivision'] = 0;
            $clues['cmbJobTitle'] = 0;
            $clues['cmbWithTerminated'] = 0;
            $clues['userType'] = ""; // TODO: Was added because of PHP warning in LeaveSummaryDao. Should be able to refactor fetchRawLeaveSummaryRecords() and fetchRawLeaveSummaryRecordsCount()

            return $this->adjustSearchClues($clues);
        }
    }

    protected function adjustSearchClues($clues) {
        
        if (isset($clues['txtEmpName']) && is_array($clues['txtEmpName'])) {
            $a = $clues['txtEmpName'];
            $clues['txtEmpName'] = $a['empName'];
            $clues['cmbEmpId'] = $a['empId'];
        }

        $clues['emp_numbers'] = $this->employeeIdList;
        $clues['userType'] = ""; // TODO: Was added because of PHP warning in LeaveSummaryDao. Should be able to refactor fetchRawLeaveSummaryRecords() and fetchRawLeaveSummaryRecordsCount()
        
        return $clues;        
    }

    private function _getSubordinatesList() {

        if (!empty($this->subordinatesList)) {

            return $this->subordinatesList;
        } else {

            $employeeService = new EmployeeService();
            $employeeService->setEmployeeDao(new EmployeeDao());
            $this->subordinatesList = $employeeService->getSubordinateList($this->loggedUserId, true);

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

    protected final function setCurrentLeavePeriodId() {

        $leavePeriodService = $this->getLeavePeriodService();
        $this->currentLeavePeriodId = (!$leavePeriodService->getCurrentLeavePeriod() instanceof LeavePeriod) ? 0 : $leavePeriodService->getCurrentLeavePeriod()->getLeavePeriodId();
    }

    /**
     * Returns LeavePeriodService
     * @return LeavePeriodService
     */
    public function getLeavePeriodService() {
        if (!($this->leavePeriodService instanceof LeavePeriodService)) {
            $this->leavePeriodService = new LeavePeriodService();
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

    /**
     *
     * @return array
     */
    protected function getFormLabels() {
        $labels = array(
            'cmbLeavePeriod' => __('Leave Period'),
            'cmbLeaveType' => __('Leave Type'),
            'cmbRecordsCount' => __('Records Per Page'),
            'cmbLocation' => __('Location'),
            'cmbJobTitle' => __('Job Title'),
            'cmbSubDivision' => __('Sub Unit'),
            'cmbWithTerminated' => __('Include Past Employees'),
            'txtEmpName' => __('Employee'),
        );

        return $labels;
    }

    /**
     *
     * @return array 
     */
    protected function getFormWidgets() {
        
        $widgets = array();

        $widgets['cmbLeavePeriod'] = new sfWidgetFormChoice(array('choices' => $this->getLeavePeriodChoices()));
        $widgets['hdnSubjectedLeavePeriod'] = new sfWidgetFormInputHidden();
        $widgets['cmbLeaveType'] = new sfWidgetFormChoice(array('choices' => $this->getLeaveTypeChoices()));

        if ($this->hasAdministrativeFilters()) {
            $widgets['txtEmpName'] = new ohrmWidgetEmployeeNameAutoFill(array('employeeList' => $this->employeeList));
            $widgets['cmbJobTitle'] = new sfWidgetFormChoice(array('choices' => $this->getJobTitleChoices()));
            $widgets['cmbLocation'] = new sfWidgetFormChoice(array('choices' => $this->getLocationChoices()));
            $widgets['cmbSubDivision'] = new ohrmWidgetSubDivisionList();
        }

        $widgets['cmbRecordsCount'] = new sfWidgetFormChoice(array('choices' => $this->getRecordsPerPageChoices()));

        if ($this->hasAdministrativeFilters()) {
            $widgets['cmbWithTerminated'] = new sfWidgetFormInputCheckbox(array('value_attribute_value' => 'on'));
        }

        return $widgets;
        
    }

    /**
     *
     * @return array 
     */
    protected function getFormValidators() {
        $validators = array();

        $validators['cmbLeavePeriod'] = new sfValidatorChoice(array('choices' => array_keys($this->getLeavePeriodChoices())));
        $validators['cmbLeaveType'] = new sfValidatorChoice(array('choices' => array_keys($this->getLeaveTypeChoices())));

        if ($this->hasAdministrativeFilters()) {
            $validators['cmbLocation'] = new sfValidatorChoice(array('choices' => array_keys($this->getLocationChoices())));
            $validators['cmbJobTitle'] = new sfValidatorChoice(array('choices' => array_keys($this->getJobTitleChoices())));
            $validators['cmbSubDivision'] = new sfValidatorString(array('required' => false)); // TODO: Improve this validator
            $validators['cmbWithTerminated'] = new sfValidatorString(array('required' => false));
            $validators['txtEmpName'] = new ohrmValidatorEmployeeNameAutoFill();
        }

        $validators['cmbRecordsCount'] = new sfValidatorChoice(array('choices' => array_keys($this->getRecordsPerPageChoices())));
        $validators['hdnSubjectedLeavePeriod'] = new sfValidatorString(array('required' => false));

        return $validators;
    }

    /**
     *
     * @return bool
     */
    protected function hasAdministrativeFilters() {
        return ($this->hasAdministrativeFilters);
    }
    
    /**
     * Returns Logged in user type
     * @return string
     * @todo Refactor this method to use auth classes instead of directly accessing the session
     */
    protected function getUserType() {
        $userType = 'ESS';
        
        if ($_SESSION['isSupervisor']) {
            $userType = 'Supervisor';
        }

        if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 'Yes') {
            $userType = 'Admin';
        }
        
        return $userType;
    }

}
