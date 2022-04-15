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
class JobInterviewForm extends BaseForm {

    public $candidateName;
    public $vacancyName;
    public $numberOfInterviewers = 5;
    public $candidateVacancyId;
    public $selectedAction;
    public $candidateId;
    public $vacancyId;
    public $historyId;
    public $currentStatus;
    private $candidateService;
    private $selectedCandidateVacancy;
    private $interviewService;
    private $defaultTime = '00:00:00';
    private $candidatePermissions;

    /**
     * 
     * @return CandidateService
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

    public function configure() {

        $this->candidateVacancyId = $this->getOption('candidateVacancyId');
        $this->selectedAction = $this->getOption('selectedAction');
        $this->id = $this->getOption('id');
        $this->interviewId = $this->getOption('interviewId');
        $this->historyId = $this->getOption('historyId');
        $this->candidatePermissions = $this->getOption('candidatePermissions');


        if ($this->candidateVacancyId > 0 && ($this->selectedAction == WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHEDULE_INTERVIEW || $this->selectedAction == WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHEDULE_2ND_INTERVIEW)) {
            $this->selectedCandidateVacancy = $this->getCandidateService()->getCandidateVacancyById($this->candidateVacancyId);
            $this->vacancyId = $this->selectedCandidateVacancy->getVacancyId();
            $this->candidateName = $this->selectedCandidateVacancy->getCandidateName();
            $this->vacancyName = $this->selectedCandidateVacancy->getVacancyName();
            $this->candidateId = $this->selectedCandidateVacancy->getCandidateId();
            $this->currentStatus = ucwords(strtolower($this->selectedCandidateVacancy->getStatus()));
        }

//creating widgets
        $this->setWidgets(array(
            'name' => new sfWidgetFormInputText(),
            'date' => new ohrmWidgetDatePicker(array(), array('id' => 'jobInterview_date')),
            'time' => new sfWidgetFormInputText(),
            'note' => new sfWidgetFormTextArea(),
            'selectedInterviewerList' => new sfWidgetFormInputHidden()
        ));

        for ($i = 1; $i <= $this->numberOfInterviewers; $i++) {
            $this->setWidget('interviewer_' . $i, new sfWidgetFormInputText());
        }

        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
        $this->setValidators(array(
            'name' => new sfValidatorString(array('required' => true, 'max_length' => 100)),
            'date' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => true),
                    array('invalid' => 'Date format should be ' . $inputDatePattern)),
            'time' => new sfValidatorString(array('required' => false, 'max_length' => 30)),
            'note' => new sfValidatorString(array('required' => false)),
            'selectedInterviewerList' => new sfValidatorString(array('required' => false))
        ));
        for ($i = 1; $i <= $this->numberOfInterviewers; $i++) {
            $this->setValidator('interviewer_' . $i, new sfValidatorString(array('required' => false, 'max_length' => 100)));
        }

        if (!$this->candidatePermissions->canUpdate()) {
            $schema = $this->getWidgetSchema();
            $fields = $schema->getFields();

            foreach ($fields as $name => $widget) {
                $widget->setAttribute('disabled', 'disabled');
            }
        }
        $this->validatorSchema->setPostValidator(
                new sfValidatorCallback(array(
                    'callback' => array($this, 'postValidate')
                ))
        );

        $this->widgetSchema->setNameFormat('jobInterview[%s]');

        if ($this->id != null) {
            $this->setDefaultValues($this->id);
        }
    }

    public function postValidate($validator, $values) {

        $time = $values['time'];
        $timeParts = explode(':', trim($time));

        if (empty($timeParts)) {
            return $values;
        }

        $hour = (int) $timeParts[0];
        $minutes = (int) $timeParts[1];

        if ($hour > 24 || $minutes > 59 || ($hour == 24 && $minutes > 0)) {
            $message = __('Invalid');
            $error = new sfValidatorError($validator, $message);
            throw new sfValidatorErrorSchema($validator, array('time' => $error));
        }

        return $values;
    }

    private function setDefaultValues($interviewId) {

        $interview = $this->getInterviewService()->getInterviewById($interviewId);
        $this->setDefault('name', $interview->getInterviewName());
        $this->setDefault('date', set_datepicker_date_format($interview->getInterviewDate()));
        if ($interview->getInterviewTime() == $this->defaultTime) {
            $this->setDefault('time', "");
        } else {
            $this->setDefault('time', date('H:i', strtotime($interview->getInterviewTime())));
        }
        $this->setDefault('note', $interview->getNote());

        $interviewers = $interview->getJobInterviewInterviewer();
        $this->setDefault('interviewer_1', $interviewers[0]->getEmployee()->getFullName());
        for ($i = 1; $i <= count($interviewers); $i++) {
            $this->setDefault('interviewer_' . $i, $interviewers[$i - 1]->getEmployee()->getFullName());
        }
        $this->setDefault('selectedInterviewerList', count($interviewers));
    }

    public function save() {

        $interviewArray = array();
        if (empty($this->interviewId)) {
            $newJobInterview = new JobInterview();
            $newCandidateHistory = new CandidateHistory();
            $interviewArray = $this->getValue('selectedInterviewerList');
            $selectedInterviewerArrayList = explode(",", $interviewArray);
        } else {
            $newCandidateHistory = $this->getCandidateService()->getCandidateHistoryById($this->historyId);
            $selectedInterviewerList = $this->getValue('selectedInterviewerList');
            $selectedInterviewerArrayList = explode(",", $selectedInterviewerList);
            $newJobInterview = $this->getInterviewService()->getInterviewById($this->interviewId);
            $existingInterviewers = $newJobInterview->getJobInterviewInterviewer();

            $idList = array();
            if ($existingInterviewers[0]->getInterviewerId() != "") {
                foreach ($existingInterviewers as $existingInterviewer) {
                    $id = $existingInterviewer->getInterviewerId();
                    if (!in_array($id, $selectedInterviewerArrayList)) {
                        $existingInterviewer->delete();
                    } else {
                        $idList[] = $id;
                    }
                }
            }

            $this->resultArray = array();

            $selectedInterviewerArrayList = array_diff($selectedInterviewerArrayList, $idList);
            $newList = array();
            foreach ($selectedInterviewerArrayList as $elements) {
                $newList[] = $elements;
            }
            $selectedInterviewerArrayList = $newList;
        }
        $interviewId = $this->saveInterview($newJobInterview, $selectedInterviewerArrayList);
        $this->saveCandidateHistory($newCandidateHistory, $interviewId);

        return $this->resultArray;
    }

    protected function saveInterview($newJobInterview, $selectedInterviewerArrayList) {

        $name = $this->getValue('name');
        $date = $this->getValue('date');
        $time = $this->getValue('time');
        $note = $this->getValue('note');
        $newJobInterview->setInterviewName($name);
        $newJobInterview->setInterviewDate($date);
        if (!empty($time)) {
            $newJobInterview->setInterviewTime($time);
        } else {
            $newJobInterview->setInterviewTime($this->defaultTime);
        }
        $newJobInterview->setNote($note);
        $newJobInterview->setCandidateVacancyId($this->candidateVacancyId);
        $newJobInterview->setCandidateId($this->candidateId);
        if (!empty($this->interviewId)) {
            $this->getInterviewService()->updateJobInterview($newJobInterview);
            $this->resultArray['messageType'] = 'success';
            $this->resultArray['message'] = __(TopLevelMessages::UPDATE_SUCCESS);
        } else {
            $newJobInterview->save();
        }

        $interviewId = $newJobInterview->getId();
        if (!empty($selectedInterviewerArrayList)) {
            for ($i = 0; $i < count($selectedInterviewerArrayList); $i++) {
                $newInterviewer = new JobInterviewInterviewer();
                $newInterviewer->setInterviewerId($selectedInterviewerArrayList[$i]);
                $newInterviewer->setInterviewId($interviewId);
                $newInterviewer->save();
            }
        }

        return $interviewId;
    }

    protected function saveCandidateHistory($newCandidateHistory, $interviewId) {

        $newCandidateHistory->setAction($this->selectedAction);
        $newCandidateHistory->setCandidateId($this->candidateId);

        $empNumber = sfContext::getInstance()->getUser()->getEmployeeNumber();
        if ($empNumber == 0) {
            $empNumber = null;
        }

        $newCandidateHistory->setVacancyId($this->selectedCandidateVacancy->getVacancyId());
        $newCandidateHistory->setPerformedBy($empNumber);
        $date = date('Y-m-d');
        $newCandidateHistory->setPerformedDate($date . " " . date('H:i:s'));
        $newCandidateHistory->setNote($note = $this->getValue('note'));
        $newCandidateHistory->setInterviewId($interviewId);
        $newCandidateHistory->setCandidateVacancyName($this->selectedCandidateVacancy->getVacancyName());
        $newCandidateHistory->setInterviewers($this->getInterviewInterviewers($interviewId));
        if (!empty($this->interviewId)) {
            $result = $this->getCandidateService()->updateCandidateHistory($newCandidateHistory);
        } else {
            $result = $this->getCandidateService()->saveCandidateHistory($newCandidateHistory);
        }
        if (empty($this->interviewId)) {
            $this->getCandidateService()->updateCandidateVacancy($this->selectedCandidateVacancy, $this->selectedAction, sfContext::getInstance()->getUser()->getAttribute('user'));
            $this->historyId = $newCandidateHistory->getId();
        }
    }

    public function getEmployeeListAsJson() {

        $jsonArray = array();
        $employeeService = new EmployeeService();
        $employeeService->setEmployeeDao(new EmployeeDao());

        $properties = array("empNumber", "firstName", "middleName", "lastName", 'termination_id');
        $employeeList = $employeeService->getEmployeePropertyList($properties, 'lastName', 'ASC', true);

        foreach ($employeeList as $employee) {
            $empNumber = $employee['empNumber'];
            $name = trim(trim($employee['firstName'] . ' ' . $employee['middleName'], ' ') . ' ' . $employee['lastName']);

            $jsonArray[] = array('name' => $name, 'id' => $empNumber);
        }

        $jsonString = json_encode($jsonArray);

        return $jsonString;
    }

    protected function getInterviewInterviewers($interviewId) {

        $interviewers = $this->getInterviewService()->getInterviewersByInterviewId($interviewId);
        $interviewersStr = "";
        foreach ($interviewers as $interviewer) {
            $interviewersStr = $interviewersStr . $interviewer->getInterviewerId() . "_";
        }
        return $interviewersStr;
    }

}

