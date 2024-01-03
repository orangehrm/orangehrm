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

namespace OrangeHRM\Recruitment\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Entity\Candidate;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Core\Controller\Common\NoRecordsFoundController;
use OrangeHRM\Core\Controller\Exception\RequestForwardableException;
use OrangeHRM\Recruitment\Traits\Service\CandidateServiceTrait;

class CandidateActionController extends AbstractVueController
{
    use CandidateServiceTrait;
    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $candidateId = $request->query->getInt('candidateId');
        $actionId = $request->query->getInt('selectedAction');
        $candidate = $this->getCandidateService()->getCandidateDao()->getCandidateById($candidateId);
        if (!$candidate instanceof Candidate) {
            throw new RequestForwardableException(NoRecordsFoundController::class . '::handle');
        }

        switch ($actionId) {
            case WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHORTLIST:
                $component = new Component('shortlist-action');
                break;
            case WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_REJECT:
                $component = new Component('reject-action');
                break;
            case WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHEDULE_INTERVIEW:
                $component = new Component('interview-schedule-action');
                break;
            case WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_MARK_INTERVIEW_PASSED:
                $component = new Component('interview-passed-action');
                break;
            case WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_MARK_INTERVIEW_FAILED:
                $component = new Component('interview-failed-action');
                break;
            case WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_OFFER_JOB:
                $component = new Component('offer-job-action');
                break;
            case WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_DECLINE_OFFER:
                $component = new Component('offer-decline-action');
                break;
            case WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_HIRE:
                $component = new Component('hire-action');
                break;
            default:
                throw new RequestForwardableException(NoRecordsFoundController::class . '::handle');
        }

        if (
            $actionId === WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_MARK_INTERVIEW_PASSED
            || $actionId === WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_MARK_INTERVIEW_FAILED
        ) {
            $interviewIds = $this->getCandidateService()->getCandidateDao()->getInterviewIdsByCandidateId($candidateId);
            $component->addProp(new Prop('interview-id', Prop::TYPE_NUMBER, $interviewIds[0]));
        }

        $component->addProp(new Prop('candidate-id', Prop::TYPE_NUMBER, $candidateId));
        $this->setComponent($component);
    }
}
