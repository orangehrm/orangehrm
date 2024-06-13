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
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\LeaveEntitlement;
use OrangeHRM\Leave\Api\Model\LeaveEntitlementModel;
use OrangeHRM\Leave\Api\Model\LeaveEntitlementSummaryModel;
use OrangeHRM\Leave\Api\Traits\LeaveEntitlementPermissionTrait;
use OrangeHRM\Leave\Api\ValidationRules\LeaveTypeIdRule;
use OrangeHRM\Leave\Dto\LeaveEntitlementSearchFilterParams;
use OrangeHRM\Leave\Traits\Service\LeaveEntitlementServiceTrait;
use OrangeHRM\Leave\Traits\Service\LeavePeriodServiceTrait;
use OrangeHRM\Pim\Dto\EmployeeSearchFilterParams;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

class LeaveEntitlementAPI extends Endpoint implements CrudEndpoint
{
    use AuthUserTrait;
    use LeaveEntitlementServiceTrait;
    use LeavePeriodServiceTrait;
    use DateTimeHelperTrait;
    use EmployeeServiceTrait;
    use LeaveEntitlementPermissionTrait;

    public const PARAMETER_BULK_ASSIGN = 'bulkAssign';
    public const PARAMETER_ENTITLEMENT = 'entitlement';
    public const PARAMETER_LOCATION_ID = 'locationId';
    public const PARAMETER_SUBUNIT_ID = 'subunitId';
    public const FILTER_MODEL = 'model';
    public const FILTER_LEAVE_TYPE_DELETED = 'leaveTypeDeleted';

    public const META_PARAMETER_SUM = 'sum';
    public const META_PARAMETER_COUNT = 'count';

    public const MODEL_DEFAULT = 'default';
    public const MODEL_SUMMARY = 'summary';

    public const MODEL_MAP = [
        self::MODEL_DEFAULT => LeaveEntitlementModel::class,
        self::MODEL_SUMMARY => LeaveEntitlementSummaryModel::class,
    ];

    /**
     * @OA\Get(
     *     path="/api/v2/leave/leave-entitlements/{id}",
     *     tags={"Leave/Entitlements"},
     *     summary="Get a Leave Entitlement",
     *     operationId="get-a-leave-entitlement",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="model",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={\OrangeHRM\Leave\Api\LeaveEntitlementAPI::MODEL_DEFAULT, \OrangeHRM\Leave\Api\LeaveEntitlementAPI::MODEL_SUMMARY},
     *             default=\OrangeHRM\Leave\Api\LeaveEntitlementAPI::MODEL_DEFAULT
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 oneOf={
     *                     @OA\Schema(ref="#/components/schemas/Leave-LeaveEntitlementModel"),
     *                     @OA\Schema(ref="#/components/schemas/Leave-LeaveEntitlementSummaryModel"),
     *                 }
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $leaveEntitlement = $this->getLeaveEntitlementService()
            ->getLeaveEntitlementDao()
            ->getLeaveEntitlement($this->getIdUrlAttribute());
        $this->throwRecordNotFoundExceptionIfNotExist($leaveEntitlement, LeaveEntitlement::class);
        $this->checkLeaveEntitlementAccessible($leaveEntitlement);
        return new EndpointResourceResult(
            $this->getModelClass(),
            $leaveEntitlement,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $leaveEntitlement->getEmployee()->getEmpNumber()])
        );
    }

    /**
     * @return int
     */
    private function getIdUrlAttribute(): int
    {
        return $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getIdParamRule(),
            $this->getModelClassParamRule(),
        );
    }

    /**
     * @return ParamRule
     */
    private function getIdParamRule(): ParamRule
    {
        return new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::POSITIVE));
    }

    /**
     * @OA\Get(
     *     path="/api/v2/leave/leave-entitlements",
     *     tags={"Leave/Entitlements"},
     *     summary="List All Leave Entitlements",
     *     operationId="list-all-leave-entitlements",
     *     @OA\Parameter(
     *         name="empNumber",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="leaveTypeId",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="fromDate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="toDate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="leaveTypeDeleted",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=LeaveEntitlementSearchFilterParams::ALLOWED_SORT_FIELDS)
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/sortOrder"),
     *     @OA\Parameter(ref="#/components/parameters/limit"),
     *     @OA\Parameter(ref="#/components/parameters/offset"),
     *     @OA\Parameter(
     *         name="model",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={\OrangeHRM\Leave\Api\LeaveEntitlementAPI::MODEL_DEFAULT, \OrangeHRM\Leave\Api\LeaveEntitlementAPI::MODEL_SUMMARY},
     *             default=\OrangeHRM\Leave\Api\LeaveEntitlementAPI::MODEL_DEFAULT
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(oneOf={
     *                     @OA\Schema(ref="#/components/schemas/Leave-LeaveEntitlementModel"),
     *                     @OA\Schema(ref="#/components/schemas/Leave-LeaveEntitlementSummaryModel"),
     *                 })
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="number"),
     *                 @OA\Property(property="sum", type="number"),
     *                 @OA\Property(property="fromDate", type="string"),
     *                 @OA\Property(property="toDate", type="string"),
     *                 @OA\Property(property="empNumber", type="integer"),
     *             )
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
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
        $leaveTypeId = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID
        );
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

        $entitlementSearchFilterParams = new LeaveEntitlementSearchFilterParams();
        $entitlementSearchFilterParams->setEmpNumber($empNumber);
        $entitlementSearchFilterParams->setLeaveTypeId($leaveTypeId);
        $entitlementSearchFilterParams->setFromDate($fromDate);
        $entitlementSearchFilterParams->setToDate($toDate);
        $entitlementSearchFilterParams->setLeaveTypeDeleted(
            $this->getRequestParams()->getBooleanOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_LEAVE_TYPE_DELETED
            )
        );
        $this->setSortingAndPaginationParams($entitlementSearchFilterParams);

        $entitlements = $this->getLeaveEntitlementService()
            ->getLeaveEntitlementDao()
            ->getLeaveEntitlements($entitlementSearchFilterParams);
        $total = $this->getLeaveEntitlementService()
            ->getLeaveEntitlementDao()
            ->getLeaveEntitlementsCount($entitlementSearchFilterParams);
        $sum = $this->getLeaveEntitlementService()
            ->getLeaveEntitlementDao()
            ->getLeaveEntitlementsSum($entitlementSearchFilterParams);

        return new EndpointCollectionResult(
            $this->getModelClass(),
            $entitlements,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_TOTAL => $total,
                    self::META_PARAMETER_SUM => $sum,
                    LeaveCommonParams::PARAMETER_FROM_DATE => $this->getDateTimeHelper()
                        ->formatDateTimeToYmd($fromDate),
                    LeaveCommonParams::PARAMETER_TO_DATE => $this->getDateTimeHelper()
                        ->formatDateTimeToYmd($toDate),
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ]
            )
        );
    }

    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        $model = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_MODEL,
            self::MODEL_DEFAULT,
        );
        return self::MODEL_MAP[$model];
    }

    /**
     * @return ParamRule
     */
    protected function getModelClassParamRule(): ParamRule
    {
        return $this->getValidationDecorator()->notRequiredParamRule(
            new ParamRule(
                self::FILTER_MODEL,
                new Rule(Rules::IN, [array_keys(self::MODEL_MAP)])
            ),
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        list($fromDateRule, $toDateRule) = $this->getFromToDatesRules(RequestParams::PARAM_TYPE_QUERY);
        return new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule($this->getEmpNumberParamRule()),
            $this->getValidationDecorator()->notRequiredParamRule($this->getLeaveTypeIdParamRule()),
            $this->getValidationDecorator()->notRequiredParamRule($fromDateRule),
            $this->getValidationDecorator()->notRequiredParamRule($toDateRule),
            $this->getModelClassParamRule(),
            new ParamRule(self::FILTER_LEAVE_TYPE_DELETED, new Rule(Rules::BOOL_VAL)),
            ...$this->getSortingAndPaginationParamsRules(LeaveEntitlementSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v2/leave/leave-entitlements",
     *     tags={"Leave/Entitlements"},
     *     summary="Assign Leave Entitlements to Employees",
     *     operationId="assign-leave-entitlements-to-employees",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="empNumber", type="integer", description="If not buk assign this is require"),
     *             @OA\Property(property="entitlement", type="integer"),
     *             @OA\Property(property="fromDate", type="number"),
     *             @OA\Property(property="toDate", type="number"),
     *             @OA\Property(property="leaveTypeId", type="integer"),
     *             @OA\Property(property="bulkAssign", type="boolean"),
     *             @OA\Property(property="locationId", type="integer"),
     *             required={"entitlement", "subunitId", "toDate", "integer"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Leave-LeaveEntitlementModel"
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
        $bulkAssign = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_BULK_ASSIGN
        );
        $leaveTypeId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_BODY,
            LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID
        );
        $fromDate = $this->getRequestParams()->getDateTime(
            RequestParams::PARAM_TYPE_BODY,
            LeaveCommonParams::PARAMETER_FROM_DATE
        );
        $toDate = $this->getRequestParams()->getDateTime(
            RequestParams::PARAM_TYPE_BODY,
            LeaveCommonParams::PARAMETER_TO_DATE
        );
        $entitlement = $this->getRequestParams()->getFloat(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_ENTITLEMENT
        );

        if ($bulkAssign) {
            $empNumbers = $this->getEmpNumbersForBulkAssign();
            list($leaveEntitlements, $savedCount) = $this->getLeaveEntitlementService()->bulkAssignLeaveEntitlements(
                $empNumbers,
                $leaveTypeId,
                $fromDate,
                $toDate,
                $entitlement
            );
            return new EndpointCollectionResult(
                LeaveEntitlementModel::class,
                $leaveEntitlements,
                new ParameterBag([self::META_PARAMETER_COUNT => $savedCount])
            );
        }

        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_BODY,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $leaveEntitlement = $this->getLeaveEntitlementService()->addEntitlementForEmployee(
            $empNumber,
            $leaveTypeId,
            $fromDate,
            $toDate,
            $entitlement
        );
        return new EndpointResourceResult(LeaveEntitlementModel::class, $leaveEntitlement);
    }

    /**
     * @return int[]
     */
    private function getEmpNumbersForBulkAssign(): array
    {
        $locationId = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_LOCATION_ID
        );
        $subunitId = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_SUBUNIT_ID
        );
        $employeeSearchFilterParams = new EmployeeSearchFilterParams();
        $employeeSearchFilterParams->setSubunitId($subunitId);
        $employeeSearchFilterParams->setLocationId($locationId);
        $employeeSearchFilterParams->setLimit(0);
        return $this->getEmployeeService()
            ->getEmployeeDao()
            ->getEmpNumbersByFilterParams($employeeSearchFilterParams);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        $paramRules = new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::PARAMETER_BULK_ASSIGN, new Rule(Rules::BOOL_TYPE))
            ),
            $this->getLeaveTypeIdParamRule(),
            $this->getEntitlementParamRule(),
            ...$this->getFromToDatesRules(),
        );
        $bulkAssign = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_BULK_ASSIGN
        );

        if ($bulkAssign) {
            $paramRules->addParamValidation(
                $this->getValidationDecorator()->notRequiredParamRule(
                    new ParamRule(self::PARAMETER_LOCATION_ID, new Rule(Rules::POSITIVE))
                )
            );
            $paramRules->addParamValidation(
                $this->getValidationDecorator()->notRequiredParamRule(
                    new ParamRule(self::PARAMETER_SUBUNIT_ID, new Rule(Rules::POSITIVE))
                )
            );
        } else {
            $paramRules->addParamValidation($this->getEmpNumberParamRule());
        }
        return $paramRules;
    }

    /**
     * @param string $requestParamType
     * @return ParamRule[]
     */
    private function getFromToDatesRules(string $requestParamType = RequestParams::PARAM_TYPE_BODY): array
    {
        return [
            new ParamRule(
                LeaveCommonParams::PARAMETER_FROM_DATE,
                new Rule(Rules::API_DATE),
                new Rule(
                    Rules::LESS_THAN,
                    [
                        $this->getRequestParams()->getDateTimeOrNull(
                            $requestParamType,
                            LeaveCommonParams::PARAMETER_TO_DATE
                        )
                    ]
                )
            ),
            new ParamRule(LeaveCommonParams::PARAMETER_TO_DATE, new Rule(Rules::API_DATE)),
        ];
    }

    /**
     * @return ParamRule
     */
    private function getEmpNumberParamRule(): ParamRule
    {
        return new ParamRule(CommonParams::PARAMETER_EMP_NUMBER, new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS));
    }

    /**
     * @return ParamRule
     */
    private function getLeaveTypeIdParamRule(): ParamRule
    {
        return new ParamRule(
            LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID,
            new Rule(Rules::POSITIVE),
            new Rule(Rules::INT_VAL),
            new Rule(LeaveTypeIdRule::class)
        );
    }

    /**
     * @return ParamRule
     */
    private function getEntitlementParamRule(): ParamRule
    {
        return new ParamRule(self::PARAMETER_ENTITLEMENT, new Rule(Rules::ZERO_OR_POSITIVE));
    }

    /**
     * @OA\Put(
     *     path="/api/v2/leave/leave-entitlements/{id}",
     *     tags={"Leave/Entitlements"},
     *     summary="Update a Leave Entitlement",
     *     operationId="update-a-leave-entitlement",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="entitlement", type="integer"),
     *             @OA\Property(property="fromDate", type="number"),
     *             @OA\Property(property="toDate", type="number"),
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Leave-LeaveEntitlementModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $leaveEntitlement = $this->getLeaveEntitlementService()
            ->getLeaveEntitlementDao()
            ->getLeaveEntitlement($this->getIdUrlAttribute());
        $this->throwRecordNotFoundExceptionIfNotExist($leaveEntitlement, LeaveEntitlement::class);
        $this->checkLeaveEntitlementAccessible($leaveEntitlement);

        $fromDate = $this->getRequestParams()->getDateTime(
            RequestParams::PARAM_TYPE_BODY,
            LeaveCommonParams::PARAMETER_FROM_DATE
        );
        $toDate = $this->getRequestParams()->getDateTime(
            RequestParams::PARAM_TYPE_BODY,
            LeaveCommonParams::PARAMETER_TO_DATE
        );
        $entitlement = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_ENTITLEMENT
        );
        $leaveEntitlement->setFromDate($fromDate);
        $leaveEntitlement->setToDate($toDate);
        $leaveEntitlement->setNoOfDays($entitlement);
        $this->getLeaveEntitlementService()->getLeaveEntitlementDao()->saveLeaveEntitlement($leaveEntitlement);
        return new EndpointResourceResult(LeaveEntitlementModel::class, $leaveEntitlement);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getIdParamRule(),
            $this->getEntitlementParamRule(),
            ...$this->getFromToDatesRules()
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/leave/leave-entitlements",
     *     tags={"Leave/Entitlements"},
     *     summary="Delete Leave Entitlements",
     *     operationId="delete-leave-entitlements",
     *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
     *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse"),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound"),
     *     @OA\Response(
     *         response="400",
     *         description="Bad Request - Leave entitlement not accessible",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="object",
     *                 @OA\Property(property="status", type="string", default="400"),
     *                 @OA\Property(property="message", type="string", default="Bad Request")
     *             )
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getLeaveEntitlementService()->getLeaveEntitlementDao()->getExistingLeaveEntitlementIds(
            $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS)
        );
        $this->throwRecordNotFoundExceptionIfEmptyIds($ids);

        $leaveEntitlements = $this->getLeaveEntitlementService()
            ->getLeaveEntitlementDao()->getLeaveEntitlementsByIds($ids);
        foreach ($leaveEntitlements as $leaveEntitlement) {
            $this->checkLeaveEntitlementAccessible($leaveEntitlement);
        }

        $deletableIds = $this->getLeaveEntitlementService()->getDeletableIdsFromEntitlementIds($ids);
        $allIdsDeletable = empty(array_diff($ids, $deletableIds));
        if (!$allIdsDeletable) {
            throw $this->getBadRequestException();
        }
        $this->getLeaveEntitlementService()->getLeaveEntitlementDao()->deleteLeaveEntitlements($ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_IDS,
                new Rule(Rules::INT_ARRAY)
            )
        );
    }
}
