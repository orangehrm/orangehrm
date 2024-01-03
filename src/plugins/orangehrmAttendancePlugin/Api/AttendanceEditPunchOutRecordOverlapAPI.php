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

namespace OrangeHRM\Attendance\Api;

use DateTime;
use DateTimeZone;
use OrangeHRM\Attendance\Api\Traits\AttendanceRecordPermissionTrait;
use OrangeHRM\Attendance\Exception\AttendanceServiceException;
use OrangeHRM\Attendance\Traits\Service\AttendanceServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;

class AttendanceEditPunchOutRecordOverlapAPI extends Endpoint implements ResourceEndpoint
{
    use DateTimeHelperTrait;
    use AuthUserTrait;
    use AttendanceServiceTrait;
    use AttendanceRecordPermissionTrait;

    public const PARAMETER_RECORD_ID = 'recordId';
    public const PARAMETER_PUNCH_IN_DATE = 'punchInDate';
    public const PARAMETER_PUNCH_IN_TIME = 'punchInTime';
    public const PARAMETER_PUNCH_OUT_DATE = 'punchOutDate';
    public const PARAMETER_PUNCH_OUT_TIME = 'punchOutTime';
    public const PARAMETER_PUNCH_IN_TIME_ZONE_OFFSET = 'punchInTimezoneOffset';
    public const PARAMETER_PUNCH_OUT_TIME_ZONE_OFFSET = 'punchOutTimezoneOffset';
    public const PARAMETER_IS_PUNCH_OUT_OVERLAP = 'valid';

    /**
     * @OA\Get(
     *     path="/api/v2/attendance/records/punch-out-overlaps",
     *     tags={"Attendance/Overlap"},
     *     summary="Check Punch Out Overlap (Editing)",
     *     operationId="check-punch-out-overlap-editing",
     *     @OA\Parameter(
     *         name="recordId",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="punchInTimezoneOffset",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="punchInDate",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="punchInTime",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="punchOutTimezoneOffset",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="punchOutDate",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="punchOutTime",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="valid", type="boolean")
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        try {
            $recordId = $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_QUERY,
                self::PARAMETER_RECORD_ID
            );

            $attendanceRecord = $this->getAttendanceService()
                ->getAttendanceDao()
                ->getAttendanceRecordById($recordId);

            $this->throwRecordNotFoundExceptionIfNotExist($attendanceRecord);
            $this->checkAttendanceRecordAccessible($attendanceRecord);
            list($punchInUtc, $punchOutUtc) = $this->getUTCTimeByOffsetAndDateTime();

            $isPunchInOverlap = $this->getAttendanceService()
                ->getAttendanceDao()
                ->checkForPunchInOutOverLappingRecordsWhenEditing(
                    $punchInUtc,
                    $punchOutUtc,
                    $attendanceRecord->getEmployee()->getEmpNumber(),
                    $recordId
                );

            return new EndpointResourceResult(
                ArrayModel::class,
                [
                    self::PARAMETER_IS_PUNCH_OUT_OVERLAP => $isPunchInOverlap,
                ]
            );
        } catch (AttendanceServiceException $attendanceServiceException) {
            throw $this->getBadRequestException($attendanceServiceException->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        $paramRules = new ParamRuleCollection(
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_RECORD_ID,
                    new Rule(Rules::POSITIVE)
                )
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_PUNCH_IN_DATE,
                    new Rule(Rules::API_DATE)
                )
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_PUNCH_OUT_DATE,
                    new Rule(Rules::API_DATE)
                )
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_PUNCH_IN_TIME,
                    new Rule(Rules::TIME, ['H:i'])
                )
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_PUNCH_OUT_TIME,
                    new Rule(Rules::TIME, ['H:i'])
                )
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_PUNCH_IN_TIME_ZONE_OFFSET,
                    new Rule(Rules::STRING_TYPE)
                )
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_PUNCH_OUT_TIME_ZONE_OFFSET,
                    new Rule(Rules::STRING_TYPE)
                )
            )
        );
        $paramRules->addExcludedParamKey(CommonParams::PARAMETER_ID);
        return $paramRules;
    }

    /**
     * @return array
     */
    protected function getUTCTimeByOffsetAndDateTime(): array
    {
        $punchInDate = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_PUNCH_IN_DATE,
        );

        $punchOutDate = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_PUNCH_OUT_DATE,
        );

        $punchInTime = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_PUNCH_IN_TIME,
        );

        $punchOutTime = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_PUNCH_OUT_TIME,
        );

        $punchInTimezoneOffset = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_PUNCH_IN_TIME_ZONE_OFFSET,
        );

        $punchOutTimezoneOffset = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_PUNCH_OUT_TIME_ZONE_OFFSET,
        );

        $punchInDateTime = $punchInDate . ' ' . $punchInTime;
        $punchOutDateTime = $punchOutDate . ' ' . $punchOutTime;

        $punchInDateTime = new DateTime(
            $punchInDateTime,
            $this->getDateTimeHelper()->getTimezoneByTimezoneOffset($punchInTimezoneOffset)
        );

        $punchOutDateTime = new DateTime(
            $punchOutDateTime,
            $this->getDateTimeHelper()->getTimezoneByTimezoneOffset($punchOutTimezoneOffset)
        );

        return [
            $punchInDateTime->setTimezone(new DateTimeZone(DateTimeHelperService::TIMEZONE_UTC)),
            $punchOutDateTime->setTimezone(new DateTimeZone(DateTimeHelperService::TIMEZONE_UTC))
        ];
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
