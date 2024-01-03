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

namespace OrangeHRM\Claim\Api;

use Exception;
use OrangeHRM\Claim\Api\Model\ClaimRequestModel;
use OrangeHRM\Claim\Api\Model\ClaimRequestSummaryModel;
use OrangeHRM\Claim\Api\Model\EmployeeClaimRequestModel;
use OrangeHRM\Claim\Dto\ClaimRequestSearchFilterParams;
use OrangeHRM\Claim\Dto\EmployeeClaimRequestSearchFilterParams;
use OrangeHRM\Claim\Traits\Service\ClaimServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Api\V2\Exception\InvalidParamException;
use OrangeHRM\Core\Api\V2\Model\WorkflowStateModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\ClaimRequest;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

class EmployeeClaimRequestAPI extends Endpoint implements CrudEndpoint
{
    use EntityManagerHelperTrait;
    use ClaimServiceTrait;
    use DateTimeHelperTrait;
    use AuthUserTrait;
    use NormalizerServiceTrait;
    use UserRoleManagerTrait;
    use EmployeeServiceTrait;

    public const PARAMETER_CLAIM_EVENT_ID = 'claimEventId';
    public const PARAMETER_CURRENCY_ID = 'currencyId';
    public const PARAMETER_REMARKS = 'remarks';
    public const REMARKS_MAX_LENGTH = 1000;
    public const PARAMETER_ALLOWED_ACTIONS = 'allowedActions';
    public const PARAMETER_CLAIM_REQUEST_OWNER = 'employee';
    public const PARAMETER_EMPLOYEE_NUMBER = 'empNumber';
    public const PARAMETER_REFERENCE_ID = 'referenceId';
    public const PARAMETER_EVENT_ID = 'eventId';
    public const PARAMETER_STATUS = 'status';
    public const PARAMETER_FROM_DATE = 'fromDate';
    public const PARAMETER_TO_DATE = 'toDate';
    public const PARAMETER_MODEL = 'model';
    public const MODEL_DEFAULT = 'default';
    public const MODEL_SUMMARY = 'summary';
    public const MODEL_MAP = [
        self::MODEL_DEFAULT => EmployeeClaimRequestModel::class,
        self::MODEL_SUMMARY => ClaimRequestSummaryModel::class,
    ];
    public const ACTIONABLE_STATES_MAP = [
        WorkflowStateMachine::CLAIM_ACTION_SUBMIT => 'SUBMIT',
        WorkflowStateMachine::CLAIM_ACTION_APPROVE => 'APPROVE',
        WorkflowStateMachine::CLAIM_ACTION_PAY => 'PAY',
        WorkflowStateMachine::CLAIM_ACTION_CANCEL => 'CANCEL',
        WorkflowStateMachine::CLAIM_ACTION_REJECT => 'REJECT'
    ];
    public const FILTER_INCLUDE_EMPLOYEES = 'includeEmployees';

    /**
     * @OA\Post(
     *     path="/api/v2/claim/employees/{empNumber}/requests",
     *     tags={"Claim/Requests"},
     *     summary="List an Employee's Claim Requests",
     *     operationId="list-an-employees-claim-requests",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="claimEventId", type="integer"),
     *             @OA\Property(property="currencyId", type="string"),
     *             @OA\Property(property="remarks", type="string"),
     *             required={"claimEventId", "currency"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Claim-RequestModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $claimRequest = new ClaimRequest();
        $empNumber = $this->getEmpNumber();
        $employee = $this->getEmployeeService()->getEmployeeDao()->getEmployeeByEmpNumber($empNumber);
        if ($employee === null) {
            throw $this->getRecordNotFoundException();
        }
        if (!is_null($employee->getEmployeeTerminationRecord())) {
            throw $this->getForbiddenException();
        }
        if (!$this->isSelfByEmpNumber($empNumber)) {
            throw $this->getForbiddenException();
        }
        if (!$this->getUserRoleManagerHelper()->isEmployeeAccessible($empNumber)) {
            throw $this->getForbiddenException();
        }
        $this->setClaimRequest($claimRequest, $empNumber);
        return new EndpointResourceResult(ClaimRequestModel::class, $claimRequest);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        $paramRules = $this->getCommonParamRuleCollection();
        $paramRules->addParamValidation(
            new ParamRule(
                self::PARAMETER_EMPLOYEE_NUMBER,
                new Rule(Rules::POSITIVE)
            )
        );
        return $paramRules;
    }

    /**
     * @return ParamRuleCollection
     */
    protected function getCommonParamRuleCollection(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_CLAIM_EVENT_ID,
                new Rule(Rules::POSITIVE)
            ),
            new ParamRule(
                self::PARAMETER_CURRENCY_ID,
                new Rule(Rules::REQUIRED)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_REMARKS,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::REMARKS_MAX_LENGTH]),
                ),
                true
            ),
        );
    }

    /**
     * @param int $empNumber
     * @return bool
     */
    protected function isSelfByEmpNumber(int $empNumber): bool
    {
        return !$this->getUserRoleManagerHelper()->isSelfByEmpNumber($empNumber);
    }

    /**
     * @param ClaimRequest $claimRequest
     * @param int $empNumber
     * @return ClaimRequest
     */
    protected function setClaimRequest(ClaimRequest $claimRequest, int $empNumber): ClaimRequest
    {
        $this->beginTransaction();
        try {
            $claimEventId = $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_CLAIM_EVENT_ID
            );

            $currencyId = $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_CURRENCY_ID
            );

            $claimEvent = $this->getClaimService()->getClaimDao()->getClaimEventById($claimEventId);
            if ($claimEvent === null || !$claimEvent->getStatus()) {
                throw $this->getInvalidParamException(self::PARAMETER_CLAIM_EVENT_ID);
            }

            if ($claimRequest->getDecorator()->getCurrencyByCurrencyId($currencyId) === null) {
                throw $this->getInvalidParamException(self::PARAMETER_CURRENCY_ID);
            }

            $claimRequest->setClaimEvent($claimEvent);
            $claimRequest->getDecorator()->setCurrencyByCurrencyId($currencyId);
            $claimRequest->setDescription(
                $this->getRequestParams()
                    ->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_REMARKS)
            );

            $claimRequest->setReferenceId($this->getClaimService()->getReferenceId());
            $claimRequest->setStatus(ClaimRequest::REQUEST_STATUS_INITIATED);
            $claimRequest->setCreatedDate($this->getDateTimeHelper()->getNow());
            $userId = $this->getAuthUser()->getUserId();
            $claimRequest->getDecorator()->setUserByUserId($userId);

            $claimRequest->getDecorator()->setEmployeeByEmpNumber($empNumber);

            $this->commitTransaction();
            return $this->getClaimService()->getClaimDao()->saveClaimRequest($claimRequest);
        } catch (InvalidParamException|BadRequestException $e) {
            $this->rollBackTransaction();
            throw $e;
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v2/claim/employees/requests",
     *     tags={"Claim/Requests"},
     *     summary="List All Claim Requests",
     *     operationId="list-all-claim-requests",
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=EmployeeClaimRequestSearchFilterParams::ALLOWED_SORT_FIELDS)
     *     ),
     *     @OA\Parameter(
     *         name="referenceId",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum={"INITIATED", "SUBMITTED", "APPROVED", "REJECTED", "CANCELLED", "PAID"})
     *     ),
     *     @OA\Parameter(
     *         name="eventId",
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
     *         name="fromDate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="DateTime")
     *     ),
     *     @OA\Parameter(
     *         name="toDate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="DateTime")
     *     ),
     *     @OA\Parameter(
     *         name="includeEmployees",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=ClaimRequestSearchFilterParams::INCLUDE_EMPLOYEES)
     *     ),
     *     @OA\Parameter(
     *         name="model",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={\OrangeHRM\Claim\Api\EmployeeClaimRequestAPI::MODEL_DEFAULT, \OrangeHRM\Claim\Api\EmployeeClaimRequestAPI::MODEL_SUMMARY},
     *             default=\OrangeHRM\Claim\Api\EmployeeClaimRequestAPI::MODEL_DEFAULT
     *         )
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
     *                 @OA\Items(oneOf={
     *                     @OA\Schema(ref="#/components/schemas/Claim-EmployeeClaimRequestModel"),
     *                     @OA\Schema(ref="#/components/schemas/Claim-ClaimRequestSummaryModel"),
     *                 })
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
        $employeeClaimRequestSearchFilterParams = $this->getClaimRequestSearchFilterParams();

        $this->setSortingAndPaginationParams($employeeClaimRequestSearchFilterParams);
        $this->getCommonFilterParams($employeeClaimRequestSearchFilterParams);
        $this->setEmpNumbers($employeeClaimRequestSearchFilterParams);
        $this->setIncludeEmployees($employeeClaimRequestSearchFilterParams);

        $claimRequests = $this->getClaimService()->getClaimDao()
            ->getClaimRequestList($employeeClaimRequestSearchFilterParams);

        $count = $this->getClaimService()->getClaimDao()
            ->getClaimRequestCount($employeeClaimRequestSearchFilterParams);

        $model = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_MODEL, self::MODEL_DEFAULT);

        return $this->getEndPointCollectionResult($claimRequests, $count, $model);
    }

    /**
     * @return ClaimRequestSearchFilterParams
     */
    protected function getClaimRequestSearchFilterParams(): ClaimRequestSearchFilterParams
    {
        return new EmployeeClaimRequestSearchFilterParams();
    }

    /**
     * @param array $claimRequests
     * @param int $count
     * @param string|null $model
     * @return EndpointCollectionResult
     */
    protected function getEndPointCollectionResult(
        array $claimRequests,
        int $count,
        string $model
    ): EndpointCollectionResult {
        if ($model === self::MODEL_SUMMARY) {
            return new EndpointCollectionResult(
                ClaimRequestSummaryModel::class,
                $claimRequests,
                new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
            );
        }
        return new EndpointCollectionResult(
            EmployeeClaimRequestModel::class,
            $claimRequests,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @param ClaimRequestSearchFilterParams $claimRequestSearchFilterParams
     */
    protected function setEmpNumbers(ClaimRequestSearchFilterParams $claimRequestSearchFilterParams): void
    {
        $empNumber = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_EMPLOYEE_NUMBER
        );

        if (!is_null($empNumber)) {
            if (!$this->getEmployeeService()->getEmployeeDao()->getEmployeeByEmpNumber(
                $empNumber
            ) instanceof Employee) {
                throw $this->getRecordNotFoundException();
            }
            if (!$this->getUserRoleManagerHelper()->isEmployeeAccessible($empNumber)) {
                throw $this->getForbiddenException();
            }
            $claimRequestSearchFilterParams->setEmpNumbers([$empNumber]);
        } else {
            $empNumbers = $this->getUserRoleManager()->getAccessibleEntityIds(Employee::class);
            $claimRequestSearchFilterParams->setEmpNumbers($empNumbers);
        }
    }

    /**
     * @param ClaimRequestSearchFilterParams $employeeClaimRequestSearchFilterParams
     */
    private function setIncludeEmployees(ClaimRequestSearchFilterParams $employeeClaimRequestSearchFilterParams)
    {
        $employeeClaimRequestSearchFilterParams->setIncludeEmployees(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_INCLUDE_EMPLOYEES,
                $this->getDefaultIncludeEmployees()
            )
        );
    }

    /**
     * @return string
     */
    private function getDefaultIncludeEmployees(): string
    {
        return ClaimRequestSearchFilterParams::INCLUDE_EMPLOYEES_ONLY_CURRENT;
    }

    /**
     * @param ClaimRequestSearchFilterParams $claimRequestSearchFilterParams
     */
    private function getCommonFilterParams(ClaimRequestSearchFilterParams $claimRequestSearchFilterParams): void
    {
        $claimRequestSearchFilterParams->setReferenceId(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::PARAMETER_REFERENCE_ID
            )
        );
        $claimRequestSearchFilterParams->setEventId(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::PARAMETER_EVENT_ID
            )
        );
        $claimRequestSearchFilterParams->setStatus(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::PARAMETER_STATUS
            )
        );
        $claimRequestSearchFilterParams->setFromDate(
            $this->getRequestParams()->getDateTimeOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::PARAMETER_FROM_DATE
            )
        );
        $claimRequestSearchFilterParams->setToDate(
            $this->getRequestParams()->getDateTimeOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::PARAMETER_TO_DATE
            )
        );
    }

    /**
     * @return ParamRuleCollection
     */
    protected function getCommonParamRuleCollectionGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_REFERENCE_ID,
                    new Rule(Rules::STRING_TYPE)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_EVENT_ID,
                    new Rule(Rules::POSITIVE)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_STATUS,
                    new Rule(Rules::STRING_TYPE)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_FROM_DATE,
                    new Rule(Rules::DATE_TIME)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_TO_DATE,
                    new Rule(Rules::DATE_TIME)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_MODEL,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::IN, [array_keys(self::MODEL_MAP)])
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_INCLUDE_EMPLOYEES,
                    new Rule(Rules::IN, [EmployeeClaimRequestSearchFilterParams::INCLUDE_EMPLOYEES])
                )
            ),
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        $paramRuleCollection = $this->getCommonParamRuleCollectionGetAll();
        $paramRuleCollection->addParamValidation(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_EMPLOYEE_NUMBER,
                    new Rule(Rules::POSITIVE)
                )
            )
        );
        $sortFieldParamRules = $this->getSortingAndPaginationParamsRules(
            EmployeeClaimRequestSearchFilterParams::ALLOWED_SORT_FIELDS
        );
        foreach ($sortFieldParamRules as $sortFieldParamRule) {
            $paramRuleCollection->addParamValidation($sortFieldParamRule);
        }

        return $paramRuleCollection;
    }

    /**
     * @OA\Get(
     *     path="/api/v2/claim/employees/{empNumber}/requests/{id}",
     *     tags={"Claim/Requests"},
     *     summary="Get an Employee's Claim Request",
     *     operationId="get-an-employees-claim-request",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Claim Request Id",
     *         @OA\Schema(type="integer"),
     *         required=true
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Claim-RequestModel"
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="allowedActions", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="employee", type="object",
     *                     @OA\Property(property="empNumber", type="integer"),
     *                     @OA\Property(property="lastName", type="string"),
     *                     @OA\Property(property="firstName", type="string"),
     *                     @OA\Property(property="middleName", type="string"),
     *                     @OA\Property(property="employeeId", type="string"),
     *                 @OA\Property(property="terminationId", type="integer"))
     *             )
     *         )
     *     )
     * )
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $id = $this->getRequestParams()
            ->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $empNumber = $this->getEmpNumber();

        if (!$this->isSelfByEmpNumber($empNumber)) {
            throw $this->getForbiddenException();
        }

        if (!$this->getUserRoleManagerHelper()->isEmployeeAccessible($empNumber)) {
            throw $this->getForbiddenException();
        }

        $claimRequest = $this->getClaimService()->getClaimDao()
            ->getClaimRequestById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($claimRequest, ClaimRequest::class);

        if ($claimRequest->getEmployee()->getEmpNumber() !== $empNumber) {
            throw $this->getForbiddenException();
        }

        $allowedActions = $this->getAllowedActions($claimRequest);
        return new EndpointResourceResult(
            ClaimRequestModel::class,
            $claimRequest,
            new ParameterBag([
                self::PARAMETER_ALLOWED_ACTIONS => $allowedActions,
                self::PARAMETER_CLAIM_REQUEST_OWNER => $this->getEmployeeService()->getEmployeeAsArray($empNumber),
            ])
        );
    }

    /**
     * @return int
     */
    protected function getEmpNumber(): int
    {
        return $this->getRequestParams()
            ->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, self::PARAMETER_EMPLOYEE_NUMBER);
    }

    /**
     * @return ParamRuleCollection
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            new ParamRule(
                self::PARAMETER_EMPLOYEE_NUMBER,
                new Rule(Rules::POSITIVE)
            )
        );
    }

    /**
     * @param ClaimRequest $claimRequest
     * @return array
     */
    protected function getAllowedActions(ClaimRequest $claimRequest): array
    {
        $excludeRoles = ['Admin'];
        if (
            $this->getAuthUser()->getUserRoleName() === 'Admin'
            && !$this->getUserRoleManagerHelper()->isSelfByEmpNumber($claimRequest->getEmployee()->getEmpNumber())
        ) {
            $excludeRoles = [];
        }
        $allowedWorkflowItems = $this->getUserRoleManager()->getAllowedActions(
            WorkflowStateMachine::FLOW_CLAIM,
            $claimRequest->getStatus(),
            $excludeRoles,
            [],
            [Employee::class => $claimRequest->getEmployee()->getEmpNumber()]
        );
        foreach ($allowedWorkflowItems as $allowedWorkflowItem) {
            $allowedWorkflowItem->setAction(self::ACTIONABLE_STATES_MAP[$allowedWorkflowItem->getAction()]);
        }
        return $this->getNormalizerService()
            ->normalizeArray(WorkflowStateModel::class, $allowedWorkflowItems);
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
}
