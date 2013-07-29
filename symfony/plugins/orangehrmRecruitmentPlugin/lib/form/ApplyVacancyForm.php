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
class ApplyVacancyForm extends BaseForm {

    private $candidateService;
    private $recruitmentAttachmentService;
    private $workflowService;
    
    public $attachment;
    public $candidateId;
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
    
    public function getWorkflowService() {
        if (is_null($this->workflowService)) {
            $this->workflowService = new AccessFlowStateMachineService();        
        }
        return $this->workflowService;
    }
    
    public function configure() {

        $this->candidateId = $this->getOption('candidateId');
        $attachmentList = $this->attachment;
        if (count($attachmentList) > 0) {
            $this->attachment = $attachmentList[0];
        }

        //creating widgets
        $this->setWidgets(array(
            'firstName' => new sfWidgetFormInputText(),
            'middleName' => new sfWidgetFormInputText(),
            'lastName' => new sfWidgetFormInputText(),
            'email' => new sfWidgetFormInputText(),
            'contactNo' => new sfWidgetFormInputText(),
            'resume' => new sfWidgetFormInputFileEditable(array('edit_mode' => false, 'with_delete' => false, 'file_src' => '')),
            'keyWords' => new sfWidgetFormInputText(),
            'comment' => new sfWidgetFormTextArea(),
            'vacancyList' => new sfWidgetFormInputHidden(),
        ));

        $this->setValidators(array(
            'firstName' => new sfValidatorString(array('required' => true, 'max_length' => 35, 'trim' => true)),
            'middleName' => new sfValidatorString(array('required' => false, 'max_length' => 35,'trim' => true)),
            'lastName' => new sfValidatorString(array('required' => true, 'max_length' => 35, 'trim' => true)),
            'email' => new sfValidatorEmail(array('required' => true, 'max_length' => 100, 'trim' => true)),
            'contactNo' => new sfValidatorString(array('required' => false, 'max_length' => 35, 'trim' => true)),
            'resume' => new sfValidatorFile(array('required' => true, 'max_size' => 1024000, 'validated_file_class' => 'orangehrmValidatedFile')),
            'keyWords' => new sfValidatorString(array('required' => false, 'max_length' => 255)),
            'comment' => new sfValidatorString(array('required' => false)),
            'vacancyList' => new sfValidatorString(array('required' => false)),
        ));

        $this->widgetSchema->setNameFormat('addCandidate[%s]');

        if (!empty($this->candidateId)) {
            $candidate = $this->getCandidateService()->getCandidateById($this->candidateId);
            $this->setDefault('firstName', $candidate->getFirstName());
            $this->setDefault('middleName', $candidate->getMiddleName());
            $this->setDefault('lastName', $candidate->getLastName());
            $this->setDefault('email', $candidate->getEmail());
            $this->setDefault('contactNo', $candidate->getContactNumber());
            $this->attachment = $candidate->getJobCandidateAttachment();
            $this->setDefault('keyWords', $candidate->getKeywords());
            $this->setDefault('comment', $candidate->getComment());
            $candidateVacancyList = $candidate->getJobCandidateVacancy();
            $vacancyList = array();
            foreach ($candidateVacancyList as $candidateVacancy) {
                $vacancyList[] = $candidateVacancy->getVacancyId();
            }
            $this->setDefault('vacancyList', implode("_", $vacancyList));
        }
    }

    public function save() {

        $file = $this->getValue('resume');
        $resume = new JobCandidateAttachment();
        $candidate = new JobCandidate();
        $vacnacyId = $this->getValue('vacancyList');
        $resultArray = array();
        if (!($this->isValidResume($file))) {
            $resultArray['messageType'] = 'warning';
            $resultArray['message'] = __(TopLevelMessages::FILE_TYPE_SAVE_FAILURE);
            return $resultArray;
        } else {
            $this->candidateId = $this->_getNewlySavedCandidateId($candidate);
            $resultArray['candidateId'] = $this->candidateId;
            $resumeId = $this->_saveResume($file, $resume, $this->candidateId);
        }

        $this->_saveCandidateVacancies($vacnacyId, $this->candidateId);
        return $resultArray;
    }

    /**
     *
     * @param <type> $candidate
     * @return <type>
     */
    private function _getNewlySavedCandidateId($candidate) {

        $candidate->firstName = $this->getValue('firstName');
        $candidate->middleName = $this->getValue('middleName');
        $candidate->lastName = $this->getValue('lastName');
        $candidate->email = $this->getValue('email');
        $candidate->comment = $this->getValue('comment');
        $candidate->contactNumber = $this->getValue('contactNo');
        $candidate->keywords = $this->getValue('keyWords');
        $date = date('Y-m-d');
        $candidate->dateOfApplication = $date . " " . date('H:i:s');
        $candidate->status = JobCandidate::ACTIVE;
        $candidate->modeOfApplication = JobCandidate::MODE_OF_APPLICATION_ONLINE;

        $candidateService = $this->getCandidateService();
        $candidateService->saveCandidate($candidate);
        $candidateId = $candidate->getId();

        return $candidateId;
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
     * @param <type> $vacnacyId
     * @param <type> $candidateId 
     */
    protected function _saveCandidateVacancies($vacnacyId, $candidateId) {
        if (!empty($vacnacyId)) {

            $candidateVacancy = new JobCandidateVacancy();
            $candidateVacancy->candidateId = $candidateId;
            $candidateVacancy->vacancyId = $vacnacyId;
            $candidateVacancy->status = $this->getResultingStateForJobApplication();
            $candidateVacancy->appliedDate = date('Y-m-d');
            $candidateService = $this->getCandidateService();
            $candidateService->saveCandidateVacancy($candidateVacancy);
            $history = new CandidateHistory();
            $history->candidateId = $candidateId;
            $history->action = CandidateHistory::RECRUITMENT_CANDIDATE_ACTION_APPLY;
            $history->performedDate = $candidateVacancy->appliedDate;
            $history->vacancyId = $vacnacyId;
            $history->candidateVacancyName = $candidateVacancy->getVacancyName();
            $this->getCandidateService()->saveCandidateHistory($history);
        }
    }

    /**
     * Get the resulting state of a job application
     */
    protected function getResultingStateForJobApplication() {
            
        $resultingState = NULL;
        
        // Since job application is done by a non-logged in user, current workflow does not support configuring
        // this via the workflow state machine.
        // Therefore, we set the state to ADMIN workflow item for the INITIAL state.
        $workFlowItems = $this->getWorkflowService()->getAllowedWorkflowItems(WorkflowStateMachine::FLOW_RECRUITMENT, 'INITIAL', 'ADMIN');     

        if (count($workFlowItems) > 0) {
            $item = $workFlowItems[0];
            $resultingState = $item->getResultingState();
        }        
        
        if (is_null($resultingState)) {
            throw new RecruitmentExeption('No workflow items found for job vacancy INITIAL state for ADMIN');
        }
        return $resultingState;
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

        $this->attachment = $resume;
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

    /**
     *
     * @return JobCandidateAttachment
     */
    public function getResume() {
        return $this->attachment;
    }

}

?>
