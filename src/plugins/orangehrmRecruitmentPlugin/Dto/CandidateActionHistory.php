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

namespace OrangeHRM\Recruitment\Dto;

use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Recruitment\Service\CandidateService;

class CandidateActionHistory
{
    /**
     * @return int[]
     */
    public function getAccessibleCandidateActionHistoryIds(): array
    {
        return array_keys(
            array_replace(
                CandidateService::STATUS_MAP,
                CandidateService::OTHER_ACTIONS_MAP
            )
        );
    }

    /**
     * @return int[]
     */
    public function getAccessibleCandidateHistoryIdsForInterviewer(): array
    {
        $actionHistory = array_replace(
            CandidateService::STATUS_MAP,
            CandidateService::OTHER_ACTIONS_MAP
        );
        unset(
            $actionHistory[WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_OFFER_JOB],
            $actionHistory[WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_DECLINE_OFFER],
            $actionHistory[WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_HIRE],
            $actionHistory[WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_REJECT]
        );
        return array_keys($actionHistory);
    }
}
