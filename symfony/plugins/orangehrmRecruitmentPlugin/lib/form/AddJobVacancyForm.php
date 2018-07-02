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
 *
 */
class AddJobVacancyForm extends BaseForm {

    private $vacancyService;
    private $vacancyId;
    private $jobTitleService;
    private $employeeService;

    /**
     * This method will return an instance of Employee Service.
     * @returns EmployeeService
     */
    public function getEmployeeService() {
        if (!$this->employeeService instanceof EmployeeService) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }


    /**
     * This method will create an instance of Employee Service.
     * @param EmployeeService $employeeService
     */
    public function setEmployeeService(EmployeeService $employeeService) {
        $this->employeeService = $employeeService;
    }

    public function getJobTitleService() {
        if (is_null($this->jobTitleService)) {
            $this->jobTitleService = new JobTitleService();
            $this->jobTitleService->setJobTitleDao(new JobTitleDao());
        }
        return $this->jobTitleService;
    }

    /**
     * Get VacancyService
     * @returns VacncyService
     */
    public function getVacancyService() {
        if (is_null($this->vacancyService)) {
            $this->vacancyService = new VacancyService();
            $this->vacancyService->setVacancyDao(new VacancyDao());
        }
        return $this->vacancyService;
    }

    /**
     * Set VacancyService
     * @param VacancyService $vacancyService
     */
    public function setVacancyService(VacancyService $vacancyService) {
        $this->vacancyService = $vacancyService;
    }

    /**
     *
     */
    public function configure() {

        $jobTitleList = $this->getJobTitleList();

        $this->vacancyId = $this->getOption('vacancyId');
        if (isset($this->vacancyId)) {
            $vacancy = $this->getVacancyDetails($this->vacancyId);
        }
        
        $vacancyPermissions = $this->getOption('vacancyPermissions');

        //creating widgets
        $widgets = array(
            'jobTitle' => new sfWidgetFormSelect(array('choices' => $jobTitleList)),
            'name' => new sfWidgetFormInputText(),
            'hiringManager' => new sfWidgetFormInputText(),
            'hiringManagerId' => new sfWidgetFormInputHidden(),
            'noOfPositions' => new sfWidgetFormInputText(),
            'description' => new sfWidgetFormTextArea(),
            'status' => new sfWidgetFormInputCheckbox(array(), array('value' => 'on')),
            'publishedInFeed' => new sfWidgetFormInputCheckbox(array(), array('value' => 'on')),
        );

        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();

        //Setting validators
        $validators = array(
            'jobTitle' => new sfValidatorString(array('required' => true)),
            'name' => new sfValidatorString(array('required' => true)),
            'hiringManager' => new sfValidatorString(array('required' => true)),
            'hiringManagerId' => new sfValidatorInteger(array('required' => true, 'min' => 0)),
            'noOfPositions' => new sfValidatorInteger(array('required' => false, 'min' => 0)),
            'description' => new sfValidatorString(array('required' => false, 'max_length' => 41000)),
            'status' => new sfValidatorString(array('required' => false)),
            'publishedInFeed' => new sfValidatorString(array('required' => false)),
        );
        
        // check create and update permissions
        if((!$vacancyPermissions->canCreate() && empty($this->vacancyId)) || 
                (!$vacancyPermissions->canUpdate() && $this->vacancyId > 0)){

            foreach ($widgets as $widget){
                $widget->setAttribute('disabled', 'disabled');
            }
        }
        
        $this->setWidgets($widgets);
        $this->setValidators($validators);
               
        $this->widgetSchema->setNameFormat('addJobVacancy[%s]');
        if (isset($vacancy) && $vacancy != null) {
            $this->setDefault('jobTitle', $vacancy->getJobTitleCode());
            $this->setDefault('name', $vacancy->getName());
            $this->setDefault('hiringManager', $vacancy->getHiringManagerFullName());
            $this->setDefault('noOfPositions', $vacancy->getNoOfPositions());
            $this->setDefault('description', $vacancy->getDescription());
            if ($vacancy->getStatus() == JobVacancy::ACTIVE) {
                $this->setDefault('status', $vacancy->getStatus());
            }
            if ($vacancy->getPublishedInFeed() == JobVacancy::PUBLISHED) {
                $this->setDefault('publishedInFeed', $vacancy->getStatus());
            }
        } else {
            $this->setDefault('status', JobVacancy::ACTIVE);
            $this->setDefault('publishedInFeed', JobVacancy::PUBLISHED);
        }
    }

    /**
     *
     */
    public function save() {

        if (empty($this->vacancyId)) {
            $jobVacancy = new JobVacancy();
            $jobVacancy->definedTime = date('Y-m-d H:i:s');
            $jobVacancy->updatedTime = date('Y-m-d H:i:s');
        } else {
            $jobVacancy = $this->getVacancyService()->getVacancyById($this->vacancyId);
            $jobVacancy->updatedTime = date('Y-m-d H:i:s');
        }
        $jobVacancy->jobTitleCode = $this->getValue('jobTitle');
        $jobVacancy->name = $this->getValue('name');
        $jobVacancy->setEmployee($this->getEmployeeService()->getEmployee($this->getValue('hiringManagerId')));
        $jobVacancy->noOfPositions = $this->getValue('noOfPositions');
        $jobVacancy->description = $this->getValue('description');
        $jobVacancy->status = JobVacancy::CLOSED;
        $status = $this->getValue('status');
        if (!empty($status)) {
            $jobVacancy->status = JobVacancy::ACTIVE;
        }

        $publishInFeed = $this->getValue('publishedInFeed');
        $jobVacancy->publishedInFeed = JobVacancy::NOT_PUBLISHED;
        if (!empty($publishInFeed)) {
            $jobVacancy->publishedInFeed = JobVacancy::PUBLISHED;
        }

        $this->getVacancyService()->saveJobVacancy($jobVacancy);

        return $jobVacancy->getId();
    }

    /**
     * Returns Vacancy List
     * @return array
     */
    public function getVacancyList() {
        $list = array();
        $vacancyList = $this->getVacancyService()->getVacancyList();
        foreach ($vacancyList as $vacancy) {
            $list[] = array('id' => $vacancy->getId(), 'name' => $vacancy->getName());
        }
        return json_encode($list);
    }

    /**
     * Returns job Title List
     * @return array
     */
    private function getJobTitleList() {
       $jobTitleList = $this->getJobTitleService()->getJobTitleList();
        $list = array("" => "-- " . __('Select') . " --");
        foreach ($jobTitleList as $jobTitle) {
            $list[$jobTitle->getId()] = $jobTitle->getJobTitleName();
        }
        return $list;
    }

    /**
     *
     * @return <type>
     */
    public function getHiringManagerListAsJson() {

        $jsonArray = array();
        $employeeService = new EmployeeService();
        $employeeService->setEmployeeDao(new EmployeeDao());

        $properties = array("empNumber","firstName", "middleName", "lastName", "termination_id");
        $employeeList = $employeeService->getEmployeePropertyList($properties, 'lastName', 'ASC', true);

        foreach ($employeeList as $employee) {
            $empNumber = $employee['empNumber'];
            $name = trim(trim($employee['firstName'] . ' ' . $employee['middleName'],' ') . ' ' . $employee['lastName']);
        
            $jsonArray[] = array('name' => $name, 'id' => $empNumber);
        }
        $jsonString = json_encode($jsonArray);
        return $jsonString;
    }

    /**
     *
     * @param <type> $vacancyId
     * @return <type>
     */
    private function getVacancyDetails($vacancyId) {

        return $this->getVacancyService()->getVacancyById($vacancyId);
    }

}

