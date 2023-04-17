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
use OrangeHRM\Leave\Dto\LeaveTypeSearchFilterParams;
use OrangeHRM\Leave\Entitlement\LeaveBalance;
use OrangeHRM\Leave\Traits\Service\LeaveEntitlementServiceTrait;
use OrangeHRM\Leave\Traits\Service\LeavePeriodServiceTrait;
use OrangeHRM\Leave\Traits\Service\LeaveRequestServiceTrait;
use OrangeHRM\Leave\Traits\Service\LeaveTypeServiceTrait;

class EmployeeLeaveBalanceAPI extends Endpoint implements CollectionEndpoint
{
    use LeaveEntitlementServiceTrait;
    use LeaveRequestServiceTrait;
    use LeavePeriodServiceTrait;
    use LeaveTypeServiceTrait;
    use AuthUserTrait;

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_QUERY,
            CommonParams::PARAMETER_EMP_NUMBER,
            $this->getAuthUser()->getEmpNumber()
        );

        if (!$this->getRequestParams()->has(RequestParams::PARAM_TYPE_QUERY, LeaveCommonParams::PARAMETER_FROM_DATE) ||
            !$this->getRequestParams()->has(RequestParams::PARAM_TYPE_QUERY, LeaveCommonParams::PARAMETER_TO_DATE)) {
            $currentLeavePeriod = $this->getLeavePeriodService()->getCurrentLeavePeriod();
            $fromDate = $this->getRequestParams()->getDateTime(
                RequestParams::PARAM_TYPE_QUERY,
                LeaveCommonParams::PARAMETER_FROM_DATE,
                null,
                $currentLeavePeriod->getStartDate()
            );
            $toDate = $this->getRequestParams()->getDateTime(
                RequestParams::PARAM_TYPE_QUERY,
                LeaveCommonParams::PARAMETER_TO_DATE,
                null,
                $currentLeavePeriod->getEndDate()
            );
        } else {
            $fromDate = $this->getRequestParams()->getDateTime(
                RequestParams::PARAM_TYPE_QUERY,
                LeaveCommonParams::PARAMETER_FROM_DATE,
            );
            $toDate = $this->getRequestParams()->getDateTime(
                RequestParams::PARAM_TYPE_QUERY,
                LeaveCommonParams::PARAMETER_TO_DATE,
            );
        }

        $leaveTypeSearchFilterParams = new LeaveTypeSearchFilterParams();
        $this->setSortingAndPaginationParams($leaveTypeSearchFilterParams);

        $leaveTypes = $this->getLeaveTypeService()
            ->getLeaveTypeDao()
            ->searchLeaveType($leaveTypeSearchFilterParams);
        $leaveTypeCount = $this->getLeaveTypeService()->getLeaveTypeDao()
            ->getSearchLeaveTypesCount($leaveTypeSearchFilterParams);

        $leaveTypeIds = array_unique(
            array_merge(
                $this->getLeaveRequestService()
                    ->getLeaveRequestDao()
                    ->getUsedLeaveTypeIdsByEmployee($empNumber),
                $this->getLeaveEntitlementService()
                    ->getLeaveEntitlementDao()
                    ->getLeaveTypeIdsForEntitlementsByEmployee($empNumber)
            )
        );

        $leaveTypesResult = [];
        foreach ($leaveTypes as $leaveType) {
            $balanceArray = null;
            $balance = null;
            if (in_array($leaveType->getId(), $leaveTypeIds)) {
                $balance = $this->getLeaveEntitlementService()
                    ->getLeaveBalance($empNumber, $leaveType->getId(), $fromDate, $toDate);
                $balanceArray = [
                    'entitled' => $balance->getEntitled(),
                    'used' => $balance->getUsed(),
                    'scheduled' => $balance->getScheduled(),
                    'pending' => $balance->getPending(),
                    'taken' => $balance->getTaken(),
                    'balance' => $balance->getBalance(),
                ];
            }

            $leaveTypeResult = [
                'leaveType' => [
                    'id' => $leaveType->getId(),
                    'name' => $leaveType->getName(),
                    'deleted' => $leaveType->isDeleted(),
                ],
                'usageBreakdown' => $balanceArray,
                'fromDate' => $balance instanceof LeaveBalance ? $balance->getYmdAsAtDate() : null,
                'toDate' => $balance instanceof LeaveBalance ? $balance->getYmdEndDate() : null,
            ];
            $leaveTypesResult[] = $leaveTypeResult;
        }
        return new EndpointCollectionResult(
            ArrayModel::class,
            $leaveTypesResult,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $leaveTypeCount])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    CommonParams::PARAMETER_EMP_NUMBER,
                    new Rule(Rules::POSITIVE),
                    new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
                )
            ),
            ...$this->getSortingAndPaginationParamsRules(LeaveTypeSearchFilterParams::ALLOWED_SORT_FIELDS),
        );
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
