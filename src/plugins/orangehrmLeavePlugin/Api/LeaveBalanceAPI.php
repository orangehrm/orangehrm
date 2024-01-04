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
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Entity\LeaveType;
use OrangeHRM\Leave\Api\Model\LeaveBalanceModel;
use OrangeHRM\Leave\Api\Model\LeavePeriodModel;
use OrangeHRM\Leave\Api\Model\LeaveTypeModel;
use OrangeHRM\Leave\Api\Traits\LeaveRequestParamHelperTrait;
use OrangeHRM\Leave\Dto\LeavePeriod;
use OrangeHRM\Leave\Service\LeaveApplicationService;
use OrangeHRM\Leave\Traits\Service\LeaveEntitlementServiceTrait;
use OrangeHRM\Leave\Traits\Service\LeaveTypeServiceTrait;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

class LeaveBalanceAPI extends Endpoint implements ResourceEndpoint
{
    use LeaveRequestParamHelperTrait;
    use LeaveEntitlementServiceTrait;
    use LeaveTypeServiceTrait;
    use EmployeeServiceTrait;
    use NormalizerServiceTrait;
    use DateTimeHelperTrait;
    use AuthUserTrait;

    public const PARAMETER_BALANCE = 'balance';

    public const META_PARAMETER_LEAVE_TYPE = 'leaveType';
    public const META_PARAMETER_EMPLOYEE = 'employee';

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
     * @return int
     */
    protected function getLeaveTypeIdParam(): int
    {
        return $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID
        );
    }

    /**
     * @return DateTime|null
     */
    protected function getFromDateParam(): ?DateTime
    {
        return $this->getRequestParams()->getDateTimeOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            LeaveCommonParams::PARAMETER_FROM_DATE
        );
    }

    /**
     * @return DateTime|null
     */
    protected function getToDateParam(): ?DateTime
    {
        return $this->getRequestParams()->getDateTimeOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            LeaveCommonParams::PARAMETER_TO_DATE
        );
    }

    /**
     * @param string $key
     * @param array|null $default
     * @return array|null
     */
    protected function getDurationParam(string $key, ?array $default = null): ?array
    {
        return $this->getRequestParams()->getArrayOrNull(RequestParams::PARAM_TYPE_QUERY, $key, $default);
    }

    /**
     * @return string|null
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
     *     path="/api/v2/leave/leave-balance/leave-type/{leaveTypeId}",
     *     tags={"Leave/Leave Balance"},
     *     summary="Get Leave Balance for a Leave Type",
     *     operationId="get-leave-balance-for-a-leave-type",
     *     @OA\PathParameter(
     *         name="leaveTypeId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="empNumber",
     *         in="query",
     *         required=false,
     *         description="Not needed if getting the leave balance of logged in user",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(
     *                         property="balance",
     *                         type="object",
     *                         @OA\Property(property="asAtDate", type="string", format="date"),
     *                         @OA\Property(property="balance", type="integer"),
     *                         @OA\Property(property="endDate", type="string", format="date"),
     *                         @OA\Property(property="entitled", type="integer"),
     *                         @OA\Property(property="pending", type="integer"),
     *                         @OA\Property(property="scheduled", type="integer"),
     *                         @OA\Property(property="taken", type="integer"),
     *                         @OA\Property(property="used", type="integer")
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(
     *                     property="employee",
     *                     ref="#/components/schemas/Pim-EmployeeModel"
     *                 ),
     *                 @OA\Property(
     *                     property="leaveType",
     *                     ref="#/components/schemas/Leave-LeaveTypeModel"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_QUERY,
            CommonParams::PARAMETER_EMP_NUMBER,
            $this->getAuthUser()->getEmpNumber()
        );

        $leaveTypeId = $this->getLeaveTypeIdParam();
        $startDate = $this->getFromDateParam();
        $endDate = $this->getToDateParam();

        $leaveByPeriods = [];
        if ($startDate instanceof DateTime & $endDate instanceof DateTime) {
            $leaveByPeriods = $this->getLeaveBreakdownForAppliedDateRange($empNumber, $leaveTypeId, $startDate);
        }

        if (!empty($leaveByPeriods)) {
            $result = $this->getNormalizedLeaveBalanceResult($leaveByPeriods, $empNumber, $leaveTypeId);
        } else {
            $asAtDate = $startDate ?? $this->getDateTimeHelper()->getNow();
            $balance = $this->getLeaveEntitlementService()->getLeaveBalance(
                $empNumber,
                $leaveTypeId,
                $asAtDate,
                $endDate ?? null
            );

            $result = [
                self::PARAMETER_BALANCE => $this->getNormalizerService()->normalize(LeaveBalanceModel::class, $balance),
            ];
        }

        return new EndpointResourceResult(
            ArrayModel::class,
            $result,
            new ParameterBag(
                [
                    self::META_PARAMETER_EMPLOYEE => $this->getEmployeeService()->getEmployeeAsArray($empNumber),
                    self::META_PARAMETER_LEAVE_TYPE => $this->getLeaveTypeAsArray($leaveTypeId),
                ]
            )
        );
    }

    /**
     * @param int $leaveTypeId
     * @return array|null
     */
    private function getLeaveTypeAsArray(int $leaveTypeId): ?array
    {
        $leaveType = $this->getLeaveTypeService()->getLeaveTypeDao()->getLeaveTypeById($leaveTypeId);
        if (!$leaveType instanceof LeaveType) {
            return null;
        }
        return $this->getNormalizerService()->normalize(LeaveTypeModel::class, $leaveType);
    }

    /**
     * @param array $leaveByPeriods
     * @param int $empNumber
     * @param int $leaveTypeId
     * @return array {
     *     negative: true,
     *     breakdown: [
     *         {
     *             period: {
     *                 startDate: 2021-01-01,
     *                 endDate: 2021-12-31,
     *             },
     *             balance: {
     *                 entitled: 4,
     *                 used: 3.5,
     *                 scheduled: 0,
     *                 pending: 3,
     *                 taken: 0.5,
     *                 balance: 0.5,
     *                 asAtDate: 2021-08-17,
     *                 endDate: 2021-12-31,
     *             },
     *             leaves: [
     *                 {
     *                     balance: -0.5,
     *                     date: 2021-08-17,
     *                     length: 1,
     *                     status: null,
     *                 },
     *                 {
     *                     balance: -0.5,
     *                     date: 2021-08-18,
     *                     length: 0,
     *                     status: {
     *                         key: 5,
     *                         name: 'Holiday',
     *                     },
     *                 },
     *             ],
     *         }
     *     ]
     * }
     */
    private function getNormalizedLeaveBalanceResult(array $leaveByPeriods, int $empNumber, int $leaveTypeId): array
    {
        $negativeBalance = false;
        foreach ($leaveByPeriods as $leavePeriodIndex => $leavePeriod) {
            $days = $leavePeriod['days'];

            if (is_null($leavePeriod['period'])) {
                // Handle past leave period
                $negativeBalance = true;
                unset($leaveByPeriods[$leavePeriodIndex]['days']);
                $leaveByPeriods[$leavePeriodIndex]['leaves'] = [];
                continue;
            }
            $firstDayInPeriod = ($leavePeriod['period'])->getStartDate();
            $lastDayInPeriod = ($leavePeriod['period'])->getEndDate();
            $dayKeys = array_keys($days);
            $firstDay = array_shift($dayKeys);
            if ($firstDay) {
                $firstDayInPeriod = new DateTime($firstDay);
            }
            $lastDay = array_pop($dayKeys);
            if ($lastDay) {
                $lastDayInPeriod = new DateTime($lastDay);
            }

            $leaveBalanceObj = $this->getLeaveEntitlementService()
                ->getLeaveBalance($empNumber, $leaveTypeId, $firstDayInPeriod, $lastDayInPeriod);

            $leaveByPeriods[$leavePeriodIndex]['balance'] = $this->getNormalizerService()
                ->normalize(LeaveBalanceModel::class, $leaveBalanceObj);
            $leaveByPeriods[$leavePeriodIndex]['period'] = $this->getNormalizerService()
                ->normalize(LeavePeriodModel::class, $leaveByPeriods[$leavePeriodIndex]['period']);

            $leaveBalance = $leaveBalanceObj->getBalance();
            foreach ($days as $date => $leaveDate) {
                $leaveDateLength = $leaveDate['length'];
                if ($leaveDateLength > 0) {
                    $leaveBalance -= $leaveDateLength;
                }
                $leaveByPeriods[$leavePeriodIndex]['leaves'][] = [
                    'balance' => $leaveBalance,
                    'date' => $date,
                    'length' => $leaveDate['length'],
                    'status' => $leaveDate['status'],
                ];
            }
            unset($leaveByPeriods[$leavePeriodIndex]['days']);

            if ($leaveBalance < 0) {
                $negativeBalance = true;
            }
        }

        return [
            'negative' => $negativeBalance,
            'breakdown' => $leaveByPeriods,
        ];
    }

    /**
     * @param int $empNumber
     * @param int $leaveTypeId
     * @param DateTime $startDate
     * @return array {
     *     period: LeavePeriod,
     *     days: {
     *         2021-08-19: {
     *             length: 1,
     *             status: null,
     *         },
     *         2021-08-20: {
     *             length: 1,
     *             status: {
     *                 key: 5,
     *                 name: 'Holiday',
     *             },
     *         },
     *         2021-08-21: {
     *             length: 1,
     *             status: {
     *                 key: 4,
     *                 name: 'Weekend',
     *             },
     *         },
     *     }
     * }
     */
    private function getLeaveBreakdownForAppliedDateRange(int $empNumber, int $leaveTypeId, DateTime $startDate): array
    {
        $leaveRequestParams = $this->getLeaveRequestParams($empNumber);
        $leaveDays = $this->getLeaveApplicationService()
            ->createLeaveObjectListForAppliedRange($leaveRequestParams);
        $holidays = [Leave::LEAVE_STATUS_LEAVE_WEEKEND, Leave::LEAVE_STATUS_LEAVE_HOLIDAY];

        $currentLeavePeriod = $this->getLeavePeriod($empNumber, $leaveTypeId, $startDate);
        $leavePeriodIndex = 0;
        $leaveByPeriods[$leavePeriodIndex] = [
            'period' => $currentLeavePeriod,
            'days' => []
        ];

        foreach ($leaveDays as $leave) {
            $leaveDate = $leave->getDate();

            // Get next leave period if request spans leave periods.
            if (!is_null($leaveByPeriods[$leavePeriodIndex]['period']) &&
                $leaveDate > ($leaveByPeriods[$leavePeriodIndex]['period'])->getEndDate()) {
                $currentLeavePeriod = $this->getLeavePeriod($empNumber, $leaveTypeId, $leaveDate);
                $leavePeriodIndex++;
                $leaveByPeriods[$leavePeriodIndex] = [
                    'period' => $currentLeavePeriod,
                    'days' => []
                ];
            }

            if (in_array($leave->getStatus(), $holidays)) {
                $length = 0;
                $status = [
                    'key' => $leave->getStatus(),
                    'name' => $leave->getDecorator()->getLeaveStatus(),
                ];
            } else {
                $length = $leave->getLengthDays();
                $status = null;
            }

            $leaveByPeriods[$leavePeriodIndex]['days'][$this->getDateTimeHelper()->formatDateTimeToYmd($leaveDate)] = [
                'length' => $length,
                'status' => $status,
            ];
        }

        return $leaveByPeriods;
    }

    /**
     * @param int $empNumber
     * @param int $leaveTypeId
     * @param DateTime $date
     * @return LeavePeriod|null
     */
    protected function getLeavePeriod(int $empNumber, int $leaveTypeId, DateTime $date): ?LeavePeriod
    {
        $strategy = $this->getLeaveEntitlementService()->getLeaveEntitlementStrategy();
        return $strategy->getLeavePeriod($date, $empNumber, $leaveTypeId);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        $paramRules = $this->getCommonParamRuleCollection();
        $paramRules->removeParamValidation(LeaveCommonParams::PARAMETER_COMMENT);
        $paramRules->addParamValidation(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(CommonParams::PARAMETER_EMP_NUMBER, new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS))
            )
        );
        if (!$this->getRequestParams()->has(RequestParams::PARAM_TYPE_QUERY, LeaveCommonParams::PARAMETER_TO_DATE)) {
            $paramRules->addParamValidation(
                $this->getValidationDecorator()->notRequiredParamRule(
                    $paramRules->removeParamValidation(LeaveCommonParams::PARAMETER_FROM_DATE)
                )
            );
        }
        if (!$this->getRequestParams()->has(RequestParams::PARAM_TYPE_QUERY, LeaveCommonParams::PARAMETER_FROM_DATE)) {
            $paramRules->addParamValidation(
                $this->getValidationDecorator()->notRequiredParamRule(
                    $paramRules->removeParamValidation(LeaveCommonParams::PARAMETER_TO_DATE)
                )
            );
        }
        return $paramRules;
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
