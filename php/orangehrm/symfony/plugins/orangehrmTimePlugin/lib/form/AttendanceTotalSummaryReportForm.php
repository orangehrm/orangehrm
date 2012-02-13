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
class AttendanceTotalSummaryReportForm extends sfForm {

    private $jobService;
    private $companyStructureService;
    public $emoloyeeList;
    private $jobTitleService;
    private $empStatusService;

    public function getJobTitleService() {
        if (is_null($this->jobTitleService)) {
            $this->jobTitleService = new JobTitleService();
            $this->jobTitleService->setJobTitleDao(new JobTitleDao());
        }
        return $this->jobTitleService;
    }

    public function configure() {

        $this->setWidgets(array(
            'empName' => new sfWidgetFormInputText(array(), array('id' => 'employee_name')),
            'employeeId' => new sfWidgetFormInputHidden(),
            'fromDate' => new ohrmWidgetDatePickerNew(array(), array('id' => 'from_date')),
            'toDate' => new ohrmWidgetDatePickerNew(array(), array('id' => 'to_date'))
        ));

//        Setting job titles
        $this->_setJobTitleWidget();

//        Setting sub divisions
        $this->_setSubDivisionWidget();

        $this->_setEmployeeStatusWidget();

        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();

        $this->setValidator('empName', new sfValidatorString(array('required' => false)));
        $this->setValidator('employeeId', new sfValidatorInteger());
        $this->setValidator('fromDate', new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false),
                        array('invalid' => 'Date format should be ' . $inputDatePattern)));
        $this->setValidator('toDate', new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false),
                        array('invalid' => 'Date format should be ' . $inputDatePattern)));
        $this->widgetSchema->setNameFormat('attendanceTotalSummary[%s]');
    }

    public function getJobService() {
        if (is_null($this->jobService)) {
            $this->jobService = new JobService();
            $this->jobService->setJobDao(new JobDao());
        }
        return $this->jobService;
    }

    public function setJobService(JobService $jobService) {
        $this->jobService = $jobService;
    }

    private function _setJobTitleWidget() {

        $jobTitleList = $this->getJobTitleService()->getJobTitleList();
        $choices[0] = __('All');

        foreach ($jobTitleList as $job) {
            $choices[$job->getId()] = $job->getJobTitleName();
        }

        $this->setWidget('jobTitle', new sfWidgetFormChoice(array('choices' => $choices)));
        $this->setValidator('jobTitle', new sfValidatorChoice(array('choices' => array_keys($choices))));
    }

    public function getCompanyStructureService() {
        if (is_null($this->companyStructureService)) {
            $this->companyStructureService = new CompanyStructureService();
            $this->companyStructureService->setCompanyStructureDao(new CompanyStructureDao());
        }
        return $this->companyStructureService;
    }
   
    public function getEmploymentStatusService() {
        if (is_null($this->empStatusService)) {
            $this->empStatusService = new EmploymentStatusService();
            $this->empStatusService->setEmploymentStatusDao(new EmploymentStatusDao());
        }
        return $this->empStatusService;
    }

    public function setCompanyStructureService(CompanyStructureService $companyStructureService) {
        $this->companyStructureService = $companyStructureService;
    }

    private function _setSubDivisionWidget() {

        $subUnitList[0] = __("All");

        $treeObject = $this->getCompanyStructureService()->getSubunitTreeObject();

        $tree = $treeObject->fetchTree();

        foreach ($tree as $node) {
            if ($node->getId() != 1) {
                $subUnitList[$node->getId()] = str_repeat('&nbsp;&nbsp;', $node['level'] - 1) . $node['name'];
            }
        }
        $this->setWidget('subUnit', new sfWidgetFormChoice(array('choices' => $subUnitList)));
        $this->setValidator('subUnit', new sfValidatorChoice(array('choices' => array_keys($subUnitList))));
    }

    private function _setEmployeeStatusWidget() {

        $empStatusService = $this->getEmploymentStatusService();
        $statusList = $empStatusService->getEmploymentStatusList();
        $choices[0] = __('All');

        foreach ($statusList as $status) {
            $choices[$status->getId()] = $status->getName();
        }

        $this->setWidget('employeeStatus', new sfWidgetFormChoice(array('choices' => $choices)));
        $this->setValidator('employeeStatus', new sfValidatorChoice(array('choices' => array_keys($choices))));
    }

    public function getEmployeeListAsJson() {

        $jsonArray = array();
        $employeeService = new EmployeeService();
        $employeeService->setEmployeeDao(new EmployeeDao());

        $employeeUnique = array();
        foreach ($this->emoloyeeList as $employee) {

            if (!isset($employeeUnique[$employee->getEmpNumber()])) {

                $name = $employee->getFullName();
                $employeeUnique[$employee->getEmpNumber()] = $name;
                $jsonArray[] = array('name' => __($name), 'id' => $employee->getEmpNumber());
                
            }
        }

        $jsonString = json_encode($jsonArray);

        return $jsonString;
    }

}
