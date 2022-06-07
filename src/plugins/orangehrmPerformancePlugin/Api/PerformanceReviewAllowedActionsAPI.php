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

namespace OrangeHRM\Performance\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Performance\Traits\Service\PerformanceReviewServiceTrait;

class PerformanceReviewAllowedActionsAPI extends Endpoint implements CollectionEndpoint
{
    use AuthUserTrait;
    use UserRoleManagerTrait;
    use PerformanceReviewServiceTrait;

    public const PARAMETER_REVIEW_ID = 'reviewId';

    public const STATE_INITIAL = 'INITIAL';

    public const ACTIONABLE_STATES_MAP = [
        WorkflowStateMachine::REVIEW_INACTIVE_SAVE => 'Save',
        WorkflowStateMachine::REVIEW_ACTIVATE => 'Activate',
        WorkflowStateMachine::REVIEW_IN_PROGRESS_SAVE => 'Save',
        WorkflowStateMachine::REVIEW_COMPLETE => 'Complete'
    ];

    public const WORKFLOW_STATES_MAP = [
        WorkflowStateMachine::REVIEW_INACTIVE_SAVE => 'SAVED',
        WorkflowStateMachine::REVIEW_ACTIVATE => 'ACTIVATED',
        WorkflowStateMachine::REVIEW_IN_PROGRESS_SAVE => 'IN PROGRESS',
        WorkflowStateMachine::REVIEW_COMPLETE => 'COMPLETED'
    ];

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $reviewId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_REVIEW_ID
        );

        $performanceReview = $this->getPerformanceReviewService()
            ->getPerformanceReviewDao()
            ->getPerformanceReviewById($reviewId);

        $currentState = is_null($performanceReview) ? self::STATE_INITIAL : self::WORKFLOW_STATES_MAP[$this->getPerformanceReviewStatus($performanceReview)];

        $allowedWorkflowItems = $this->getUserRoleManager()->getAllowedActions(
            WorkflowStateMachine::FLOW_REVIEW,
            $currentState
        );

        ksort($allowedWorkflowItems);

        $actionableStates = array_map(
            function ($workflow) {
                $actionableState['id'] = $workflow->getAction();
                $actionableState['label'] = self::ACTIONABLE_STATES_MAP[$workflow->getAction()];
                return $actionableState;
            },
            $allowedWorkflowItems
        );

        return new EndpointCollectionResult(
            ArrayModel::class,
            $actionableStates,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => count($actionableStates)])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_REVIEW_ID,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::ENTITY_ID_EXISTS, [PerformanceReview::class])
            )
        );
    }

    /**
     * @param PerformanceReview $performanceReview
     * @return int
     */
    private function getPerformanceReviewStatus(PerformanceReview $performanceReview): int
    {
        if ($this->getAuthUser()->getEmpNumber() === $performanceReview->getEmployee()->getEmpNumber()) {
            $selfReviewer = $this->getPerformanceReviewService()
                ->getPerformanceReviewDao()
                ->getPerformanceSelfReviewer($performanceReview);
            return $selfReviewer->getStatus() + 1;
        }
        return $performanceReview->getStatusId();
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }
}
