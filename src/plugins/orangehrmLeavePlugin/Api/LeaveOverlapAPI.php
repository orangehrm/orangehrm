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
use OrangeHRM\Leave\Api\Model\OverlapLeaveModel;
use OrangeHRM\Leave\Api\Traits\LeaveRequestParamHelperTrait;
use OrangeHRM\Leave\Dto\LeaveOverlapParams;
use OrangeHRM\Leave\Service\LeaveApplicationService;
use OrangeHRM\Leave\Traits\Service\LeaveRequestServiceTrait;

class LeaveOverlapAPI extends Endpoint implements CollectionEndpoint
{
    use LeaveRequestParamHelperTrait;
    use AuthUserTrait;
    use LeaveRequestServiceTrait;

    public const META_PARAMETER_IS_WORK_SHIFT_LENGTH_EXCEEDED = 'isWorkShiftLengthExceeded';

    private ?LeaveApplicationService $leaveApplicationService = null;

    /**
     * @return LeaveApplicationService
     */
    protected function getLeaveApplicationService(): LeaveApplicationService
    {
        if (!$this->leaveApplicationService instanceof LeaveApplicationService) {
            $this->leaveApplicationService = new LeaveApplicationService();
        }
        return $this->leaveApplicationService;
    }

    /**
     * To reuse LeaveRequestParamHelperTrait::class but with LeaveOverlapParams::class
     * @inheritDoc
     */
    protected function getLeaveTypeIdParam(): int
    {
        return 0;
    }

    /**
     * @inheritDoc
     */
    protected function getFromDateParam(): ?DateTime
    {
        return $this->getRequestParams()->getDateTimeOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            LeaveCommonParams::PARAMETER_FROM_DATE
        );
    }

    /**
     * @inheritDoc
     */
    protected function getToDateParam(): ?DateTime
    {
        return $this->getRequestParams()->getDateTimeOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            LeaveCommonParams::PARAMETER_TO_DATE
        );
    }

    /**
     * @inheritDoc
     */
    protected function getDurationParam(string $key, ?array $default = null): ?array
    {
        return $this->getRequestParams()->getArrayOrNull(RequestParams::PARAM_TYPE_QUERY, $key, $default);
    }

    /**
     * @inheritDoc
     */
    protected function getPartialOptionParam(): ?string
    {
        return $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            LeaveCommonParams::PARAMETER_PARTIAL_OPTION
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v2/leave/overlap-leaves",
     *     tags={"Leave/Overlap Leave"},
     *     summary="Get Overlapping Leaves",
     *     operationId="get-overlapping-leaves",
     *     @OA\Parameter(
     *         name="empNumber",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="fromDate",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="toDate",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="duration[type]",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", example="full_day, half_day_afternoon")
     *     ),
     *     @OA\Parameter(
     *         name="duration[fromTime]",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="time")
     *     ),
     *     @OA\Parameter(
     *         name="duration[toTime]",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="time")
     *     ),
     *     @OA\Parameter(
     *         name="endDuration[type]",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={
     *                 OrangeHRM\Leave\Dto\LeaveDuration::FULL_DAY,
     *                 OrangeHRM\Leave\Dto\LeaveDuration::HALF_DAY_MORNING,
     *                 OrangeHRM\Leave\Dto\LeaveDuration::HALF_DAY_AFTERNOON,
     *                 OrangeHRM\Leave\Dto\LeaveDuration::SPECIFY_TIME
     *             }
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="endDuration[fromTime]",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="time")
     *     ),
     *     @OA\Parameter(
     *         name="endDuration[toTime]",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="time")
     *     ),
     *     @OA\Parameter(
     *         name="partialOption",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={
     *                 OrangeHRM\Leave\Dto\LeaveParameterObject::PARTIAL_OPTION_NONE,
     *                 OrangeHRM\Leave\Dto\LeaveParameterObject::PARTIAL_OPTION_ALL,
     *                 OrangeHRM\Leave\Dto\LeaveParameterObject::PARTIAL_OPTION_START,
     *                 OrangeHRM\Leave\Dto\LeaveParameterObject::PARTIAL_OPTION_END,
     *                 OrangeHRM\Leave\Dto\LeaveParameterObject::PARTIAL_OPTION_START_END
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Leave-OverlapLeaveModel"
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="empNumber", type="integer"),
     *                 @OA\Property(property="isWorkShiftLengthExceeded", type="boolean")
     *             )
     *         )
     *     ),
     * )
     *
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $this->getLeaveRequestService()->getLeaveRequestDao()->markApprovedLeaveAsTaken();
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_QUERY,
            CommonParams::PARAMETER_EMP_NUMBER,
            $this->getAuthUser()->getEmpNumber()
        );
        $leaveRequestParams = $this->getLeaveRequestParams($empNumber, LeaveOverlapParams::class);

        $overlapLeaves = [];
        $hasOverlapLeaves = $this->getLeaveApplicationService()->hasOverlapLeaves($leaveRequestParams);
        if ($hasOverlapLeaves) {
            $overlapLeaves = $this->getLeaveApplicationService()->getOverlapLeaves($leaveRequestParams);
        }

        $isWorkShiftLengthExceeded = false;
        if (!$hasOverlapLeaves) {
            $isWorkShiftLengthExceeded = $this->getLeaveApplicationService()
                ->isWorkShiftLengthExceeded($leaveRequestParams);
            if ($isWorkShiftLengthExceeded) {
                $overlapLeaves = $this->getLeaveApplicationService()
                    ->getWorkShiftLengthExceedOverlapLeaves($leaveRequestParams);
            }
        }
        return new EndpointCollectionResult(
            OverlapLeaveModel::class,
            $overlapLeaves,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    self::META_PARAMETER_IS_WORK_SHIFT_LENGTH_EXCEEDED => $isWorkShiftLengthExceeded,
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        $paramRules = $this->getCommonParamRuleCollection();
        $paramRules->removeParamValidation(LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID);
        $paramRules->removeParamValidation(LeaveCommonParams::PARAMETER_COMMENT);
        $paramRules->addParamValidation(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(CommonParams::PARAMETER_EMP_NUMBER, new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS))
            )
        );
        return $paramRules;
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
