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
class ViewJobVacancyForm extends BaseForm {

    private $candidateService;
    private $vacancyService;
    private $allowedVacancyList;
    private $jobTitleService;

    public function getJobTitleService() {
        if (is_null($this->jobTitleService)) {
            $this->jobTitleService = new JobTitleService();
            $this->jobTitleService->setJobTitleDao(new JobTitleDao());
        }
        return $this->jobTitleService;
    }

    /**
     * Get CandidateService
     * @returns CandidateService
     */
    public function getCandidateService() {
        if (is_null($this->candidateService)) {
            $this->candidateService = new CandidateService();
            $this->candidateService->setCandidateDao(new CandidateDao());
        }
        return $this->candidateService;
    }

    /**
     * Set CandidateService
     * @param CandidateService $candidateService
     */
    public function setCandidateService(CandidateService $candidateService) {
        $this->candidateService = $candidateService;
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

        $this->allowedVacancyList = $this->getOption('allowedVacancyList');
        $hiringManagerList = $this->getHiringManagersList();
        $jobTitleList = $this->getJobTitleList();
        $statusList = $this->getStatusList();
        $jobVacancyList = $this->getVacancyList();

        $this->setWidgets(array(
            'jobTitle' => new sfWidgetFormSelect(array('choices' => $jobTitleList)),
            'jobVacancy' => new sfWidgetFormSelect(array('choices' => $jobVacancyList)),
            'hiringManager' => new sfWidgetFormSelect(array('choices' => $hiringManagerList)),
            'status' => new sfWidgetFormSelect(array('choices' => $statusList)),
        ));

        $this->setValidators(array(
            'jobTitle' => new sfValidatorString(array('required' => false)),
            'jobVacancy' => new sfValidatorString(array('required' => false)),
            'hiringManager' => new sfValidatorString(array('required' => false)),
            'status' => new sfValidatorString(array('required' => false)),
        ));

        $this->widgetSchema->setNameFormat('vacancySearch[%s]');
    }

    /**
     *
     * @param <type> $searchParam 
     */
    public function setDefaultDataToWidgets($searchParam) {
        $this->setDefault('jobTitle', $searchParam['jobTitle']);
        $this->setDefault('jobVacancy', $searchParam['jobVacancy']);
        $this->setDefault('hiringManager', $searchParam['hiringManager']);
        $this->setDefault('status', $searchParam['status']);
    }

    /**
     * Returns HiringManager List
     * @return array
     */
    private function getHiringManagersList() {

        $list = array("" => __('All'));
        $hiringManagersList = $this->getVacancyService()->getHiringManagersList("", "", $this->allowedVacancyList);
        foreach ($hiringManagersList as $hiringManager) {
            $list[$hiringManager['id']] = $hiringManager['name'];
        }

        return $list;
    }

    /**
     * Returns job Title List
     * @return array
     */
    private function getJobTitleList() {
        $jobTitleList = $this->getJobTitleService()->getJobTitleList();
        $list = array("" => __('All'));
        foreach ($jobTitleList as $jobTitle) {
            $list[$jobTitle->getId()] = $jobTitle->getJobTitleName();
        }
        return $list;
    }

    /**
     * Make status List
     * @return array
     */
    private function getStatusList() {
        $list = array("" => __('All'), JobVacancy::ACTIVE => __('Active'), JobVacancy::CLOSED => __("Closed"));
        return $list;
    }

    /**
     * Returns Vacancy List
     * @return array
     */
    private function getVacancyList() {
        $list = array("" => __('All'));
        $vacancyList = $this->getVacancyService()->getVacancyList();
        foreach ($vacancyList as $vacancy) {
            $list[$vacancy->getId()] = $vacancy->getName();
        }
        return $list;
    }

    /**
     *
     * @return <type>
     */
    public function getSearchParamsBindwithFormData() {

        $srchParams = array('jobTitle' => $this->getValue('jobTitle'),
            'jobVacancy' => $this->getValue('jobVacancy'),
            'hiringManager' => $this->getValue('hiringManager'),
            'status' => $this->getValue('status'));

        return $srchParams;
    }

}
