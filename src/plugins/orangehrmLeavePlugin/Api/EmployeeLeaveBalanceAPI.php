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

namespace OrangeHRM\Leave\Api;

use DateTime;
use OpenApi\Annotations as OA;
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
use OrangeHRM\Leave\Dto\EmployeeLeaveBalanceSearchFilterParams;
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
     * @OA\Get(
     *     path="/api/v2/leave/employees/leave-balances",
     *     tags={"Leave/Leave Balance"},
     *     summary="Get an Employee's Leave Balance",
     *     operationId="get-an-employees-leave-balance",
     *     @OA\Parameter(
     *         name="empNumber",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="fromDate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="toDate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=EmployeeLeaveBalanceSearchFilterParams::ALLOWED_SORT_FIELDS)
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/sortOrder"),
     *     @OA\Parameter(ref="#/components/parameters/limit"),
     *     @OA\Parameter(ref="#/components/parameters/offset"),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Leave-EmployeeLeaveBalanceModel")
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="empNumber", type="integer")
     *             )
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_QUERY,
            CommonParams::PARAMETER_EMP_NUMBER,
            $this->getAuthUser()->getEmpNumber()
        );
        list($fromDate, $toDate) = $this->getFromAndToDates();

        $searchFilterParams = new EmployeeLeaveBalanceSearchFilterParams();
        $this->setSortingAndPaginationParams($searchFilterParams);
        $searchFilterParams->setFromDate($fromDate);
        $searchFilterParams->setToDate($toDate);
        $searchFilterParams->setEmpNumber($empNumber);

        $leaveTypesResult = $this->getEmployeeLeaveBalancesResult($searchFilterParams);
        $leaveTypeCount = $this->getLeaveTypeService()
            ->getLeaveTypeDao()
            ->getSearchLeaveTypesCount($searchFilterParams);

        return new EndpointCollectionResult(
            ArrayModel::class,
            $leaveTypesResult,
            new ParameterBag([
                CommonParams::PARAMETER_TOTAL => $leaveTypeCount,
                CommonParams::PARAMETER_EMP_NUMBER => $empNumber
            ])
        );
    }

    /**
     * @OA\Schema(
     *     schema="Leave-EmployeeLeaveBalanceModel",
     *     type="object",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(
     *         property="usageBreakdown",
     *         type="object",
     *         nullable=true,
     *         @OA\Property(property="entitlement", type="number"),
     *         @OA\Property(property="used", type="number"),
     *         @OA\Property(property="scheduled", type="number"),
     *         @OA\Property(property="pending", type="number"),
     *         @OA\Property(property="taken", type="number"),
     *         @OA\Property(property="balance", type="number"),
     *     ),
     *     @OA\Property(
     *         property="leaveType",
     *         type="object",
     *         @OA\Property(property="id", type="integer"),
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="deleted", type="boolean")
     *     ),
     *     @OA\Property(property="fromDate", type="string", format="date"),
     *     @OA\Property(property="toDate", type="string", format="date"),
     * )
     *
     * @param EmployeeLeaveBalanceSearchFilterParams $searchFilterParams
     * @return array
     */
    private function getEmployeeLeaveBalancesResult(EmployeeLeaveBalanceSearchFilterParams $searchFilterParams): array
    {
        $leaveTypes = $this->getLeaveTypeService()
            ->getLeaveTypeDao()
            ->searchLeaveType($searchFilterParams);

        $empNumber = $searchFilterParams->getEmpNumber();
        $leaveTypeIds = array_unique(
            array_merge(
                $this->getLeaveRequestService()
                    ->getLeaveRequestDao()
                    ->getUsedLeaveTypeIdsByEmployee(
                        $empNumber,
                        $searchFilterParams->getFromDate(),
                        $searchFilterParams->getToDate()
                    ),
                $this->getLeaveEntitlementService()
                    ->getLeaveEntitlementDao()
                    ->getLeaveTypeIdsForEntitlementsByEmployee(
                        $empNumber,
                        $searchFilterParams->getFromDate(),
                        $searchFilterParams->getToDate()
                    )
            )
        );

        $leaveTypesResult = [];
        foreach ($leaveTypes as $leaveType) {
            $balanceArray = null;
            $balance = null;
            if (in_array($leaveType->getId(), $leaveTypeIds)) {
                $balance = $this->getLeaveEntitlementService()
                    ->getLeaveBalance(
                        $empNumber,
                        $leaveType->getId(),
                        $searchFilterParams->getFromDate(),
                        $searchFilterParams->getToDate()
                    );
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
        return $leaveTypesResult;
    }

    /**
     * @return DateTime[]
     */
    private function getFromAndToDates(): array
    {
        if ($this->getRequestParams()->has(RequestParams::PARAM_TYPE_QUERY, LeaveCommonParams::PARAMETER_FROM_DATE) xor
            $this->getRequestParams()->has(RequestParams::PARAM_TYPE_QUERY, LeaveCommonParams::PARAMETER_TO_DATE)) {
            throw $this->getInvalidParamException([
                LeaveCommonParams::PARAMETER_FROM_DATE,
                LeaveCommonParams::PARAMETER_TO_DATE
            ]);
        }
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
            if ($fromDate > $toDate) {
                throw $this->getInvalidParamException([
                    LeaveCommonParams::PARAMETER_FROM_DATE,
                    LeaveCommonParams::PARAMETER_TO_DATE
                ]);
            }
        }

        return [$fromDate, $toDate];
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
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(LeaveCommonParams::PARAMETER_FROM_DATE, new Rule(Rules::API_DATE))
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(LeaveCommonParams::PARAMETER_TO_DATE, new Rule(Rules::API_DATE))
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
