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
     * @OA\Get(
     *     path="/api/v2/leave/leave-requests/{leaveRequestId}",
     *     tags={"Leave/My Leave Request"},
     *     summary="Get My Leave Request",
     *     operationId="get-my-leave-request",
     *     @OA\PathParameter(
     *         name="leaveRequestId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="model",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum={\OrangeHRM\Leave\Api\EmployeeLeaveRequestAPI::MODEL_DEFAULT, \OrangeHRM\Leave\Api\EmployeeLeaveRequestAPI::MODEL_DETAILED})
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 oneOf={
     *                     @OA\Schema(ref="#/components/schemas/Leave-LeaveRequestDetailedModel"),
     *                     @OA\Schema(ref="#/components/schemas/Leave-LeaveRequestModel"),
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
        return parent::getOne();
    }

    /**
     * @OA\Get(
     *     path="/api/v2/leave/leave-requests",
     *     tags={"Leave/My Leave Request"},
     *     summary="List My Leave Requests",
     *     operationId="list-my-leave-requests",
     *     @OA\Parameter(
     *         name="leaveTypeId",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="fromDate",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="toDate",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="includeEmployees",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=LeaveRequestSearchFilterParams::INCLUDE_EMPLOYEES)
     *     ),
     *     @OA\Parameter(
     *         name="statuses",
     *         in="query",
     *         required=false,
     *         description="-1 => rejected, 0 => cancelled, 1 => pending, 2 => approved, 3 => taken",
     *         @OA\Schema(type="integer", enum=LeaveRequestSearchFilterParams::LEAVE_STATUSES)
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=LeaveRequestSearchFilterParams::ALLOWED_SORT_FIELDS)
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
     *                 ref="#/components/schemas/Leave-LeaveRequestDetailedModel"
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="empNumber", type="integer"),
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     ),
     * )
     *
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
     * @OA\Post(
     *     path="/api/v2/leave/leave-requests",
     *     tags={"Leave/My Leave Request"},
     *     summary="Apply for Leave",
     *     operationId="apply-for-leave",
     *     @OA\Parameter(
     *         name="model",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum={\OrangeHRM\Leave\Api\EmployeeLeaveRequestAPI::MODEL_DEFAULT, \OrangeHRM\Leave\Api\EmployeeLeaveRequestAPI::MODEL_DETAILED})
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="comment", type="string"),
     *             @OA\Property(
     *                 property="duration",
     *                 type="object",
     *                 @OA\Property(
     *                     property="type",
     *                     type="string",
     *                     enum={"full_day", "half_day_afternoon", "half_day_morning", "specify_time"},
     *                 ),
     *                 @OA\Property(
     *                     property="fromTime",
     *                     type="number",
     *                     example="12:00",
     *                     description="used when duration type = specify_time "
     *                 ),
     *                 @OA\Property(
     *                     property="toTime",
     *                     type="number",
     *                     example="17:00",
     *                     description="used when duration type = specify_time "
     *                 ),
     *                 required={"type"}
     *             ),
     *             @OA\Property(
     *                 property="endDuration",
     *                 type="object",
     *                 description="Used when there are partial days at both the beginning and end",
     *                 @OA\Property(
     *                     property="type",
     *                     type="string",
     *                     enum={"full_day", "half_day_afternoon", "half_day_morning", "specify_time"},
     *                 ),
     *                 @OA\Property(
     *                     property="fromTime",
     *                     type="number", example="12:00",
     *                     description="used when endDuration type = specify_time "
     *                 ),
     *                 @OA\Property(
     *                     property="toTime",
     *                     type="number",
     *                     example="17:00",
     *                     description="used when endDuration type = specify_time "
     *                 ),
     *                 required={"type"}
     *             ),
     *             @OA\Property(property="partialOption", type="string", example="start"),
     *             @OA\Property(property="fromDate", type="string"),
     *             @OA\Property(property="toDate", type="string"),
     *             @OA\Property(property="leaveTypeId", type="integer"),
     *             required={"duration", "fromDate", "toDate", "leaveTypeId"}
     *         ),
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 oneOf={
     *                     @OA\Schema(ref="#/components/schemas/Leave-LeaveRequestDetailedModel"),
     *                     @OA\Schema(ref="#/components/schemas/Leave-LeaveRequestModel"),
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
     * @OA\Put(
     *     path="/api/v2/leave/leave-requests/{leaveRequestId}",
     *     tags={"Leave/My Leave Request"},
     *     summary="Update my Leave Request",
     *     operationId="update-my-leave-request",
     *     @OA\PathParameter(
     *         name="leaveRequestId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="model",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum={\OrangeHRM\Leave\Api\EmployeeLeaveRequestAPI::MODEL_DEFAULT, \OrangeHRM\Leave\Api\EmployeeLeaveRequestAPI::MODEL_DETAILED})
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="action",
     *                 type="string",
     *                 enum={"APPROVE", "REJECT", "CANCEL"},
     *             ),
     *             required={"action"}
     *         ),
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 oneOf={
     *                     @OA\Schema(ref="#/components/schemas/Leave-LeaveRequestDetailedModel"),
     *                     @OA\Schema(ref="#/components/schemas/Leave-LeaveRequestModel"),
     *                 }
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="400",
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 oneOf={
     *                     @OA\Schema(
     *                         type="object",
     *                         @OA\Property(property="status", type="string", default="400"),
     *                         @OA\Property(property="message", type="string", default="Performed action not allowed")
     *                     ),
     *                     @OA\Schema(
     *                         type="object",
     *                         @OA\Property(property="status", type="string", default="400"),
     *                         @OA\Property(property="message", type="string", default="Leave request has multiple statuses")
     *                     ),
     *                 }
     *             )
     *         )
     *     ),
     * )
     *
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        return parent::update();
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
