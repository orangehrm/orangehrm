<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 *
 * Description of BasePefromanceSearchForm
 *
 * @author nadeera
 */

class BasePefromanceSearchForm extends sfForm {

    public $jobService;
    public $kpiService;
    public $companyStructureService;
    public $employeeService;
    public $performanceReviewService;
    public $user;
    public $templateMessage;
    public $reviewStatusFactory;
    public $reviewerReviewStatusFactory;
    private $workShiftService;
    
    
    /**
     *
     * @return ReviewerReviewStatusFactory 
     */
    public function getReviewerReviewStatusFactory() {
        if($this->reviewerReviewStatusFactory == null ){
            return new ReviewerReviewStatusFactory();
        } else {
            return $this->reviewerReviewStatusFactory;
        }        
    }

    /**
     *
     * @param ReviewerReviewStatusFactory $reviewerReviewStatusFactory 
     */
    public function setReviewerReviewStatusFactory($reviewerReviewStatusFactory) {
        $this->reviewerReviewStatusFactory = $reviewerReviewStatusFactory;
    }

        
    /**
     *
     * @return ReviewStatusFactory 
     */
    public function getReviewStatusFactory() {
        
        if($this->reviewStatusFactory == null ){
            return new ReviewStatusFactory();
        } else {
            return $this->reviewStatusFactory;
        }
    }

    /**
     *
     * @param ReviewStatusFactory $reviewStatusFactory 
     */
    public function setReviewStatusFactory($reviewStatusFactory) {
        $this->reviewStatusFactory = $reviewStatusFactory;
    }
    
    /**
     *
     * @return array 
     */
    public function getTemplateMessage() {
        return $this->templateMessage;
    }

    /**
     *
     * @param array $templateMessage 
     */
    public function setTemplateMessage($templateMessage) {
        $this->templateMessage = $templateMessage;
    }

    public function getWorkShiftService() {
        if (is_null($this->workShiftService)) {
            $this->workShiftService = new WorkShiftService();
            $this->workShiftService->setWorkShiftDao(new WorkShiftDao());
        }
        return $this->workShiftService;
    }
    
    public function setWorkShiftService($workShiftService) {
        $this->workShiftService = $workShiftService;
    }

    /**
     *
     * @return type 
     */
    public function getUser() {
        return $this->user;
    }

    /**
     *
     * @param type $user 
     */
    public function setUser($user) {
        $this->user = $user;
    }

    /**
     *
     * @return \PerformanceReviewService 
     */
    public function getPerformanceReviewService() {
        if ($this->performanceReviewService == null) {
            return new PerformanceReviewService();
        } else {
            return $this->performanceReviewService;
        }
    }

    /**
     *
     * @param \PerformanceReviewService $performanceReviewService 
     */
    public function setPerformanceReviewService($performanceReviewService) {
        $this->performanceReviewService = $performanceReviewService;
    }

    /**
     *
     * @return \EmployeeService 
     */
    public function getEmployeeService() {
        if ($this->employeeService == null) {
            $employeeService = new EmployeeService();
            $employeeService->setEmployeeDao(new EmployeeDao());
            return $employeeService;
        } else {
            return $this->employeeService;
        }
    }

    /**
     *
     * @param \EmployeeService $employeeService 
     */
    public function setEmployeeService($employeeService) {
        $this->employeeService = $employeeService;
    }

    /**
     *
     * @return type 
     */
    public function getCompanyStructureService() {
        if (is_null($this->companyStructureService)) {
            $this->companyStructureService = new CompanyStructureService();
            $this->companyStructureService->setCompanyStructureDao(new CompanyStructureDao());
        }
        return $this->companyStructureService;
    }

    /**
     *
     * @param CompanyStructureService $companyStructureService 
     */
    public function setCompanyStructureService(CompanyStructureService $companyStructureService) {
        $this->companyStructureService = $companyStructureService;
    }

    /**
     *
     * @return \KpiService 
     */
    public function getKpiService() {

        if ($this->kpiService == null) {
            return new KpiService();
        } else {
            return $this->kpiService;
        }
    }

    /**
     *
     * @param \KpiService $kpiService 
     */
    public function setKpiService($kpiService) {
        $this->kpiService = $kpiService;
    }

    /**
     *
     * @return type 
     */

    /**
     *
     * @return \JobService 
     */
    public function getJobService() {

        if ($this->jobService == null) {
            return new JobTitleService();
        } else {
            return $this->jobService;
        }
    }

    /**
     *
     * @param type $jobService 
     */
    public function setJobService($jobService) {
        $this->jobService = $jobService;
    }

    /**
     *
     * @return array 
     */
    public function getJobTitleListAsArray() {
        $jobTitles = array("" => "All");
        foreach ($this->getJobService()->getJobTitleList() as $job) {
            $jobTitles [$job->getId()] = $job->getJobTitleName();
        }
        return $jobTitles;
    }
    
    public function getPerformanceReviewStatusAsArray( $includeInactive = false){
        $reviewStatus = array();
        $reviewStatus [0] = 'All';
        if( $includeInactive ){
            $reviewStatus [ReviewStatusInactive::getInstance()->getStatusId()] = ReviewStatusInactive::getInstance()->getName() ;
        }
        $reviewStatus [ReviewStatusActivated::getInstance()->getStatusId()] = ReviewStatusActivated::getInstance()->getName() ;
        $reviewStatus [ReviewStatusApproved::getInstance()->getStatusId()] = ReviewStatusApproved::getInstance()->getName() ;
        $reviewStatus [ReviewStatusInProgress::getInstance()->getStatusId()] = ReviewStatusInProgress::getInstance()->getName() ;
        
         
        return $reviewStatus;
    }

    /**
     * 
     * @return array
     */
    public function getSubDivisionChoices() {

        if (is_null($this->subDivisionChoices)) {
            $this->subDivisionChoices = array(0 => __('All'));

            $treeObject = $this->getCompanyStructureService()->getSubunitTreeObject();

            $tree = $treeObject->fetchTree();

            foreach ($tree as $node) {
                if ($node->getId() != 1) {
                    $this->subDivisionChoices[$node->getId()] = str_repeat('&nbsp;&nbsp;', $node['level'] - 1) . $node['name'];
                }
            }
        }

        return $this->subDivisionChoices;
    }

    /**
     * 
     * @return array
     */
    public function getSubDivisionChoicesWithSelectOption() {

        if (is_null($this->subDivisionChoices)) {
            $this->subDivisionChoices = array(0 => __("--".__("Select")."--"));

            $treeObject = $this->getCompanyStructureService()->getSubunitTreeObject();

            $tree = $treeObject->fetchTree();

            foreach ($tree as $node) {
                if ($node->getId() != 1) {
                    $this->subDivisionChoices[$node->getId()] = str_repeat('&nbsp;&nbsp;', $node['level'] - 1) . $node['name'];
                }
            }
        }

        return $this->subDivisionChoices;
    }

    /**
     * 
     * @return array
     */
    public function getSubDivisionChoicesWithoutAllOption() {

        if (is_null($this->subDivisionChoices)) {
            $treeObject = $this->getCompanyStructureService()->getSubunitTreeObject();

            $tree = $treeObject->fetchTree();

            foreach ($tree as $node) {
                if ($node->getId() != 1) {
                    $this->subDivisionChoices[$node->getId()] = str_repeat('&nbsp;&nbsp;', $node['level'] - 1) . $node['name'];
                }
            }
        }
        return $this->subDivisionChoices;
    }

    /**
     *
     * @return array 
     */
    public function getJobTitleListAsArrayWithAllOption() {
        $jobTitles = array("" => "All");
        foreach ($this->getJobService()->getJobTitleList() as $job) {
            $jobTitles [$job->getId()] = $job->getJobTitleName();
        }
        return $jobTitles;
    }

    /**
     *
     * @return array 
     */
    public function getJobTitleListAsArrayWithSelectOption() {
        $jobTitles = array("" => "--".__("Select")."--");
        foreach ($this->getJobService()->getJobTitleList() as $job) {
            $jobTitles [$job->getId()] = $job->getJobTitleName();
        }
        return $jobTitles;
    }

    /**
     *
     * @return jsonString 
     */
    public function getEmployeeListAsJson( $excludeId = null ) {

        $jsonArray = array();
        $employeeService = $this->getEmployeeService();
        $workShiftService = $this->getWorkShiftService();
        $employeeList = $employeeService->getEmployeeList('empNumber', 'ASC', true);
        $workshiftList = $workShiftService->getWorkShiftEmployeeList();
        $workshiftListArray = array();
        foreach ($workshiftList as $workshift) {
            $workshiftListArray[$workshift->getEmpNumber()] = $workshift;
        }

        $employeeUnique = array();
        foreach ($employeeList as $employee) {
            $workShiftLength = 0;
            $employeeCountry = null;
            $terminationId = $employee->getTerminationId();
            if (!isset($employeeUnique[$employee->getEmpNumber()]) && empty($terminationId)) {
                $employeeWorkShift = $employeeService->getEmployeeWorkShift($employee->getEmpNumber());
                if ($employeeWorkShift != null) {
                    $workShiftLength = $employeeWorkShift->getWorkShift()->getHoursPerDay();
                } else
                    $workShiftLength = WorkShift :: DEFAULT_WORK_SHIFT_LENGTH;

                $operatinalCountry = $employee->getOperationalCountry();
                if ($employee->getOperationalCountry() instanceof OperationalCountry) {
                    $employeeCountry = $operatinalCountry->getId();
                }

                $name = $employee->getFullName();
                $employeeUnique[$employee->getEmpNumber()] = $name;

                if( $employee->getEmpNumber() != $excludeId) {
                    $jsonArray[] = array('name' => $name, 'id' => $employee->getEmpNumber(), 'workShift' => $workShiftLength, 'country' => $employeeCountry);
                }
            }
        }
        $jsonString = json_encode($jsonArray);

        return $jsonString;
    }

    /**
     *
     * @return json string 
     */
    public function getSupervisorListAsJson($subordinateId) {

        $jsonArray = array();
        $employeeService = new EmployeeService();
        $employeeService->setEmployeeDao(new EmployeeDao());

        $employeeList = $employeeService->getSupervisorIdListBySubordinateId($subordinateId);

        foreach ($employeeList as $employee) {

            $name = $employee->getFirstName() . " " . $employee->getMiddleName();
            $name = trim(trim($name) . " " . $employee->getLastName());

            $jsonArray[] = array('name' => $name, 'id' => $employee->getEmpNumber());
        }

        $jsonString = json_encode($jsonArray);

        return $jsonString;
    }

    /**
     *
     * @return json string 
     */
    public function getSubordinateListAsJson() {

        $jsonArray = array();
        $employeeService = new EmployeeService();
        $employeeService->setEmployeeDao(new EmployeeDao());

        $employeeList = $employeeService->getSubordinateListForEmployee();

        foreach ($employeeList as $employee) {

            $name = $employee->getFirstName() . " " . $employee->getMiddleName();
            $name = trim(trim($name) . " " . $employee->getLastName());

            $jsonArray[] = array('name' => $name, 'id' => $employee->getEmpNumber());
        }

        $jsonString = json_encode($jsonArray);

        return $jsonString;
    }

    /**
     *
     * @param type $value
     * @return null 
     */
    public function filterPostValues($value) {
        if (strlen(trim($value)) == 0) {
            return null;
        } else {
            return $value;
        }
    }

}