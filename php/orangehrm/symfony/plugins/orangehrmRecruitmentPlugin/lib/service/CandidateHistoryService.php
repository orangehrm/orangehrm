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
 */

/**
 * CandidateHistoryService
 */
class CandidateHistoryService {

	private $interviewService;


	public function getCandidateHistoryList($objects) {
		$list = array();
		foreach ($objects as $object) {
			$list[] = $this->getCandidateHistoryRecord($object);
		}
		return $list;
	}

	public function getCandidateHistoryRecord($object) {

		$dto = new CandidateHistoryDto();
		$dto->setId($object->getId());
		$dto->setPerformedDate($object->getPerformedDate());
		$dto->setVacancyName($object->getCandidateVacancyName());
		$description = $this->getCandidateHistoryDescription($object);
		$dto->setDescription($description);
		$dto->setDetails($object->getLinkLabel());

		return $dto;
	}

	public function getCandidateHistoryDescription($object) {
		$description = "";
		switch ($object->getAction()) {

			case CandidateHistory::RECRUITMENT_CANDIDATE_ACTION_ADD:
				$description = $this->getDescriptionForAdd($object);
				break;
			case CandidateHistory::RECRUITMENT_CANDIDATE_ACTION_APPLY:
				$description = $this->getDescriptionForApply($object);
				break;
			case CandidateHistory::RECRUITMENT_CANDIDATE_ACTION_REMOVE:
				$description = $this->getDescriptionForRemove($object);
				break;
			case PluginWorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_ATTACH_VACANCY:
				$description = $this->getDescriptionForAttachVacancy($object);
				break;
			case PluginWorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHORTLIST:
				$description = $this->getDescriptionForShortList($object);
				break;
			case PluginWorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_REJECT:
				$description = $this->getDescriptionForReject($object);
				break;
			case PluginWorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHEDULE_INTERVIEW:
				$description = $this->getDescriptionForScheduleInterview($object);
				break;
			case PluginWorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_MARK_INTERVIEW_PASSED:
				$description = $this->getDescriptionForMarkInterviewPassed($object);
				break;
			case PluginWorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_MARK_INTERVIEW_FAILED:
				$description = $this->getDescriptionForMarkInterviewFailed($object);
				break;
			case PluginWorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_OFFER_JOB:
				$description = $this->getDescriptionForOfferJob($object);
				break;
			case PluginWorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_DECLINE_OFFER:
				$description = $this->getDescriptionForDeclineOffer($object);
				break;
			case PluginWorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_HIRE:
				$description = $this->getDescriptionForHire($object);
				break;
		}
		return $description;
	}

	/** Description generator block begins * */
	public function getDescriptionForAdd($object) {
		return $object->getPerformerName() . " " . __("added");
	}

	public function getDescriptionForApply($object) {
		return __("Applied for the") . " " . $object->getVacancyName();
	}

	public function getDescriptionForRemove($object) {
		return $object->getPerformerName() . " " . __("removed from the vacancy");
	}

	public function getDescriptionForAttachVacancy($object) {
		return $object->getPerformerName() . " " . __("assigned the job vacancy");
	}

	public function getDescriptionForShortList($object) {
		return __("Shortlisted") . " " . __("by") . " " . $object->getPerformerName();
	}

	public function getDescriptionForReject($object) {
		return $object->getPerformerName() ." ". __("rejected from the vacanay");
	}

	public function getDescriptionForScheduleInterview($object) {
		
		$interviewId = $object->getInterviewId();
		$jobInterview = $this->getInterviewService()->getInterviewById($interviewId);
		$interviewers =  $this->getInterviewService()->getInterviewersByInterviewId($interviewId);
		$interviewersNameList = array();
		foreach ($interviewers as $interviewer) {
			$interviewersNameList[] = $interviewer->getEmployee()->getFullName();
		}
		return $object->getPerformerName() . " " . __("scheduled") . " " . $jobInterview->getInterviewName() . " " . __("on") . " " . $jobInterview->getInterviewDate()
		. " " . __("at") . " " . $jobInterview->getInterviewTime() . " " . __("with") . " " . implode(", ", $interviewersNameList);

	}

	public function getDescriptionForMarkInterviewPassed($object) {
		$interviewId = $object->getInterviewId();
		$jobInterview = $this->getInterviewService()->getInterviewById($interviewId);
		return $object->getPerformerName() ." ".__("marked")." ".$jobInterview->getInterviewName()." ".__("as passed");
	}

	public function getDescriptionForMarkInterviewFailed($object) {
		$interviewId = $object->getInterviewId();
		$jobInterview = $this->getInterviewService()->getInterviewById($interviewId);
		return $object->getPerformerName() ." ".__("marked")." ".$jobInterview->getInterviewName()." ".__("as failed");
	}

	public function getDescriptionForOfferJob($object) {
		return $object->getPerformerName()." ".__("offred the job");
	}

	public function getDescriptionForDeclineOffer($object) {
		return $object->getPerformerName()." ".__("declined the offer");
	}

	public function getDescriptionForHire($object) {
		return $object->getPerformerName()." ".__("hired");
	}

	public function getInterviewService() {
		if (is_null($this->interviewService)) {
			$this->interviewService = new JobInterviewService();
			$this->interviewService->setJobInterviewDao(new JobInterviewDao());
		}
		return $this->interviewService;
	}

}

