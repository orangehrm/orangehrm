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
        //die;
        return $list;
    }

    public function getCandidateHistoryRecord($object) {

        $dto = new CandidateHistoryDto();
        $dto->setId($object->getId());
        $temp = explode(" ", $object->getPerformedDate());
        $dto->setPerformedDate($temp[0]);
        $dto->setVacancyName($object->getCandidateVacancyName());
        $description = $this->getCandidateHistoryDescription($object);
        $dto->setDescription($description);
        $array = array(CandidateHistory::RECRUITMENT_CANDIDATE_ACTION_ADD, CandidateHistory::RECRUITMENT_CANDIDATE_ACTION_APPLY, CandidateHistory::RECRUITMENT_CANDIDATE_ACTION_REMOVE, WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_ATTACH_VACANCY);
        $candidateVacancy = $this->getCandidateService()->getCandidateVacancyByCandidateIdAndVacancyId($object->getCandidateId(), $object->getVacancyId());
        $link = ($candidateVacancy == null || in_array($object->getAction(), $array) ? "" : __("View"));
        $dto->setDetails($link);

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
            case PluginWorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHEDULE_2ND_INTERVIEW:
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
        return __($object->getPerformerName()) . " " . __("added") . " " . $object->getJobCandidate()->getFullName();
    }

    public function getDescriptionForApply($object) {
        return $object->getJobCandidate()->getFullName()." ".__("applied for the vacancy ") . " " . $object->getCandidateVacancyName();
    }

    public function getDescriptionForRemove($object) {
        return __($object->getPerformerName()) . " " . __("removed") . " " . $object->getJobCandidate()->getFullName() . " " . __("from the")." ".$object->getCandidateVacancyName();
    }

    public function getDescriptionForAttachVacancy($object) {
        return __($object->getPerformerName()) . " " . __("assigned the job vacancy")." ".$object->getCandidateVacancyName();
    }

    public function getDescriptionForShortList($object) {
        return __("Shortlisted for")." ".$object->getCandidateVacancyName(). " " . __("by") . " " . __($object->getPerformerName());
    }

    public function getDescriptionForReject($object) {
        return __($object->getPerformerName()) . " " . __("rejected") . " " . $object->getJobCandidate()->getFullName() . " " . __("from the")." ".$object->getCandidateVacancyName();
    }

    public function getDescriptionForScheduleInterview($object) {

        $interviewId = $object->getInterviewId();
        $jobInterview = $this->getInterviewService()->getInterviewById($interviewId);
    $interviewers = $object->getInterviewers();
    $interviewers = explode("_", $interviewers);
        if ($jobInterview->getInterviewTime() == '00:00:00') {
            $time = "";
        } else {
            $time = __("at") . " " . date('H:i', strtotime($jobInterview->getInterviewTime())) . " ";
        }
        $interviewersNameList = array();
    $employeeService = new EmployeeService();
        for($i=0; $i < sizeof($interviewers)-1; $i++){
            $interviewersNameList[] = $employeeService->getEmployee($interviewers[$i])->getFullName();
        }
        return __($object->getPerformerName()) . " " . __("scheduled") . " " . $jobInterview->getInterviewName() . " " . __("on") . " " . set_datepicker_date_format($jobInterview->getInterviewDate())
        . " " . $time . __("with") . " " . implode(", ", $interviewersNameList)." ".__("for")." ".$object->getCandidateVacancyName();
    }

    public function getDescriptionForMarkInterviewPassed($object) {
        $interviewId = $object->getInterviewId();
        $jobInterview = $this->getInterviewService()->getInterviewById($interviewId);
        return __($object->getPerformerName()) . " " . __("marked") . " " . $jobInterview->getInterviewName() . " " . __("as passed")." ".__("for")." ".$object->getCandidateVacancyName();
    }

    public function getDescriptionForMarkInterviewFailed($object) {
        $interviewId = $object->getInterviewId();
        $jobInterview = $this->getInterviewService()->getInterviewById($interviewId);
        return __($object->getPerformerName()) . " " . __("marked") . " " . $jobInterview->getInterviewName() . " " . __("as failed")." ".__("for")." ".$object->getCandidateVacancyName();
    }

    public function getDescriptionForOfferJob($object) {
        return __($object->getPerformerName()) . " " . __("offered the job")." ".__("for")." ".$object->getCandidateVacancyName();
    }

    public function getDescriptionForDeclineOffer($object) {
        return __($object->getPerformerName()) . " " . __("marked the offer as declined")." ".__("for")." ".$object->getCandidateVacancyName();
    }

    public function getDescriptionForHire($object) {
        return __($object->getPerformerName()) . " " . __("hired") . " " . $object->getJobCandidate()->getFullName()." ".__("for")." ".$object->getCandidateVacancyName();
    }

    public function getInterviewService() {
        if (is_null($this->interviewService)) {
            $this->interviewService = new JobInterviewService();
            $this->interviewService->setJobInterviewDao(new JobInterviewDao());
        }
        return $this->interviewService;
    }

    public function getCandidateService() {
        if (is_null($this->candidateService)) {
            $this->candidateService = new CandidateService();
            $this->candidateService->setCandidateDao(new CandidateDao());
        }
        return $this->candidateService;
    }

}

