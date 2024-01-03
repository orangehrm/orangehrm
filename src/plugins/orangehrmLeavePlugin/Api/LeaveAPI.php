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
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Entity\LeaveRequest;
use OrangeHRM\Leave\Api\Model\LeaveDetailedModel;
use OrangeHRM\Leave\Api\Model\LeaveModel;
use OrangeHRM\Leave\Api\Traits\LeavePermissionTrait;
use OrangeHRM\Leave\Api\Traits\LeaveRequestParamHelperTrait;
use OrangeHRM\Leave\Api\Traits\LeaveRequestPermissionTrait;
use OrangeHRM\Leave\Dto\LeaveRequest\DetailedLeave;
use OrangeHRM\Leave\Dto\LeaveSearchFilterParams;
use OrangeHRM\Leave\Traits\Service\LeaveRequestServiceTrait;
use OrangeHRM\Pim\Api\Model\EmployeeModel;

class LeaveAPI extends Endpoint implements CrudEndpoint
{
    use LeaveRequestParamHelperTrait;
    use LeaveRequestServiceTrait;
    use UserRoleManagerTrait;
    use NormalizerServiceTrait;
    use LeaveRequestPermissionTrait;
    use LeavePermissionTrait;
    use DateTimeHelperTrait;

    public const FILTER_LEAVE_REQUEST_ID = 'leaveRequestId';

    public const PARAMETER_LEAVE_ID = 'leaveId';
    public const PARAMETER_ACTION = 'action';

    public const META_PARAMETER_EMPLOYEE = 'employee';
    public const META_PARAMETER_LEAVE_START_DATE = 'startDate';
    public const META_PARAMETER_LEAVE_END_DATE = 'endDate';

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
     * @OA\Get(
     *     path="/api/v2/leave/leave-requests/{leaveRequestId}/leaves",
     *     tags={"Leave/Leaves"},
     *     summary="List All Leaves in a Leave Request",
     *     operationId="list-all-leaves-in-a-leave-request",
     *     @OA\PathParameter(
     *         name="leaveRequestId",
     *         @OA\Schema(type="integer")
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
     *                 ref="#/components/schemas/Leave-LeaveDetailedModel"
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(
     *                     property="employee",
     *                     type="object",
     *                     @OA\Property(property="empNumber", type="integer"),
     *                     @OA\Property(property="firstName", type="string"),
     *                     @OA\Property(property="lastName", type="string"),
     *                     @OA\Property(property="middleName", type="string"),
     *                     @OA\Property(property="employeeId", type="string"),
     *                     @OA\Property(property="terminationId", type="integer"),
     *                 ),
     *                 @OA\Property(property="startDate", type="string", format="date"),
     *                 @OA\Property(property="endDate", type="string", format="date")
     *             )
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $leaveSearchFilterParams = $this->getLeaveSearchFilterParams();

        /** @var LeaveRequest|null $leaveRequest */
        $leaveRequest = $this->getLeaveRequestService()
            ->getLeaveRequestDao()
            ->getLeaveRequestById($leaveSearchFilterParams->getLeaveRequestId());

        $this->throwRecordNotFoundExceptionIfNotExist($leaveRequest, LeaveRequest::class);
        $this->checkLeaveRequestAccessible($leaveRequest);

        $leaves = $this->getLeaveRequestService()
            ->getLeaveRequestDao()
            ->getLeaves($leaveSearchFilterParams);
        $total = $this->getLeaveRequestService()
            ->getLeaveRequestDao()
            ->getLeavesCount($leaveSearchFilterParams);
        $allLeavesOfLeaveRequest = $this->getLeaveRequestService()->getLeaveRequestDao()
            ->getLeavesByLeaveRequestIds([$leaveRequest->getId()]);
        $detailedLeaves = $this->getLeaveRequestService()->getDetailedLeaves($leaves, $allLeavesOfLeaveRequest);

        $employee = $leaveRequest->getEmployee();

        return new EndpointCollectionResult(
            LeaveDetailedModel::class,
            $detailedLeaves,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_TOTAL => $total,
                    self::META_PARAMETER_EMPLOYEE => $this->getNormalizedEmployee($employee),
                    self::META_PARAMETER_LEAVE_START_DATE => $this->getDateTimeHelper()->formatDateTimeToYmd($allLeavesOfLeaveRequest[0]->getDate()),
                    self::META_PARAMETER_LEAVE_END_DATE => $this->getDateTimeHelper()->formatDateTimeToYmd(end($allLeavesOfLeaveRequest)->getDate()),
                ]
            )
        );
    }

    /**
     * @param Employee $employee
     * @return array
     */
    protected function getNormalizedEmployee(Employee $employee): array
    {
        return $this->getNormalizerService()->normalize(
            EmployeeModel::class,
            $employee
        );
    }

    /**
     * @return LeaveSearchFilterParams
     */
    protected function getLeaveSearchFilterParams(): LeaveSearchFilterParams
    {
        $leaveRequestId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::FILTER_LEAVE_REQUEST_ID
        );

        $leaveRequestSearchFilterParams = new LeaveSearchFilterParams();
        $leaveRequestSearchFilterParams->setLeaveRequestId($leaveRequestId);
        $this->setSortingAndPaginationParams($leaveRequestSearchFilterParams);

        return $leaveRequestSearchFilterParams;
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(self::FILTER_LEAVE_REQUEST_ID, new Rule(Rules::POSITIVE)),
            ...$this->getSortingAndPaginationParamsRules()
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
     * @OA\Put(
     *     path="/api/v2/leave/leaves/{leaveId}",
     *     tags={"Leave/Leaves"},
     *     summary="Update a Leave",
     *     operationId="update-a-leave",
     *     @OA\PathParameter(
     *         name="leaveId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="action", type="string", enum={"APPROVE", "REJECT", "CANCEL"}),
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Leave-LeaveModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $leaveId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_LEAVE_ID
        );
        $leave = $this->getLeaveRequestService()->getLeaveRequestDao()->getLeaveById($leaveId);
        $this->throwRecordNotFoundExceptionIfNotExist($leave, Leave::class);
        $this->checkLeaveAccessible($leave);

        $detailedLeave = new DetailedLeave($leave);

        $action = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_ACTION);
        if (!$detailedLeave->isActionAllowed($action)) {
            throw $this->getBadRequestException('Performed action not allowed');
        }

        $workflow = $detailedLeave->getWorkflowForAction($action);
        $this->getLeaveRequestService()->changeLeaveStatus($leave, $workflow);

        return new EndpointResourceResult(LeaveModel::class, $leave);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(self::PARAMETER_LEAVE_ID, new Rule(Rules::POSITIVE)),
            new ParamRule(self::PARAMETER_ACTION, new Rule(Rules::STRING_TYPE)),
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
