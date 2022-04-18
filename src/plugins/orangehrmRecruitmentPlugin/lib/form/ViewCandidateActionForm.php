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

/**
 * Form class for search candidates
 */
class ViewCandidateActionForm extends BaseForm {

    private $candidateService;
    public $candidateId;
    public $candidate;
    public $empNumber;
    private $isAdmin;

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

    public function getInterviewService() {
        if (is_null($this->interviewService)) {
            $this->interviewService = new JobInterviewService();
            $this->interviewService->setJobInterviewDao(new JobInterviewDao());
        }
        return $this->interviewService;
    }

    /**
     *
     */
    public function configure() {

        $this->candidateId = $this->getOption('candidateId');
        $this->empNumber = $this->getOption('empNumber');
        $this->isAdmin = $this->getOption('isAdmin');
        if ($this->candidateId > 0) {
            $this->candidate = $this->getCandidateService()->getCandidateById($this->candidateId);
            $existingVacancyList = $this->candidate->getJobCandidateVacancy();
            if ($existingVacancyList[0]->getVacancyId() > 0) {
                $userObj = new User();
                $userRoleDecorator = new SimpleUserRoleFactory();
                $userRoleArray = array();
                foreach ($existingVacancyList as $candidateVacancy) {
                    $userRoleArray['isHiringManager'] = $this->getCandidateService()->isHiringManager($candidateVacancy->getId(), $this->empNumber);
                    $userRoleArray['isInterviewer'] = $this->getCandidateService()->isInterviewer($candidateVacancy->getId(), $this->empNumber);
                    if ($this->isAdmin) {
                        $userRoleArray['isAdmin'] = true;
                    }
                    $newlyDecoratedUserObj = $userRoleDecorator->decorateUserRole($userObj, $userRoleArray);
                    $choicesList = $this->getCandidateService()->getNextActionsForCandidateVacancy($candidateVacancy->getStatus(), $newlyDecoratedUserObj);
                    $interviewCount = count($this->getInterviewService()->getInterviewsByCandidateVacancyId($candidateVacancy->getId()));
                    if ($interviewCount == JobInterview::NO_OF_INTERVIEWS) {
                        unset($choicesList[WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHEDULE_INTERVIEW]);
                    }
                    if ($candidateVacancy->getJobVacancy()->getStatus() == JobVacancy::CLOSED) {
                        $choicesList = array("" => __("No Actions"));
                    }
                    $widgetName = $candidateVacancy->getId();
                    $this->setWidget($widgetName, new sfWidgetFormSelect(array('choices' => $choicesList)));
                    $this->setValidator($widgetName, new sfValidatorString(array('required' => false)));
                }
            }
        }
        $this->widgetSchema->setNameFormat('viewCandidateAction[%s]');
    }

}

