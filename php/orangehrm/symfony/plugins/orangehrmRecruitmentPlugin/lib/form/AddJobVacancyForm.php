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

        //creating widgets
        $this->setWidgets(array(
            'jobTitle' => new sfWidgetFormSelect(array('choices' => $jobTitleList)),
            'name' => new sfWidgetFormInputText(),
            'hiringManager' => new sfWidgetFormInputText(),
            'hiringManagerId' => new sfWidgetFormInputHidden(),
            'noOfPositions' => new sfWidgetFormInputText(),
            'description' => new sfWidgetFormTextArea(),
            'status' => new sfWidgetFormInputCheckbox(),
            'publishedInFeed' => new sfWidgetFormInputCheckbox(),
        ));

        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();

        //Setting validators
        $this->setValidators(array(
            'jobTitle' => new sfValidatorString(array('required' => true)),
            'name' => new sfValidatorString(array('required' => true)),
            'hiringManager' => new sfValidatorString(array('required' => true)),
            'hiringManagerId' => new sfValidatorInteger(array('required' => true, 'min' => 0)),
            'noOfPositions' => new sfValidatorInteger(array('required' => false, 'min' => 0)),
            'description' => new sfValidatorString(array('required' => false, 'max_length' => 41000)),
            'status' => new sfValidatorString(array('required' => false)),
            'publishedInFeed' => new sfValidatorString(array('required' => false)),
        ));
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
        $jobVacancy->hiringManagerId = $this->getValue('hiringManagerId');
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

        // Fetch only none terminated employees
        $employeeList = $employeeService->getEmployeeList('lastName', 'ASC', false);

        $employeeUnique = array();
        foreach ($employeeList as $employee) {

            if (!isset($employeeUnique[$employee->getEmpNumber()])) {

                $name = $employee->getFirstName() . " " . $employee->getMiddleName();
                $name = trim(trim($name) . " " . $employee->getLastName());

                $employeeUnique[$employee->getEmpNumber()] = $name;
                $jsonArray[] = array('name' => $name, 'id' => $employee->getEmpNumber());
            }
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

