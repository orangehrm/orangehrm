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
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Leave\Api\Model\LeaveRequestDetailedModel;
use OrangeHRM\Leave\Api\Model\LeaveRequestModel;
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
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection();
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $empNumber = $this->getAuthUser()->getEmpNumber();
        $leaveRequestParams = $this->getLeaveRequestParams($empNumber);
        $leaveRequest = $this->getLeaveApplicationService()->applyLeave($leaveRequestParams);
        return new EndpointResourceResult(
            LeaveRequestModel::class,
            $leaveRequest,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $empNumber])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return $this->getCommonParamRuleCollection();
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
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
