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
        public $allowedVacancyList;

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

	/**
	 *
	 */
	public function configure() {

		$this->candidateId = $this->getOption('candidateId');
		$this->allowedVacancyList = $this->getOption('allowedVacancyList');
		$attachmentList = $this->attachment;
		if (count($attachmentList) > 0) {
			$this->attachment = $attachmentList[0];
		}

		$vacancyList = $this->getVacancyList();

		$resumeUpdateChoices = array(self::CONTRACT_KEEP => __('Keep Current'),
		    self::CONTRACT_DELETE => __('Delete Current'),
		    self::CONTRACT_UPLOAD => __('Replace Current'));

		//creating widgets
		$this->setWidgets(array(
		    'firstName' => new sfWidgetFormInputText(),
		    'middleName' => new sfWidgetFormInputText(),
		    'lastName' => new sfWidgetFormInputText(),
		    'email' => new sfWidgetFormInputText(),
		    'contactNo' => new sfWidgetFormInputText(),
		    'resume' => new sfWidgetFormInputFileEditable(array('edit_mode' => false, 'with_delete' => false, 'file_src' => '')/* , array("size" => 37) */),
		    'keyWords' => new sfWidgetFormInputText(),
		    'comment' => new sfWidgetFormTextArea(),
		    'appliedDate' => new sfWidgetFormInputText(),
		    'vacancyList' => new sfWidgetFormInputHidden(),
		    'resumeUpdate' => new sfWidgetFormChoice(array('expanded' => true, 'choices' => $resumeUpdateChoices)),
		));

		$this->setValidators(array(
		    'firstName' => new sfValidatorString(array('required' => true, 'max_length' => 35)),
		    'middleName' => new sfValidatorString(array('required' => false, 'max_length' => 35)),
		    'lastName' => new sfValidatorString(array('required' => true, 'max_length' => 35)),
		    'email' => new sfValidatorEmail(array('required' => true, 'max_length' => 100, 'trim' => true)),
		    'contactNo' => new sfValidatorString(array('required' => false, 'max_length' => 35)),
		    'resume' => new sfValidatorFile(array('required' => false, 'max_size' => 1024000)),
		    'keyWords' => new sfValidatorString(array('required' => false, 'max_length' => 255)),
		    'comment' => new sfValidatorString(array('required' => false)),
		    'appliedDate' => new sfValidatorString(array('required' => false, 'max_length' => 30)),
		    'vacancyList' => new sfValidatorString(array('required' => false)),
		    'resumeUpdate' => new sfValidatorString(array('required' => false)),
		));

		$this->widgetSchema->setNameFormat('addCandidate[%s]');
		$this->widgetSchema['appliedDate']->setAttribute('style', 'width:100px');
		$this->setDefault('appliedDate', ohrm_format_date(date('Y-m-d')));

		if ($this->candidateId != null) {
			$this->setDefaultValues($this->candidateId);
		}
	}

	private function setDefaultValues($candidateId) {

		$candidate = $this->getCandidateService()->getCandidateById($candidateId);
		$this->setDefault('firstName', $candidate->getFirstName());
		$this->setDefault('middleName', $candidate->getMiddleName());
		$this->setDefault('lastName', $candidate->getLastName());
		$this->setDefault('email', $candidate->getEmail());
		$this->setDefault('contactNo', $candidate->getContactNumber());
		$this->attachment = $candidate->getJobCandidateAttachment();
		$this->setDefault('keyWords', $candidate->getKeywords());
		$this->setDefault('comment', $candidate->getComment());
		$this->setDefault('appliedDate', $candidate->getDateOfApplication());
		$candidateVacancyList = $candidate->getJobCandidateVacancy();
		$vacancyList = array();
		foreach ($candidateVacancyList as $candidateVacancy) {
			$vacancyList[] = $candidateVacancy->getVacancyId();
		}
		$this->setDefault('vacancyList', implode("_", $vacancyList));
	}

	/**
	 *
	 * @return <type> 
	 */
	private function getVacancyList() {
		$list = array("" => "-- " . __('Select') . " --");
		$vacancyList = $this->getVacancyService()->getVacancyList();
		foreach ($vacancyList as $vacancy) {
			$list[$vacancy->getId()] = $vacancy->getName();
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
		$vacnacyArray = explode("_", $this->getValue('vacancyList'));
		$existingVacancyList = array();
		$empNumber = sfContext::getInstance()->getUser()->getEmployeeNumber();
		if ($empNumber == 0) {
			$empNumber = null;
		}
		$this->addedBy = $empNumber;

		if (!empty($file)) {
			if (!($this->isValidResume($file))) {
				$resultArray['messageType'] = 'warning';
				$resultArray['message'] = __('Error Occurred - Invalid File Type');
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

			$idList = array();
			if ($existingVacancyList[0]->getVacancyId() != "") {
				foreach ($existingVacancyList as $candidateVacancy) {
					$id = $candidateVacancy->getVacancyId();
					if (!in_array($id, $vacnacyArray)) {
						$vacancyName = $candidateVacancy->getVacancyName();
						$candidateVacancy->delete();
						$history = new CandidateHistory();
						$history->candidateId = $this->candidateId;
						$history->action = CandidateHistory::RECRUITMENT_CANDIDATE_ACTION_REMOVE;
						$history->performedBy = $this->addedBy;
						$history->performedDate = ohrm_format_date(date('Y-m-d'));
						$history->candidateVacancyName = $vacancyName;

						$this->getCandidateService()->saveCandidateHistory($history);
					} else {
						$idList[] = $id;
					}
				}
			}
			$vacnacyArray = array_diff($vacnacyArray, $idList);

			$newList = array();
			foreach ($vacnacyArray as $elements) {
				$newList[] = $elements;
			}
			$vacnacyArray = $newList;
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
		$this->_saveCandidateVacancies($vacnacyArray, $candidateId);
		return $resultArray;
	}

	/**
	 *
	 * @param sfValidatedFile $file
	 * @return <type>
	 */
	protected function isValidResume($file) {

		if (($file instanceof sfValidatedFile) && $file->getOriginalName() != "") {

			$fileType = $file->getType();
			$allowedImageTypes[] = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
			$allowedImageTypes[] = "application/msword";
			$allowedImageTypes[] = "application/x-msword";
			$allowedImageTypes[] = "application/vnd.oasis.opendocument.text";
			$allowedImageTypes[] = "application/pdf";
			$allowedImageTypes[] = "application/zip";
			$allowedImageTypes[] = "application/x-pdf";
			$allowedImageTypes[] = "application/rtf";
			$allowedImageTypes[] = "text/rtf";
			$allowedImageTypes[] = "text/plain";

			if (!empty($fileType) && !in_array($fileType, $allowedImageTypes)) {
				return false;
			} else {
				return true;
			}
		}
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

		$candidate->firstName = $this->getValue('firstName');
		$candidate->middleName = $this->getValue('middleName');
		$candidate->lastName = $this->getValue('lastName');
		$candidate->email = $this->getValue('email');
		$candidate->comment = $this->getValue('comment');
		$candidate->contactNumber = $this->getValue('contactNo');
		$candidate->keywords = $this->getValue('keyWords');
		$candidate->addedPerson = $this->addedBy;

		if ($this->getValue('appliedDate') == "") {
			$candidate->dateOfApplication = ohrm_format_date(date('Y-m-d'));
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
			$history = new CandidateHistory();
			$history->candidateId = $candidate->getId();
			$history->action = CandidateHistory::RECRUITMENT_CANDIDATE_ACTION_ADD;
			$history->performedBy = $this->addedBy;
			$history->performedDate = $candidate->dateOfApplication;
			$this->getCandidateService()->saveCandidateHistory($history);
		}
		$candidateId = $candidate->getId();
		return $candidateId;
	}

	/**
	 *
	 * @param <type> $vacnacyArray
	 * @param <type> $candidateId
	 */
	private function _saveCandidateVacancies($vacnacyArray, $candidateId) {
		// print_r($vacnacyArray);die;
		if ($vacnacyArray[0] != null) {
			for ($i = 0; $i < sizeof($vacnacyArray) - 1; $i++) {
				if ($vacnacyArray[$i] != "") {
					$candidateVacancy = new JobCandidateVacancy();
					$candidateVacancy->candidateId = $candidateId;
					$candidateVacancy->vacancyId = $vacnacyArray[$i];
					$candidateVacancy->status = "APPLICATION INITIATED";
					if ($this->getValue('appliedDate') == "") {
						$candidateVacancy->appliedDate = ohrm_format_date(date('Y-m-d'));
					} else {
						$candidateVacancy->appliedDate = $this->getValue('appliedDate');
					}
					$candidateService = $this->getCandidateService();
					$candidateService->saveCandidateVacancy($candidateVacancy);
					$history = new CandidateHistory();
					$history->candidateId = $candidateId;
					$history->action = WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_ATTACH_VACANCY;
					$history->candidateVacancyId = $candidateVacancy->getId();
					$history->performedBy = $this->addedBy;
					$history->performedDate = $candidateVacancy->appliedDate;
					$history->candidateVacancyName= $candidateVacancy->getVacancyName();
					$this->getCandidateService()->saveCandidateHistory($history);
				}
			}
		}
	}

}

