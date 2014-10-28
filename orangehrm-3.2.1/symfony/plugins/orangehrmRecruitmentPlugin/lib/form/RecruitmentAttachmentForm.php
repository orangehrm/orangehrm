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
class RecruitmentAttachmentForm extends BaseForm {

	private $candidateService;
	private $vacancyService;
	private $recruitmentAttachmentService;
	private $screen;

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
	 * Get RecruitmentAttachmentService
	 * @returns RecruitmentAttachmentService
	 */
	public function getRecruitmentAttachmentService() {
		if (is_null($this->recruitmentAttachmentService)) {
			$this->recruitmentAttachmentService = new RecruitmentAttachmentService();
			$this->recruitmentAttachmentService->setRecruitmentAttachmentDao(new RecruitmentAttachmentDao());
		}
		return $this->recruitmentAttachmentService;
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
	 *
	 */
	public function configure() {

		$this->screen = $this->getOption('screen');

		$this->setWidgets(array(
		    'vacancyId' => new sfWidgetFormInputHidden(),
		    'ufile' => new sfWidgetFormInputFile(),
		    'comment' => new sfWidgetFormTextArea(),
		    'commentOnly' => new sfWidgetFormInputHidden(),
		    'recruitmentId' => new sfWidgetFormInputHidden(),
		));

		$this->setValidators(array(
		    'vacancyId' => new sfValidatorNumber(array('required' => false, 'min' => 0)),
		    'ufile' => new sfValidatorFile(array('required' => false,
			'max_size' => 1024000), array('max_size' => __('Attachment Size Exceeded.'))),
		    'comment' => new sfValidatorString(array('required' => false, 'max_length' => 255)),
		    'commentOnly' => new sfValidatorString(array('required' => false)),
		    'recruitmentId' => new sfValidatorString(array('required' => false)),
		));

		$this->widgetSchema->setNameFormat('recruitmentAttachment[%s]');
	}

	/**
	 *
	 * @return <type>
	 */
	public function save() {

		$recId = $this->getValue('recruitmentId');
		$commentOnly = $this->getValue('commentOnly');
		$file = $this->getValue('ufile');
		$candidateService = $this->getRecruitmentAttachmentService();
		if ($recId != "") {
			$existRec = $this->getRecruitmentAttachmentService()->getAttachment($recId, $this->screen);
			if ($commentOnly == '1') {
				$existRec->comment = $this->getValue('comment');
				//$candidateService->saveAttachment($existRec, $this->screen);
				$existRec->save();
				return;
			} else {
				$existRec->delete();
			}
		}

		$id = $this->getValue('vacancyId');
		if (($file instanceof sfValidatedFile) && $file->getOriginalName() != "") {
			$tempName = $file->getTempName();
			$attachment = $this->getRecruitmentAttachmentService()->getNewAttachment($this->screen, $id);
			//$attachment = new JobVacancyAttachment();
			//$attachment->vacancyId = $vacancyId;
			$attachment->fileContent = file_get_contents($tempName);
			$attachment->fileName = $file->getOriginalName();
			$attachment->fileType = $file->getType();
			$attachment->fileSize = $file->getSize();
			$attachment->comment = $this->getValue('comment');
			//$candidateService->saveVacancyAttachment($attachment);
			$attachment->save();
		}
	}

}