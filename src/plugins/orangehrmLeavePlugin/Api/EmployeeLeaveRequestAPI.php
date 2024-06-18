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
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
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
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Entity\LeaveRequest;
use OrangeHRM\Entity\Subunit;
use OrangeHRM\Leave\Api\Model\LeaveRequestDetailedModel;
use OrangeHRM\Leave\Api\Model\LeaveRequestModel;
use OrangeHRM\Leave\Api\Traits\LeaveRequestParamHelperTrait;
use OrangeHRM\Leave\Api\Traits\LeaveRequestPermissionTrait;
use OrangeHRM\Leave\Api\ValidationRules\LeaveTypeIdRule;
use OrangeHRM\Leave\Dto\LeaveRequest\DetailedLeaveRequest;
use OrangeHRM\Leave\Dto\LeaveRequestSearchFilterParams;
use OrangeHRM\Leave\Exception\LeaveAllocationServiceException;
use OrangeHRM\Leave\Service\LeaveAssignmentService;
use OrangeHRM\Leave\Traits\Service\LeaveRequestServiceTrait;

class EmployeeLeaveRequestAPI extends Endpoint implements CrudEndpoint
{
    use LeaveRequestParamHelperTrait;
    use LeaveRequestServiceTrait;
    use UserRoleManagerTrait;
    use AuthUserTrait;
    use LeaveRequestPermissionTrait;

    public const PARAMETER_ACTION = 'action';
    public const PARAMETER_LEAVE_REQUEST_ID = 'leaveRequestId';

    public const FILTER_SUBUNIT_ID = 'subunitId';
    public const FILTER_STATUSES = 'statuses';
    public const FILTER_INCLUDE_EMPLOYEES = 'includeEmployees';
    public const FILTER_MODEL = 'model';

    public const MODEL_DEFAULT = 'default';
    public const MODEL_DETAILED = 'detailed';
    public const MODEL_MAP = [
        self::MODEL_DEFAULT => LeaveRequestModel::class,
        self::MODEL_DETAILED => LeaveRequestDetailedModel::class,
    ];

    protected ?LeaveAssignmentService $leaveAssignmentService = null;

    /**
     * @return LeaveAssignmentService
     */
    protected function getLeaveAssignmentService(): LeaveAssignmentService
    {
        if (!$this->leaveAssignmentService instanceof LeaveAssignmentService) {
            $this->leaveAssignmentService = new LeaveAssignmentService();
        }
        return $this->leaveAssignmentService;
    }

    /**
     * @OA\Get(
     *     path="/api/v2/leave/employees/leave-requests/{leaveRequestId}",
     *     tags={"Leave/Employee Leave Request"},
     *     summary="Get a Leave Request",
     *     operationId="get-a-leave-request",
     *     @OA\PathParameter(
     *         name="leaveRequestId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="model",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={
     *                 OrangeHRM\Leave\Api\EmployeeLeaveRequestAPI::MODEL_DEFAULT,
     *                 OrangeHRM\Leave\Api\EmployeeLeaveRequestAPI::MODEL_DETAILED
     *             },
     *             default=OrangeHRM\Leave\Api\EmployeeLeaveRequestAPI::MODEL_DEFAULT
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 oneOf={
     *                     @OA\Schema(ref="#/components/schemas/Leave-LeaveRequestModel"),
     *                     @OA\Schema(ref="#/components/schemas/Leave-LeaveRequestDetailedModel"),
     *                 }
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     * )
     *
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $leaveRequestId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_LEAVE_REQUEST_ID
        );
        $leaveRequest = $this->getLeaveRequestService()->getLeaveRequestDao()->getLeaveRequestById($leaveRequestId);
        $this->throwRecordNotFoundExceptionIfNotExist($leaveRequest, LeaveRequest::class);
        $this->checkLeaveRequestAccessible($leaveRequest);

        $model = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_MODEL,
            self::MODEL_DEFAULT
        );
        if ($model === self::MODEL_DETAILED) {
            $detailedLeaveRequest = new DetailedLeaveRequest($leaveRequest);
            $detailedLeaveRequest->fetchLeaves();
            $leaveRequest = $detailedLeaveRequest;
        }
        return new EndpointResourceResult(self::MODEL_MAP[$model], $leaveRequest);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(self::PARAMETER_LEAVE_REQUEST_ID, new Rule(Rules::POSITIVE)),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::FILTER_MODEL, new Rule(Rules::IN, [array_keys(self::MODEL_MAP)])),
            ),
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v2/leave/employees/leave-requests",
     *     tags={"Leave/Employee Leave Request"},
     *     summary="List All Leave Requests",
     *     operationId="list-all-leave-requests",
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
     *         @OA\Schema(type="string", enum=LeaveRequestSearchFilterParams::ALLOWED_SORT_FIELDS)
     *     ),
     *     @OA\Parameter(
     *         name="includeEmployees",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=LeaveRequestSearchFilterParams::INCLUDE_EMPLOYEES)
     *     ),
     *     @OA\Parameter(
     *         name="subunitId",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="empNumber",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="statuses",
     *         in="query",
     *         required=false,
     *         description="-1 => rejected, 0 => cancelled, 1 => pending, 2 => approved, 3 => taken",
     *         @OA\Schema(type="integer", enum=LeaveRequestSearchFilterParams::LEAVE_STATUSES)
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
     *                 ref="#/components/schemas/Leave-LeaveRequestDetailedModel",
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $empNumber = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $leaveRequestSearchFilterParams = $this->getLeaveRequestSearchFilterParams($empNumber);
        if (is_null($empNumber)) {
            $accessibleEmpNumbers = $this->getUserRoleManager()->getAccessibleEntityIds(Employee::class);
            $leaveRequestSearchFilterParams->setEmpNumbers($accessibleEmpNumbers);
        }
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
                    CommonParams::PARAMETER_TOTAL => $total
                ]
            )
        );
    }

    /**
     * @param int|null $empNumber
     * @return LeaveRequestSearchFilterParams
     */
    protected function getLeaveRequestSearchFilterParams(?int $empNumber = null): LeaveRequestSearchFilterParams
    {
        $leaveRequestSearchFilterParams = new LeaveRequestSearchFilterParams();
        $leaveRequestSearchFilterParams->setEmpNumber($empNumber);
        $this->setSortingAndPaginationParams($leaveRequestSearchFilterParams);

        // TODO leave period start date
        $leaveRequestSearchFilterParams->setFromDate(
            $this->getRequestParams()->getDateTimeOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                LeaveCommonParams::PARAMETER_FROM_DATE
            )
        );
        // TODO leave period end date
        $leaveRequestSearchFilterParams->setToDate(
            $this->getRequestParams()->getDateTimeOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                LeaveCommonParams::PARAMETER_TO_DATE
            )
        );
        $leaveRequestSearchFilterParams->setIncludeEmployees(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_INCLUDE_EMPLOYEES,
                $this->getDefaultIncludeEmployees()
            )
        );
        $leaveRequestSearchFilterParams->setStatuses(
            $this->getRequestParams()->getArray(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_STATUSES,
                $this->getDefaultStatuses()
            )
        );
        $leaveRequestSearchFilterParams->setSubunitId(
            $this->getRequestParams()->getIntOrNull(RequestParams::PARAM_TYPE_QUERY, self::FILTER_SUBUNIT_ID)
        );
        $leaveRequestSearchFilterParams->setLeaveTypeId(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID
            )
        );
        return $leaveRequestSearchFilterParams;
    }

    /**
     * @return string
     */
    protected function getDefaultIncludeEmployees(): string
    {
        return LeaveRequestSearchFilterParams::INCLUDE_EMPLOYEES_ONLY_CURRENT;
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
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        $paramRules = $this->getCommonFilterParamRuleCollection();
        $paramRules->addParamValidation(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    CommonParams::PARAMETER_EMP_NUMBER,
                    new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
                )
            )
        );
        return $paramRules;
    }

    /**
     * @return ParamRuleCollection
     */
    protected function getCommonFilterParamRuleCollection(): ParamRuleCollection
    {
        $paramRules = new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_INCLUDE_EMPLOYEES,
                    new Rule(Rules::IN, [LeaveRequestSearchFilterParams::INCLUDE_EMPLOYEES])
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_SUBUNIT_ID,
                    new Rule(Rules::ENTITY_ID_EXISTS, [Subunit::class])
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID,
                    new Rule(Rules::INT_VAL),
                    new Rule(Rules::POSITIVE),
                    new Rule(LeaveTypeIdRule::class)
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
                                new Rule(Rules::IN, [LeaveRequestSearchFilterParams::LEAVE_STATUSES])
                            )
                        ]
                    )
                )
            ),
            ...$this->getSortingAndPaginationParamsRules(LeaveRequestSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
        $fromDateRule = new ParamRule(
            LeaveCommonParams::PARAMETER_FROM_DATE,
            new Rule(Rules::API_DATE),
            new Rule(
                Rules::LESS_THAN_OR_EQUAL,
                [
                    fn () => $this->getRequestParams()->getDateTimeOrNull(
                        RequestParams::PARAM_TYPE_QUERY,
                        LeaveCommonParams::PARAMETER_TO_DATE
                    )
                ]
            )
        );
        $toDateRule = new ParamRule(
            LeaveCommonParams::PARAMETER_TO_DATE,
            new Rule(Rules::API_DATE)
        );

        $paramRules->addParamValidation(
            $this->getRequestParams()->has(
                RequestParams::PARAM_TYPE_QUERY,
                LeaveCommonParams::PARAMETER_TO_DATE
            ) ? $fromDateRule : $this->getValidationDecorator()->notRequiredParamRule($fromDateRule)
        );
        $paramRules->addParamValidation(
            $this->getRequestParams()->has(
                RequestParams::PARAM_TYPE_QUERY,
                LeaveCommonParams::PARAMETER_FROM_DATE
            ) ? $toDateRule : $this->getValidationDecorator()->notRequiredParamRule($toDateRule)
        );
        return $paramRules;
    }

    /**
     * @OA\Post(
     *     path="/api/v2/leave/employees/leave-requests",
     *     tags={"Leave/Employee Leave Request"},
     *     summary="Create a Leave Request",
     *     operationId="create-a-leave-request",
     *     @OA\Parameter(
     *         name="model",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={
     *                 OrangeHRM\Leave\Api\EmployeeLeaveRequestAPI::MODEL_DEFAULT,
     *                 OrangeHRM\Leave\Api\EmployeeLeaveRequestAPI::MODEL_DETAILED
     *             },
     *             default=OrangeHRM\Leave\Api\EmployeeLeaveRequestAPI::MODEL_DEFAULT
     *         )
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             required={"empNumber", "leaveTypeId", "fromDate", "toDate", "duration"},
     *             @OA\Property(property="empNumber", type="integer"),
     *             @OA\Property(property="leaveTypeId", type="integer"),
     *             @OA\Property(property="fromDate", type="string", format="date"),
     *             @OA\Property(property="toDate", type="string", format="date"),
     *             @OA\Property(property="comment", type="string"),
     *             @OA\Property(property="partialOption", type="string", enum={"none, all, start, end, start_end"}),
     *             @OA\Property(property="duration", type="object",
     *                 required={"type"},
     *                 @OA\Property(
     *                     property="type",
     *                     type="string",
     *                     enum={
     *                         OrangeHRM\Leave\Dto\LeaveDuration::FULL_DAY,
     *                         OrangeHRM\Leave\Dto\LeaveDuration::HALF_DAY_MORNING,
     *                         OrangeHRM\Leave\Dto\LeaveDuration::HALF_DAY_AFTERNOON,
     *                         OrangeHRM\Leave\Dto\LeaveDuration::SPECIFY_TIME
     *                     }
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 oneOf={
     *                     @OA\Schema(ref="#/components/schemas/Leave-LeaveRequestModel"),
     *                     @OA\Schema(ref="#/components/schemas/Leave-LeaveRequestDetailedModel"),
     *                 }
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     * )
     *
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_BODY,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $leaveRequestParams = $this->getLeaveRequestParams($empNumber);
        try {
            $leaveRequest = $this->getLeaveAssignmentService()->assignLeave($leaveRequestParams);
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
        } catch (LeaveAllocationServiceException $exception) {
            throw $this->getBadRequestException($exception->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        $paramRules = $this->getCommonParamRuleCollection();
        $paramRules->addParamValidation(
            new ParamRule(CommonParams::PARAMETER_EMP_NUMBER, new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)),
        );
        $paramRules->addParamValidation(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::FILTER_MODEL, new Rule(Rules::IN, [array_keys(self::MODEL_MAP)])),
            )
        );

        return $paramRules;
    }

    /**
     * @OA\Put(
     *     path="/api/v2/leave/employees/leave-requests/{leaveRequestId}",
     *     tags={"Leave/Employee Leave Request"},
     *     summary="Update a Leave Request",
     *     operationId="update-a-leave-request",
     *     @OA\PathParameter(
     *         name="leaveRequestId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="model",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={
     *                 OrangeHRM\Leave\Api\EmployeeLeaveRequestAPI::MODEL_DEFAULT,
     *                 OrangeHRM\Leave\Api\EmployeeLeaveRequestAPI::MODEL_DETAILED
     *             },
     *             default=OrangeHRM\Leave\Api\EmployeeLeaveRequestAPI::MODEL_DEFAULT
     *         )
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="action", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 oneOf={
     *                     @OA\Schema(ref="#/components/schemas/Leave-LeaveRequestModel"),
     *                     @OA\Schema(ref="#/components/schemas/Leave-LeaveRequestDetailedModel"),
     *                 }
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound"),
     *     @OA\Response(
     *         response="400",
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="object",
     *                 @OA\Property(property="status", type="string", default="400"),
     *                 @OA\Property(property="message", type="string", example="Leave request has multiple statuses, Performed action not allowed")
     *             )
     *         )
     *     ),
     * )
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $leaveRequestId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_LEAVE_REQUEST_ID
        );
        $leaveRequest = $this->getLeaveRequestService()->getLeaveRequestDao()->getLeaveRequestById($leaveRequestId);
        $this->throwRecordNotFoundExceptionIfNotExist($leaveRequest, LeaveRequest::class);
        $this->checkLeaveRequestAccessible($leaveRequest);

        $detailedLeaveRequest = new DetailedLeaveRequest($leaveRequest);
        $detailedLeaveRequest->fetchLeaves();
        if ($detailedLeaveRequest->hasMultipleStatus()) {
            throw $this->getBadRequestException('Leave request has multiple statuses');
        }

        $action = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_ACTION);
        if (!$detailedLeaveRequest->isActionAllowed($action)) {
            throw $this->getBadRequestException('Performed action not allowed');
        }

        $workflow = $detailedLeaveRequest->getWorkflowForAction($action);
        $this->getLeaveRequestService()->changeLeaveRequestStatus($detailedLeaveRequest, $workflow);

        $model = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_MODEL,
            self::MODEL_DEFAULT
        );
        if ($model === self::MODEL_DETAILED) {
            $detailedLeaveRequest->setLeaves(
                $this->getLeaveRequestService()
                    ->getLeaveRequestDao()
                    ->getLeavesByLeaveRequestId($leaveRequest->getId())
            );
            $leaveRequest = $detailedLeaveRequest;
        }
        return new EndpointResourceResult(self::MODEL_MAP[$model], $leaveRequest);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(self::PARAMETER_LEAVE_REQUEST_ID, new Rule(Rules::POSITIVE)),
            new ParamRule(self::PARAMETER_ACTION, new Rule(Rules::STRING_TYPE)),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::FILTER_MODEL, new Rule(Rules::IN, [array_keys(self::MODEL_MAP)])),
            ),
        );
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
