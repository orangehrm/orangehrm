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

    private $companyStructureService;
    private $jobService;
    private $jobTitleService;
    private $empStatusService;

    const WITHOUT_TERMINATED = 1;
    const WITH_TERMINATED = 2;
    const ONLY_TERMINATED = 3;

    public function getEmploymentStatusService() {
        if (is_null($this->empStatusService)) {
            $this->empStatusService = new EmploymentStatusService();
            $this->empStatusService->setEmploymentStatusDao(new EmploymentStatusDao());
        }
        return $this->empStatusService;
    }

    public function getJobTitleService() {
        if (is_null($this->jobTitleService)) {
            $this->jobTitleService = new JobTitleService();
            $this->jobTitleService->setJobTitleDao(new JobTitleDao());
        }
        return $this->jobTitleService;
    }

    public function configure() {

        $this->setWidgets(array(
            'employee_name' => new ohrmWidgetEmployeeNameAutoFill(array('loadingMethod'=>'ajax')),
            'id' => new sfWidgetFormInputText(),
        ));

        $this->_setEmployeeStatusWidget();

        $this->_setTerminatedEmployeeWidget();

        $this->setWidget('supervisor_name', new sfWidgetFormInputText());
        $this->setValidator('supervisor_name', new sfValidatorString(array('required' => false)));
        
        /* Setting job titles */
        $this->_setJobTitleWidget();

        /* Setting sub divisions */
        $this->_setSubunitWidget();
        $this->setWidget('isSubmitted', new sfWidgetFormInputHidden(array(), array()));
        $this->setValidator('isSubmitted', new sfValidatorString(array('required' => false)));
        $this->setValidator('employee_name', new ohrmValidatorEmployeeNameAutoFill());
        $this->setValidator('id', new sfValidatorString(array('required' => false)));

        $formExtension  =   PluginFormMergeManager::instance();
        $formExtension->mergeForms( $this,'viewEmployeeList','EmployeeSearchForm');

        
        $this->widgetSchema->setNameFormat('empsearch[%s]');
        $this->getWidgetSchema()->setLabels($this->getFormLabels());

    }

    public function getSupervisorListAsJson() {

        $jsonArray = array();
        $employeeService = new EmployeeService();
        $employeeService->setEmployeeDao(new EmployeeDao());

        $employeeList = $employeeService->getSupervisorList(true);

        foreach ($employeeList as $employee) {

            $name = $employee->getFirstName() . " " . $employee->getMiddleName();
            $name = trim(trim($name) . " " . $employee->getLastName());
            if ($employee->getTerminationId()) {
                $name = $name. ' ('.__('Past Employee') .')';
            }
            $jsonArray[] = array('name' => $name, 'id' => $employee->getEmpNumber());
        }

        $jsonString = json_encode($jsonArray);

        return $jsonString;
    }

    private function _setJobTitleWidget() {

        $jobTitleList = $this->getJobTitleService()->getJobTitleList();
        $choices = array('0' => __('All'));

        foreach ($jobTitleList as $job) {
            $choices[$job->getId()] = $job->getJobTitleName();
        }

        $this->setWidget('job_title', new sfWidgetFormChoice(array('choices' => $choices)));
        $this->setValidator('job_title', new sfValidatorChoice(array('choices' => array_keys($choices))));
    }

    private function _setEmployeeStatusWidget() {

        $empStatusService = $this->getEmploymentStatusService();
        $statusList = $empStatusService->getEmploymentStatusList();
        $choices = array('0' => __('All'));

        foreach ($statusList as $status) {
            $choices[$status->getId()] = $status->getName();
        }

        $this->setWidget('employee_status', new sfWidgetFormChoice(array('choices' => $choices)));
        $this->setValidator('employee_status', new sfValidatorChoice(array('choices' => array_keys($choices))));
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

    private function _setSubunitWidget() {

        $subUnitList = array(0 => __("All"));
        $treeObject = $this->getCompanyStructureService()->getSubunitTreeObject();

        $tree = $treeObject->fetchTree();

        foreach ($tree as $node) {
            if ($node->getId() != 1) {
                $subUnitList[$node->getId()] = str_repeat('&nbsp;&nbsp;', $node['level'] - 1) . $node['name'];
            }
        }
        $this->setWidget('sub_unit', new sfWidgetFormChoice(array('choices' => $subUnitList)));
        $this->setValidator('sub_unit', new sfValidatorChoice(array('choices' => array_keys($subUnitList))));
    }

    private function _setTerminatedEmployeeWidget() {
        $terminateSelection = array(self::WITHOUT_TERMINATED => __('Current Employees Only'), self::WITH_TERMINATED => __('Current and Past Employees'), self::ONLY_TERMINATED => __('Past Employees Only'));
        $this->setWidget('termination', new sfWidgetFormChoice(array('choices' => $terminateSelection)));
        $this->setValidator('termination', new sfValidatorChoice(array('choices' => array_keys($terminateSelection))));
    }

    /**
     *
     * @return array
     */
    protected function getFormLabels() {

        $labels = array(
            'employee_name' => __('Employee Name'),
            'id' => __('Id'),
            'employee_status' => __('Employment Status'),
            'termination' => __('Include'),
            'supervisor_name' => __('Supervisor Name'),
            'job_title' => __('Job Title'),
            'sub_unit' => __('Sub Unit')
        );
        return $labels;
    }

}

