<?php

/*
  // OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
  // all the essential functionalities required for any enterprise.
  // Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com

  // OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
  // the GNU General Public License as published by the Free Software Foundation; either
  // version 2 of the License, or (at your option) any later version.

  // OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
  // without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
  // See the GNU General Public License for more details.

  // You should have received a copy of the GNU General Public License along with this program;
  // if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
  // Boston, MA  02110-1301, USA
 */

/**
 * Form class for employee list in PIM
 */
class EmployeeSearchForm extends BaseForm {

    private $userType;
    private $loggedInUserId;
    private $companyService;
    private $jobService;

    public function configure() {

        $this->userType =  $this->getOption('userType');
        $this->loggedInUserId =  $this->getOption('loggedInUserId');
        
        $this->setWidgets(array(
            'employee_name' => new sfWidgetFormInputText(),            
            'id' => new sfWidgetFormInputText(),
            'search_mode' => new sfWidgetFormInputHidden(),
        ));

        $this->setDefault('search_mode', 'basic');

        /* Setting job titles */
        $this->_setJobTitleWidget();

        /* Setting sub divisions */
        $this->_setSubDivisionWidget();

        $this->_setEmployeeStatusWidget();

        if ($this->userType == 'Admin') {
            $this->setWidget('supervisor_name', new sfWidgetFormInputText());
            $this->setValidator('supervisor_name', new sfValidatorString(array('required' => false)));
        }

        $this->setValidator('employee_name', new sfValidatorString(array('required' => false)));
        $this->setValidator('id', new sfValidatorString(array('required' => false)));
        $this->setValidator('search_mode', new sfValidatorString(array('required' => false)));
        
        $this->widgetSchema->setNameFormat('empsearch[%s]');
    }

    public function getEmployeeListAsJson() {

        $jsonArray	=	array();
        $escapeCharSet = array(38, 39, 34, 60, 61,62, 63, 64, 58, 59, 94, 96);
        $employeeService = new EmployeeService();
        $employeeService->setEmployeeDao(new EmployeeDao());

        if ($this->userType == 'Admin') {
            $employeeList = $employeeService->getEmployeeList();
        } elseif ($this->userType == 'Supervisor') {

            $employeeList = $employeeService->getSupervisorEmployeeChain($this->loggedInUserId);

        }

        $employeeUnique = array();
        foreach($employeeList as $employee) {

            if(!isset($employeeUnique[$employee->getEmpNumber()])) {

                $name = $employee->getFirstName() . " " . $employee->getMiddleName();
                $name = trim(trim($name) . " " . $employee->getLastName());

                foreach($escapeCharSet as $char) {
                    $name = str_replace(chr($char), (chr(92) . chr($char)), $name);
                }

                $employeeUnique[$employee->getEmpNumber()] = $name;
                $jsonArray[] = array('name'=>$name, 'id' => $employee->getEmpNumber());
            }

        }

        $jsonString = json_encode($jsonArray);

        return $jsonString;

    }

    public function getSupervisorListAsJson() {

        $jsonArray	=	array();
        $escapeCharSet = array(38, 39, 34, 60, 61,62, 63, 64, 58, 59, 94, 96);
        $employeeService = new EmployeeService();
        $employeeService->setEmployeeDao(new EmployeeDao());

        if ($this->userType == 'Admin') {
            $employeeList = $employeeService->getEmployeeList();
        } elseif ($this->userType == 'Supervisor') {

            $employeeList = $employeeService->getSupervisorEmployeeChain($this->loggedInUserId);

        }

        $employeeUnique = array();
        foreach($employeeList as $employee) {

            if(!isset($employeeUnique[$employee->getEmpNumber()])) {

                $name = $employee->getFirstName() . " " . $employee->getMiddleName();
                $name = trim(trim($name) . " " . $employee->getLastName());

                foreach($escapeCharSet as $char) {
                    $name = str_replace(chr($char), (chr(92) . chr($char)), $name);
                }

                $employeeUnique[$employee->getEmpNumber()] = $name;
                $jsonArray[] = array('name'=>$name, 'id' => $employee->getEmpNumber());
            }

        }

        $jsonString = json_encode($jsonArray);

        return $jsonString;

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

    private function _setJobTitleWidget() {

        $jobService = $this->getJobService();
        $jobList = $jobService->getJobTitleList();
        $choices = array('0' => __('All'));

        foreach ($jobList as $job) {
            $choices[$job->getId()] = $job->getName();
        }

        $this->setWidget('job_title', new sfWidgetFormChoice(array('choices' => $choices)));
        $this->setValidator('job_title', new sfValidatorChoice(array('choices' => array_keys($choices))));

    }
    private function _setEmployeeStatusWidget() {

        $jobService = $this->getJobService();
        $statusList = $jobService->getEmployeeStatusList();
        $choices = array('0' => __('All'));

        foreach ($statusList as $status) {
            $choices[$status->getId()] = $status->getName();
        }

        $this->setWidget('employee_status', new sfWidgetFormChoice(array('choices' => $choices)));
        $this->setValidator('employee_status', new sfValidatorChoice(array('choices' => array_keys($choices))));

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
    
    private function _setSubDivisionWidget() {

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

        $this->setWidget('sub_unit', new sfWidgetFormChoice(array('choices' => $subUnitList)));
        $this->setValidator('sub_unit', new sfValidatorChoice(array('choices' => array_keys($subUnitList))));

    }

}

