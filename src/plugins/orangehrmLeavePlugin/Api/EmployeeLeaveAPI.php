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

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Leave\Api\Model\EmployeeLeaveModel;
use OrangeHRM\Leave\Dto\EmployeeLeaveSearchFilterParams;
use OrangeHRM\Leave\Dto\LeaveSearchFilterParams;
use OrangeHRM\Leave\Traits\Service\LeaveRequestServiceTrait;

class EmployeeLeaveAPI extends Endpoint implements CollectionEndpoint
{
    use LeaveRequestServiceTrait;
    use AuthUserTrait;

    public const  PARAMETER_FROM_DATE = 'fromDate';
    public const PARAMETER_TO_DATE = 'toDate';
    public const FILTER_STATUSES = 'statuses';
    public const FILTER_INCLUDE_EMPLOYEES = 'includeEmployees';

    /**
     * @OA\Get(
     *     path="/api/v2/leave/employees/leaves",
     *     tags={"Leave/Employee Leave"},
     *     summary="List All Employee Leaves",
     *     operationId="list-all-employee-leaves",
     *     @OA\Parameter(
     *         name="empNumber",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="fromDate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date-time")
     *     ),
     *     @OA\Parameter(
     *         name="toDate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date-time")
     *     ),
     *     @OA\Parameter(
     *         name="includeEmployees",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=EmployeeLeaveSearchFilterParams::INCLUDE_EMPLOYEES)
     *     ),
     *     @OA\Parameter(
     *         name="statuses",
     *         in="query",
     *         required=false,
     *         description="-1 => rejected, 0 => cancelled, 1 => pending, 2 => approved, 3 => taken",
     *         @OA\Schema(type="integer", enum=EmployeeLeaveSearchFilterParams::LEAVE_STATUSES)
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=LeaveSearchFilterParams::ALLOWED_SORT_FIELDS)
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
     *                 @OA\Items(ref="#/components/schemas/Leave-EmployeeLeaveModel")
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $employeeNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_QUERY,
            CommonParams::PARAMETER_EMP_NUMBER,
            $this->getAuthUser()->getEmpNumber(),
        );

        $fromDate = $this->getRequestParams()->getDateTimeOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_FROM_DATE
        );

        $toDate = $this->getRequestParams()->getDateTimeOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_TO_DATE
        );

        if ($fromDate != null && $toDate != null && $fromDate > $toDate) {
            throw $this->getInvalidParamException(["fromDate","toDate"]);
        }

        $leaveSearchFilterParams = new EmployeeLeaveSearchFilterParams();
        $leaveSearchFilterParams->setEmpNumber($employeeNumber);
        $leaveSearchFilterParams->setFromDate($fromDate);
        $leaveSearchFilterParams->setToDate($toDate);
        $leaveSearchFilterParams->setIncludeEmployees(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_INCLUDE_EMPLOYEES,
                $this->getDefaultIncludeEmployees()
            )
        );
        $leaveSearchFilterParams->setStatuses(
            $this->getRequestParams()->getArray(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_STATUSES,
                $this->getDefaultStatuses()
            )
        );
        $this->setSortingAndPaginationParams($leaveSearchFilterParams);

        $leaves = $this->getLeaveRequestService()
            ->getLeaveRequestDao()
            ->getEmployeeLeaves($leaveSearchFilterParams);
        $count = $this->getLeaveRequestService()->getLeaveRequestDao()
            ->getEmployeeLeavesCount($leaveSearchFilterParams);

        return new EndpointCollectionResult(
            EmployeeLeaveModel::class,
            $leaves,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
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
                    new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS, [true])
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_FROM_DATE,
                    new Rule(Rules::API_DATE)
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_TO_DATE,
                    new Rule(Rules::API_DATE)
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_INCLUDE_EMPLOYEES,
                    new Rule(Rules::IN, [EmployeeLeaveSearchFilterParams::INCLUDE_EMPLOYEES])
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_STATUSES,
                    new Rule(Rules::ARRAY_TYPE),
                    new Rule(
                        Rules::EACH,
                        [
                            new Rules\Composite\AllOf(
                                new Rule(Rules::IN, [EmployeeLeaveSearchFilterParams::LEAVE_STATUSES])
                            )
                        ]
                    )
                )
            ),
            ...$this->getSortingAndPaginationParamsRules(LeaveSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @return string
     */
    protected function getDefaultIncludeEmployees(): string
    {
        return EmployeeLeaveSearchFilterParams::INCLUDE_EMPLOYEES_ONLY_CURRENT;
    }

    /**
     * @return string[]
     */
    protected function getDefaultStatuses(): array
    {
        return [Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL];
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
