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
class CandidateVacancyStatusForm extends BaseForm {

    private $candidateService;
    public $candidateVacancyId;
    public $selectedAction;
    public $actionName;
    public $candidateName;
    public $vacancyName;
    public $hiringManagerName;
    public $candidateId;
    public $id;
    public $performedActionName;
    public $currentStatus;
    public $performedDate;
    public $performedBy;
    public $vacancyId;
    private $selectedCandidateVacancy;
    private $interviewService;

    /**
     *
     * @return <type>
     */
    public function getCandidateService() {
        if (is_null($this->candidateService)) {
            $this->candidateService = new CandidateService();
            $this->candidateService->setCandidateDao(new CandidateDao());
        }
        return $this->candidateService;
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

        $this->candidateVacancyId = $this->getOption('candidateVacancyId');
        $this->selectedAction = $this->getOption('selectedAction');
        $this->id = $this->getOption('id');
        $candidatePermissions = $this->getOption('candidatePermissions');
        if ($this->candidateVacancyId > 0 && $this->selectedAction != "") {
            $stateMachine = new WorkflowStateMachine();
            $this->actionName = $stateMachine->getRecruitmentActionName($this->selectedAction);
            $this->selectedCandidateVacancy = $this->getCandidateService()->getCandidateVacancyById($this->candidateVacancyId);
        }
        if ($this->id > 0) {
            $candidateHistory = $this->getCandidateService()->getCandidateHistoryById($this->id);
            $this->selectedCandidateVacancy = $this->getCandidateService()->getCandidateVacancyByCandidateIdAndVacancyId($candidateHistory->getCandidateId(), $candidateHistory->getVacancyId());
            $this->performedActionName = $candidateHistory->getActionName();
            $date = explode(" ", $candidateHistory->getPerformedDate());
            $this->performedDate = set_datepicker_date_format($date[0]);
            $this->performedBy = $candidateHistory->getPerformerName();
            $this->vacancyId = $candidateHistory->getVacancyId();
            $this->selectedAction = $candidateHistory->getAction();
        }
        $this->candidateId = $this->selectedCandidateVacancy->getCandidateId();
        $this->vacancyId = $this->selectedCandidateVacancy->getVacancyId();
        $this->candidateName = $this->selectedCandidateVacancy->getCandidateName();
        $this->vacancyName = $this->selectedCandidateVacancy->getVacancyName();
        $this->hiringManagerName = $this->selectedCandidateVacancy->getHiringManager();
        $this->currentStatus = ucwords(strtolower($this->selectedCandidateVacancy->getStatus()));

        $this->setWidget('notes', new sfWidgetFormTextArea());
        $this->setValidator('notes', new sfValidatorString(array('required' => false, 'max_length' => 2147483647)));

        if (!$candidatePermissions->canUpdate()) {
            $schema = $this->getWidgetSchema();
            $fields = $schema->getFields();

            foreach ($fields as $name => $widget) {
                $widget->setAttribute('disabled', 'disabled');
            }
        }

        $this->widgetSchema->setNameFormat('candidateVacancyStatus[%s]');

        if ($this->id > 0) {
            $this->setDefault('notes', $candidateHistory->getNote());
            $this->widgetSchema ['notes']->setAttribute('disabled', 'disable');
            $this->actionName = 'View Action History';
        }
    }

    /**
     *
     */
    public function performAction() {

        $note = $this->getValue('notes');
        if ($this->id > 0) {

            $history = $this->getCandidateService()->getCandidateHistoryById($this->id);
            $history->setNote($note);
            $this->getCandidateService()->saveCandidateHistory($history);
            $this->historyId = $history->getId();
            $resultArray['messageType'] = 'success';
            $resultArray['message'] = __(TopLevelMessages::SAVE_SUCCESS);
            return $resultArray;
        }
        $result = $this->getCandidateService()->updateCandidateVacancy($this->selectedCandidateVacancy, $this->selectedAction, sfContext::getInstance()->getUser()->getAttribute('user'));
        $interviews = $this->getInterviewService()->getInterviewsByCandidateVacancyId($this->candidateVacancyId);
        $interview = $interviews[count($interviews) - 1];
        $candidateHistory = new CandidateHistory();
        $candidateHistory->setCandidateId($this->candidateId);
        $candidateHistory->setVacancyId($this->vacancyId);
        $candidateHistory->setAction($this->selectedAction);
        $candidateHistory->setCandidateVacancyName($this->selectedCandidateVacancy->getVacancyName());
        if (!empty($interview)) {
            if ($this->selectedAction == WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHEDULE_INTERVIEW || $this->selectedAction == WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHEDULE_2ND_INTERVIEW || $this->selectedAction == WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_MARK_INTERVIEW_PASSED || $this->selectedAction == WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_MARK_INTERVIEW_FAILED) {
                $candidateHistory->setInterviewId($interview->getId());
            }
        }
        $empNumber = sfContext::getInstance()->getUser()->getEmployeeNumber();
        if ($empNumber == 0) {
            $empNumber = null;
        }
        $candidateHistory->setPerformedBy($empNumber);
        $date = date('Y-m-d');
        $candidateHistory->setPerformedDate($date . " " . date('H:i:s'));
        $candidateHistory->setNote($note);

        $result = $this->getCandidateService()->saveCandidateHistory($candidateHistory);
        $this->historyId = $candidateHistory->getId();

        if ($this->selectedAction == WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_HIRE) {

            $employee = new Employee();
            $employee->firstName = $this->selectedCandidateVacancy->getJobCandidate()->getFirstName();
            $employee->middleName = $this->selectedCandidateVacancy->getJobCandidate()->getMiddleName();
            $employee->lastName = $this->selectedCandidateVacancy->getJobCandidate()->getLastName();
            $employee->emp_oth_email = $this->selectedCandidateVacancy->getJobCandidate()->getEmail();
            $employee->job_title_code = $this->selectedCandidateVacancy->getJobVacancy()->getJobTitleCode();
            $employee->jobTitle = $this->selectedCandidateVacancy->getJobVacancy()->getJobTitle();

            $this->getCandidateService()->addEmployee($employee);
        }
    }

}
