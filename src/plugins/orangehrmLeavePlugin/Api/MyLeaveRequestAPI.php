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

namespace OrangeHRM\Leave\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Entity\LeaveRequest;
use OrangeHRM\Leave\Api\Model\LeaveRequestDetailedModel;
use OrangeHRM\Leave\Dto\LeaveRequest\DetailedLeaveRequest;
use OrangeHRM\Leave\Dto\LeaveRequestSearchFilterParams;
use OrangeHRM\Leave\Exception\LeaveAllocationServiceException;
use OrangeHRM\Leave\Service\LeaveApplicationService;
use OrangeHRM\Leave\Traits\Service\LeaveRequestServiceTrait;

class MyLeaveRequestAPI extends EmployeeLeaveRequestAPI
{
    use AuthUserTrait;
    use LeaveRequestServiceTrait;

    protected ?LeaveApplicationService $leaveApplicationService = null;

    /**
     * @return LeaveApplicationService
     */
    public function getLeaveApplicationService(): LeaveApplicationService
    {
        if (!$this->leaveApplicationService instanceof LeaveApplicationService) {
            $this->leaveApplicationService = new LeaveApplicationService();
        }
        return $this->leaveApplicationService;
    }

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $empNumber = $this->getAuthUser()->getEmpNumber();
        $leaveRequestSearchFilterParams = $this->getLeaveRequestSearchFilterParams($empNumber);
        $leaveRequests = $this->getLeaveRequestService()
            ->getLeaveRequestDao()
            ->getLeaveRequests($leaveRequestSearchFilterParams);
        $total = $this->getLeaveRequestService()
            ->getLeaveRequestDao()
            ->getLeaveRequestsCount($leaveRequestSearchFilterParams);
        $detailedLeaveRequests = $this->getLeaveRequestService()->getDetailedLeaveRequests($leaveRequests);

        return new EndpointCollectionResult(
            LeaveRequestDetailedModel::class,
            $detailedLeaveRequests,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    CommonParams::PARAMETER_TOTAL => $total
                ]
            )
        );
    }

    /**
     * @return string
     */
    protected function getDefaultIncludeEmployees(): string
    {
        return LeaveRequestSearchFilterParams::INCLUDE_EMPLOYEES_CURRENT_AND_PAST;
    }

    /**
     * @return string[]
     */
    protected function getDefaultStatuses(): array
    {
        return LeaveRequestSearchFilterParams::LEAVE_STATUSES;
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return $this->getCommonFilterParamRuleCollection();
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $empNumber = $this->getAuthUser()->getEmpNumber();
        $leaveRequestParams = $this->getLeaveRequestParams($empNumber);
        try {
            $leaveRequest = $this->getLeaveApplicationService()->applyLeave($leaveRequestParams);
            $model = $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_MODEL,
                self::MODEL_DEFAULT
            );
            if ($model === self::MODEL_DETAILED) {
                $data = new DetailedLeaveRequest($leaveRequest);
                $data->fetchLeaves();
            } else {
                $data = $leaveRequest;
            }
            return new EndpointResourceResult(
                self::MODEL_MAP[$model],
                $data,
                new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $empNumber])
            );
        } catch (LeaveAllocationServiceException $e) {
            throw $this->getBadRequestException($e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        $paramRules = $this->getCommonParamRuleCollection();
        $paramRules->addParamValidation(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::FILTER_MODEL, new Rule(Rules::IN, [array_keys(self::MODEL_MAP)])),
            )
        );
        return $paramRules;
    }

    /**
     * @inheritDoc
     */
    public function checkLeaveRequestAccessible(LeaveRequest $leaveRequest): void
    {
        $empNumber = $leaveRequest->getEmployee()->getEmpNumber();
        if (!$this->getUserRoleManagerHelper()->isSelfByEmpNumber($empNumber)) {
            throw $this->getForbiddenException();
        }
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
