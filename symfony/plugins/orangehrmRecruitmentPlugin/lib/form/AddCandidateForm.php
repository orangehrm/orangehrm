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
class AddCandidateForm extends BaseForm {

    private $vacancyService;
    private $candidateService;
    public $attachment;
    public $candidateId;
    private $recruitmentAttachmentService;
    private $addedBy;
    private $addedHistory;
    private $removedHistory;
    public $allowedVacancyList;
    public $empNumber;
    private $isAdmin;
    private $candidatePermissions;
    private $allowedFileTypes = array(
        "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        "doc" => "application/msword",
        "doc" => "application/x-msword",
        "doc" => "application/vnd.ms-office",
        "odt" => "application/vnd.oasis.opendocument.text",
        "pdf" => "application/pdf",
        "pdf" => "application/x-pdf",
        "rtf" => "application/rtf",
        "rtf" => "text/rtf",
        "txt" => "text/plain"
    );

    const CONTRACT_KEEP = 1;
    const CONTRACT_DELETE = 2;
    const CONTRACT_UPLOAD = 3;

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

    /**
     *
     * @return <type>
     */
    public function getRecruitmentAttachmentService() {
        if (is_null($this->recruitmentAttachmentService)) {
            $this->recruitmentAttachmentService = new RecruitmentAttachmentService();
            $this->recruitmentAttachmentService->setRecruitmentAttachmentDao(new RecruitmentAttachmentDao());
        }
        return $this->recruitmentAttachmentService;
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
        $this->allowedVacancyList = $this->getOption('allowedVacancyList');
        $this->empNumber = $this->getOption('empNumber');
                $this->isAdmin = $this->getOption('isAdmin');
                
        $this->candidatePermissions = $this->getOption('candidatePermissions');
        $attachmentList = $this->attachment;
        if (count($attachmentList) > 0) {
            $this->attachment = $attachmentList[0];
        }
        $vacancyList = $this->getActiveVacancyList();
        if ($this->candidateId != null) {
            $jobCandidate = $this->getCandidateService()->getCandidateById($this->candidateId);
            if ($jobCandidate instanceof JobCandidate) {
                $candidateVacancyList = $jobCandidate->getJobCandidateVacancy();
                $vacancy = $candidateVacancyList[0]->getJobVacancy();
                if ($vacancy->getStatus() == JobVacancy::CLOSED) {
                    $vacancyList[$vacancy->getId()] = $vacancy->getVacancyName();
                } elseif ($vacancy->getStatus() == JobVacancy::ACTIVE) {
                    $vacancyList[$vacancy->getId()] = $vacancy->getName();
                }
            }
        }

        $resumeUpdateChoices = array(self::CONTRACT_KEEP => __('Keep Current'),
            self::CONTRACT_DELETE => __('Delete Current'),
            self::CONTRACT_UPLOAD => __('Replace Current'));

        // creating widgets
        $widgets = array(
            'firstName' => new sfWidgetFormInputText(),
            'middleName' => new sfWidgetFormInputText(),
            'lastName' => new sfWidgetFormInputText(),
            'email' => new sfWidgetFormInputText(),
            'contactNo' => new sfWidgetFormInputText(),
            'resume' => new sfWidgetFormInputFileEditable(
                    array('edit_mode' => false,
                        'with_delete' => false,
                        'file_src' => '')),
            'keyWords' => new sfWidgetFormInputText(),
            'comment' => new sfWidgetFormTextArea(),
            'appliedDate' => new ohrmWidgetDatePicker(array(), array('id' => 'addCandidate_appliedDate')),
            'vacancy' => new sfWidgetFormSelect(array('choices' => $vacancyList)),
            'resumeUpdate' => new sfWidgetFormChoice(array('expanded' => true, 'choices' => $resumeUpdateChoices)),
            'consentToKeepData' => new sfWidgetFormInputCheckbox(),
        );

        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();

        $validators = array(
            'firstName' => new sfValidatorString(array('required' => true, 'max_length' => 35)),
            'middleName' => new sfValidatorString(array('required' => false, 'max_length' => 35)),
            'lastName' => new sfValidatorString(array('required' => true, 'max_length' => 35)),
            'email' => new sfValidatorEmail(array('required' => true, 'max_length' => 100, 'trim' => true)),
            'contactNo' => new sfValidatorString(array('required' => false, 'max_length' => 35)),
            'resume' => new sfValidatorFile(array('required' => false, 'max_size' => 1024000,
                'validated_file_class' => 'orangehrmValidatedFile')),
            'keyWords' => new sfValidatorString(array('required' => false, 'max_length' => 255)),
            'comment' => new sfValidatorString(array('required' => false)),
            'appliedDate' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false),
                    array('invalid' => 'Date format should be ' . $inputDatePattern)),
            'vacancy' => new sfValidatorString(array('required' => false)),
            'resumeUpdate' => new sfValidatorString(array('required' => false)),
            'consentToKeepData' => new sfValidatorString(array('required' => false)),
        );
        
            
        if(!($this->candidatePermissions->canCreate() && empty($this->candidateId)) || ($this->candidatePermissions->canUpdate() && $this->candidateId > 0)){
            foreach ($widgets as $widget){
                $widget->setAttribute('disabled', 'disabled');
            }
        }
        
        $this->setWidgets($widgets);
        $this->setValidators($validators);
        
        $this->widgetSchema->setNameFormat('addCandidate[%s]');
        $this->widgetSchema['appliedDate']->setAttribute($name =null,$value = null);
        $this->setDefault('appliedDate', set_datepicker_date_format(date('Y-m-d')));

        if ($this->candidateId != null) {
            $this->setDefaultValues($this->candidateId);
        }
    }

    private function setDefaultValues($candidateId) {

        $candidate = $this->getCandidateService()->getCandidateById($candidateId);
        if ($candidate instanceof JobCandidate) {
            $this->setDefault('firstName', $candidate->getFirstName());
            $this->setDefault('middleName', $candidate->getMiddleName());
            $this->setDefault('lastName', $candidate->getLastName());
            $this->setDefault('email', $candidate->getEmail());
            $this->setDefault('contactNo', $candidate->getContactNumber());
            $this->attachment = $candidate->getJobCandidateAttachment();
            $this->setDefault('keyWords', $candidate->getKeywords());
            $this->setDefault('comment', $candidate->getComment());
            $this->setDefault('appliedDate', set_datepicker_date_format($candidate->getDateOfApplication()));
            $candidateVacancyList = $candidate->getJobCandidateVacancy();
            $defaultVacancy = ($candidateVacancyList[0]->getVacancyId() == "") ? "" : $candidateVacancyList[0]->getVacancyId();
            $this->setDefault('vacancy', $defaultVacancy);
            $this->setDefault('consentToKeepData', $candidate->getConsentToKeepData());
        }
    }

    private function getActiveVacancyList() {
        $list = array("" => "-- " . __('Select') . " --");
        $vacancyProperties = array('name', 'id', 'hiringManagerId');
        $activeVacancyList = $this->getVacancyService()->getVacancyPropertyList($vacancyProperties, JobVacancy::ACTIVE);
        
        $predefined = sfContext::getInstance()->getUser()->getAttribute('auth.userRole.predefined');
        foreach ($activeVacancyList as $vacancy) {
            $vacancyId = $vacancy['id'];
            if (in_array($vacancyId, $this->allowedVacancyList) && ($vacancy['hiringManagerId'] == $this->empNumber || $this->isAdmin
                    || !$predefined)) {
                $list[$vacancyId] = $vacancy['name'];
             }
        }
        return $list;
    }

    /**
     *
     * @return string
     */
    public function save() {

        $file = $this->getValue('resume');
        $resumeUpdate = $this->getValue('resumeUpdate');
        $resume = new JobCandidateAttachment();
        $resumeId = "";
        $candidate = new JobCandidate();
        $vacancy = $this->getValue('vacancy');
        $existingVacancyList = array();
        $empNumber = sfContext::getInstance()->getUser()->getEmployeeNumber();
        if ($empNumber == 0) {
            $empNumber = null;
        }
        $this->addedBy = $empNumber;

        if (!empty($file)) {
            if (!($this->isValidResume($file))) {
                $resultArray['messageType'] = 'warning';
                $resultArray['message'] = __(TopLevelMessages::FILE_TYPE_SAVE_FAILURE);
                return $resultArray;
            }
        }
        if ($this->candidateId != null) {
            $candidate = $this->getCandidateService()->getCandidateById($this->candidateId);
            $storedResume = $candidate->getJobCandidateAttachment();
            if ($storedResume != "") {
                $resume = $storedResume;
            }
            $existingVacancyList = $candidate->getJobCandidateVacancy();
            $candidateVacancy = $existingVacancyList[0];
            $id = $candidateVacancy->getVacancyId();
            if (!empty($id)) {
                if ($id != $vacancy) {
                    $interviews = $this->getInterviewService()->getInterviewsByCandidateVacancyId($candidateVacancy);
                    foreach ($interviews as $interview) {
                        $interviewers = $interview->getJobInterviewInterviewer();
                        foreach ($interviewers as $interviewer) {
                            $interviewer->delete();
                        }
                    }
                    $candidateVacancy->delete();
                    $vacancyName = $candidateVacancy->getVacancyName();
                    $this->removedHistory = new CandidateHistory();
                    $this->removedHistory->candidateId = $this->candidateId;
                    $this->removedHistory->action = CandidateHistory::RECRUITMENT_CANDIDATE_ACTION_REMOVE;
                    $this->removedHistory->performedBy = $this->addedBy;
                    $date = date('Y-m-d');
                    $this->removedHistory->performedDate = $date . " " . date('H:i:s');
                    $this->removedHistory->candidateVacancyName = $vacancyName;
                    $this->removedHistory->vacancyId = $id;
                    $this->_saveCandidateVacancies($vacancy, $this->candidateId);
                }
            } else {
                $this->_saveCandidateVacancies($vacancy, $this->candidateId);
            }
        }

        if ($resumeUpdate == self::CONTRACT_DELETE) {
            $resume->delete();
        }
        $candidateId = $this->_getNewlySavedCandidateId($candidate);

        $resultArray = array();
        $resultArray['candidateId'] = $candidateId;
        if (!empty($file)) {
            $resumeId = $this->_saveResume($file, $resume, $candidateId);
        }
        if ($this->candidateId == "") {
            $this->_saveCandidateVacancies($vacancy, $candidateId);
        }
        if (!empty($this->addedHistory)) {
            $this->getCandidateService()->saveCandidateHistory($this->addedHistory);
        }
        if (!empty($this->removedHistory)) {
            $this->getCandidateService()->saveCandidateHistory($this->removedHistory);
        }
        return $resultArray;
    }

    /**
     *
     * @param sfValidatedFile $file
     * @return <type>
     */
    protected function isValidResume($file) {
        $validFile = false;

        $mimeTypes = array_values($this->allowedFileTypes);
        $originalName = $file->getOriginalName();

        if (($file instanceof orangehrmValidatedFile) && $originalName != "") {

            $fileType = $file->getType();

            if (!empty($fileType) && in_array($fileType, $mimeTypes)) {
                $validFile = true;
            } else {
                $fileType = $this->guessTypeFromFileExtension($originalName);

                if (!empty($fileType)) {
                    $file->setType($fileType);
                    $validFile = true;
                }
            }
        }

        return $validFile;
    }

    /**
     *
     * @param <type> $file
     * @param <type> $resume
     * @param <type> $candidateId
     * @return <type> 
     */
    private function _saveResume($file, $resume, $candidateId) {

        $tempName = $file->getTempName();
        $resume->fileContent = file_get_contents($tempName);
        $resume->fileName = $file->getOriginalName();
        $resume->fileType = $file->getType();
        $resume->fileSize = $file->getSize();
        $resume->fileSize = $file->getSize();
        $resume->candidateId = $candidateId;

        $recruitmentAttachmentService = $this->getRecruitmentAttachmentService();
        $recruitmentAttachmentService->saveCandidateAttachment($resume);
    }

    /**
     *
     * @param <type> $candidate
     * @return <type>
     */
    private function _getNewlySavedCandidateId($candidate) {

        $candidate->firstName = trim($this->getValue('firstName'));
        $candidate->middleName = trim($this->getValue('middleName'));
        $candidate->lastName = trim($this->getValue('lastName'));
        $candidate->email = $this->getValue('email');
        $candidate->comment = $this->getValue('comment');
        $candidate->contactNumber = $this->getValue('contactNo');
        $candidate->keywords = $this->getValue('keyWords');
        $candidate->addedPerson = $this->addedBy;
        if ($this->getValue('consentToKeepData')) {
            $candidate->consentToKeepData = true;
        }else{
            $candidate->consentToKeepData = false;
        }
        if ($this->getValue('appliedDate') == "") {
            $candidate->dateOfApplication = date('Y-m-d');
        } else {
            $candidate->dateOfApplication = $this->getValue('appliedDate');
        }
        $candidate->status = JobCandidate::ACTIVE;
        $candidate->modeOfApplication = JobCandidate::MODE_OF_APPLICATION_MANUAL;

        $candidateService = $this->getCandidateService();
        if ($this->candidateId != null) {
            $candidateService->updateCandidate($candidate);
        } else {
            $candidateService->saveCandidate($candidate);
            $this->addedHistory = new CandidateHistory();
            $this->addedHistory->candidateId = $candidate->getId();
            $this->addedHistory->action = CandidateHistory::RECRUITMENT_CANDIDATE_ACTION_ADD;
            $this->addedHistory->performedBy = $this->addedBy;
            $date = date('Y-m-d');
            $this->addedHistory->performedDate = $date . " " . date('H:i:s');
        }
        $candidateId = $candidate->getId();
        return $candidateId;
    }

    /**
     *
     * @param <type> $vacnacyArray
     * @param <type> $candidateId
     */
    private function _saveCandidateVacancies($vacnacy, $candidateId) {

        if ($vacnacy != null) {
            $candidateVacancy = new JobCandidateVacancy();
            $candidateVacancy->candidateId = $candidateId;
            $candidateVacancy->vacancyId = $vacnacy;
            
            // Get correct status for candidate vacancy
            $userRoleManager = UserRoleManagerFactory::getUserRoleManager();
            $workflowItems = $userRoleManager->getAllowedActions(WorkflowStateMachine::FLOW_RECRUITMENT, 'INITIAL');
            
            if (isset($workflowItems[WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_ATTACH_VACANCY])) {
                
                $workflowItem = $workflowItems[WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_ATTACH_VACANCY];
                $candidateVacancy->status = $workflowItem->getResultingState();
                if ($this->getValue('appliedDate') == "") {
                    $candidateVacancy->appliedDate = date('Y-m-d');
                } else {
                    $candidateVacancy->appliedDate = $this->getValue('appliedDate');
                }
                $candidateService = $this->getCandidateService();
                $candidateService->saveCandidateVacancy($candidateVacancy);
                $history = new CandidateHistory();
                $history->candidateId = $candidateId;
                $history->action = WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_ATTACH_VACANCY;
                $history->vacancyId = $candidateVacancy->getVacancyId();
                $history->performedBy = $this->addedBy;
                $date = date('Y-m-d');
                $history->performedDate = $date . " " . date('H:i:s');
                $history->candidateVacancyName = $candidateVacancy->getVacancyName();
                $this->getCandidateService()->saveCandidateHistory($history);
            } else {
                throw new RecruitmentExeption('No workflow items found for job vacancy INITIAL state');
            }
        }
    }

    /**
     *
     * @return JobCandidateAttachment
     */
    public function getResume() {
        return $this->attachment;
    }

    /**
     * Guess the file mime type from the file extension
     *
     * @param  string $file  The absolute path of a file
     *
     * @return string The mime type of the file (null if not guessable)
     */
    public function guessTypeFromFileExtension($file) {

        $mimeType = null;

        $extension = pathinfo($file, PATHINFO_EXTENSION);

        if (isset($this->allowedFileTypes[$extension])) {
            $mimeType = $this->allowedFileTypes[$extension];
        }

        return $mimeType;
    }

}

