<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Recruitment\Service;

use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Recruitment\Dao\CandidateDao;

class CandidateService
{
    public const RECRUITMENT_CANDIDATE_VACANCY_REMOVED = 15;
    public const RECRUITMENT_CANDIDATE_ACTION_ADD = 16;
    public const RECRUITMENT_CANDIDATE_ACTION_APPLIED = 17;

    public const STATUS_MAP = [
        WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_ATTACH_VACANCY => 'APPLICATION INITIATED',
        WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHORTLIST => 'SHORTLISTED',
        WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_REJECT => 'REJECTED',
        WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHEDULE_INTERVIEW => 'INTERVIEW SCHEDULED',
        WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_MARK_INTERVIEW_PASSED => 'INTERVIEW PASSED',
        WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_MARK_INTERVIEW_FAILED => 'INTERVIEW FAILED',
        WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_OFFER_JOB => 'JOB OFFERED',
        WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_DECLINE_OFFER => 'OFFER DECLINED',
        WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_HIRE => 'HIRED',
    ];

    public const OTHER_ACTIONS_MAP = [
        self::RECRUITMENT_CANDIDATE_ACTION_ADD => 'ADDED',
        self::RECRUITMENT_CANDIDATE_VACANCY_REMOVED => 'REMOVED',
        self::RECRUITMENT_CANDIDATE_ACTION_APPLIED => 'APPLIED'
    ];

    protected ?CandidateDao $candidateDao = null;

    /**
     * Get Candidate Dao
     * @return CandidateDao
     */
    public function getCandidateDao(): CandidateDao
    {
        if (is_null($this->candidateDao)) {
            $this->candidateDao = new CandidateDao();
        }
        return $this->candidateDao;
    }
}
