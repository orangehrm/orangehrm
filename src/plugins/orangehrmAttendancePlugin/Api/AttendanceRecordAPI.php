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
use OrangeHRM\Attendance\Api\Model\AttendanceRecordModel;
use OrangeHRM\Attendance\Api\Model\DetailedAttendanceRecordModel;
use OrangeHRM\Attendance\Exception\AttendanceServiceException;
use OrangeHRM\Attendance\Traits\Service\AttendanceServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\AttendanceRecord;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\WorkflowStateMachine;

class AttendanceRecordAPI extends Endpoint implements ResourceEndpoint
{
    use AttendanceServiceTrait;
    use AuthUserTrait;
    use UserRoleManagerTrait;
    use DateTimeHelperTrait;

    public const PARAMETER_PUNCH_IN_DATE = 'punchInDate';
    public const PARAMETER_PUNCH_IN_TIME = 'punchInTime';
    public const PARAMETER_PUNCH_IN_NOTE = 'punchInNote';
    public const PARAMETER_PUNCH_IN_OFFSET = 'punchInOffset';
    public const PARAMETER_PUNCH_IN_TIMEZONE_NAME = 'punchInTimezoneName';
    public const PARAMETER_PUNCH_OUT_DATE = 'punchOutDate';
    public const PARAMETER_PUNCH_OUT_TIME = 'punchOutTime';
    public const PARAMETER_PUNCH_OUT_NOTE = 'punchOutNote';
    public const PARAMETER_PUNCH_OUT_OFFSET = 'punchOutOffset';
    public const PARAMETER_PUNCH_OUT_TIMEZONE_NAME = 'punchOutTimezoneName';

    /**
     * @OA\Get(
     *     path="/api/v2/attendance/records/{id}",
     *     tags={"Attendance/Attendance Record"},
     *     summary="List an Attendance Record",
     *     operationId="list-an-attendance-record",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Attendance Record Id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Attendance-DetailedAttendanceRecordModel"
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
        $recordId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
        $attendanceRecord = $this->getAttendanceService()->getAttendanceDao()->getAttendanceRecordById($recordId);
        $this->throwRecordNotFoundExceptionIfNotExist($attendanceRecord, AttendanceRecord::class);
        if (!$this->isAuthUserAllowedToPerformTheActions($attendanceRecord)) {
            throw $this->getForbiddenException();
        }
        return new EndpointResourceResult(DetailedAttendanceRecordModel::class, $attendanceRecord);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            )
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v2/attendance/records/{id}",
     *     tags={"Attendance/Attendance Record"},
     *     summary="Update an Attendance Record",
     *     operationId="update-an-attendance-record",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Attendance Record Id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="punchInDate", type="string", format="date"),
     *             @OA\Property(property="punchInTime", type="string"),
     *             @OA\Property(property="punchInOffset", type="number"),
     *             @OA\Property(property="punchInTimezoneName", type="string"),
     *             @OA\Property(property="punchOutDate", type="string", format="date"),
     *             @OA\Property(property="punchOutTime", type="string"),
     *             @OA\Property(property="punchOutOffset", type="number"),
     *             @OA\Property(property="punchOutTimezoneName", type="string"),
     *             @OA\Property(property="punchOutNote", type="string"),
     *             required={"date", "name", "time", "timezoneOffset", "timezoneName"}
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Attendance-AttendanceRecordModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound"),
     *     @OA\Response(
     *         response="400",
     *         description="Bad Request - punch in/out overlap",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 oneOf={
     *                     @OA\Property(
     *                         property="error",
     *                         type="object",
     *                         @OA\Property(property="status", type="string", default="400"),
     *                         @OA\Property(property="message", type="string", default="Punch-In Overlap Found")
     *                     ),
     *                     @OA\Property(
     *                         property="error",
     *                         type="object",
     *                         @OA\Property(property="status", type="string", default="400"),
     *                         @OA\Property(property="message", type="string", default="Punch-Out Overlap Found")
     *                     ),
     *                 }
     *             )
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $recordId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
        $attendanceRecord = $this->getAttendanceService()->getAttendanceDao()->getAttendanceRecordById($recordId);
        $this->throwRecordNotFoundExceptionIfNotExist($attendanceRecord, AttendanceRecord::class);
        if (!$this->isAuthUserAllowedToPerformTheActions($attendanceRecord)) {
            throw $this->getForbiddenException();
        }
        $attendanceRecord = $this->setAttendanceRecord($attendanceRecord);
        $attendanceRecord = $this->getAttendanceService()->getAttendanceDao()->savePunchRecord($attendanceRecord);
        return new EndpointResourceResult(AttendanceRecordModel::class, $attendanceRecord);
    }

    /**
     * @param AttendanceRecord $attendanceRecord
     * @return bool
     */
    private function isAuthUserAllowedToPerformTheActions(AttendanceRecord $attendanceRecord): bool
    {
        $attendanceRecordOwnedEmpNumber = $attendanceRecord->getEmployee()->getEmpNumber();
        $loggedInUserEmpNumber = $this->getAuthUser()->getEmpNumber();
        $rolesToInclude = [];
        //check the configuration as ESS since Admin user is always allowed to update
        if ($attendanceRecordOwnedEmpNumber === $loggedInUserEmpNumber) {
            $rolesToInclude = ['ESS'];
        }
        //If edit own attendance record, get the allowed actions list as an ESS user
        //If edit for someone, get the allowed actions list as a Supervisor
        //Admin is always allowed to edit others records
        $allowedWorkflowItems = $this->getUserRoleManager()->getAllowedActions(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            $attendanceRecord->getState(),
            [],
            $rolesToInclude,
            [Employee::class => $attendanceRecordOwnedEmpNumber]
        );
        $workflowItem = $attendanceRecord->getState() === AttendanceRecord::STATE_PUNCHED_IN ?
            WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME :
            WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_OUT_TIME;
        //check whether work flow item allowed for the user
        if (!in_array($workflowItem, array_keys($allowedWorkflowItems))) {
            return false;
        }
        return true;
    }

    /**
     * @throws BadRequestException
     */
    private function setAttendanceRecord(AttendanceRecord $attendanceRecord): AttendanceRecord
    {
        try {
            list(
                $punchInDate,
                $punchInTime,
                $punchInOffset,
                $punchInTimezoneName,
                $punchInNote,
                $punchOutDate,
                $punchOutTime,
                $punchOutOffset,
                $punchOutTimezoneName,
                $punchOutNote
            ) = $this->getRequestBodyParams();

            $recordId = $attendanceRecord->getId();
            $attendanceRecordOwnedEmpNumber = $attendanceRecord->getEmployee()->getEmpNumber();
            if ($this->isAllowedToEditTimezone($punchInOffset, $punchInTimezoneName, $attendanceRecordOwnedEmpNumber)) {
                $punchInTimezoneOffset = $punchInOffset;
            } else {
                $punchInTimezoneOffset = $attendanceRecord->getPunchInTimeOffset();
            }
            $punchInDateTime = $this->extractPunchDateTime($punchInDate . ' ' . $punchInTime, $punchInTimezoneOffset);
            $punchInUTCDateTime = (clone $punchInDateTime)->setTimezone(
                new DateTimeZone(DateTimeHelperService::TIMEZONE_UTC)
            );
            //current state is punched in and editing it
            if ($attendanceRecord->getState() === AttendanceRecord::STATE_PUNCHED_IN) {
                $overlappingAttendanceRecords = $this->getAttendanceService()
                    ->getAttendanceDao()
                    ->checkForPunchInOverLappingRecordsWhenEditing(
                        $punchInUTCDateTime,
                        $attendanceRecordOwnedEmpNumber,
                        $recordId
                    );
                if ($overlappingAttendanceRecords) {
                    throw AttendanceServiceException::punchInOverlapFound();
                }
                //if there are no overlaps
                $attendanceRecord->setPunchInUserTime($punchInDateTime);
                $attendanceRecord->setPunchInUtcTime($punchInUTCDateTime);
                $attendanceRecord->setPunchInNote($punchInNote);
                if ($this->isAllowedToEditTimezone($punchInOffset, $punchInTimezoneName)) {
                    $attendanceRecord->setPunchInTimeOffset($punchInOffset);
                    $attendanceRecord->setPunchInTimezoneName($punchInTimezoneName);
                }
            } //current state is punched out and editing it
            else {
                if (is_null($punchOutDate) || is_null($punchOutTime)) {
                    throw AttendanceServiceException::punchOutDateTimeNull();
                }

                if ($this->isAllowedToEditTimezone(
                    $punchOutOffset,
                    $punchOutTimezoneName,
                )) {
                    $punchOutTimezoneOffset = $punchOutOffset;
                } else {
                    $punchOutTimezoneOffset = $attendanceRecord->getPunchOutTimeOffset();
                }

                $punchOutDateTime = $this->extractPunchDateTime(
                    $punchOutDate . ' ' . $punchOutTime,
                    $punchOutTimezoneOffset
                );
                $punchOutUTCDateTime = (clone $punchOutDateTime)->setTimezone(
                    new DateTimeZone(DateTimeHelperService::TIMEZONE_UTC)
                );
                $punchInOverlappingAttendanceRecords = $this->getAttendanceService()
                    ->getAttendanceDao()
                    ->checkForPunchInOverLappingRecordsWhenEditing(
                        $punchInUTCDateTime,
                        $attendanceRecordOwnedEmpNumber,
                        $recordId,
                        $punchOutUTCDateTime
                    );
                if ($punchInOverlappingAttendanceRecords) {
                    throw AttendanceServiceException::punchInOverlapFound();
                }
                $punchOutOverlappingAttendanceRecords = $this->getAttendanceService()
                    ->getAttendanceDao()
                    ->checkForPunchInOutOverLappingRecordsWhenEditing(
                        $punchInUTCDateTime,
                        $punchOutUTCDateTime,
                        $attendanceRecordOwnedEmpNumber,
                        $recordId,
                    );
                if (!$punchOutOverlappingAttendanceRecords) {
                    throw AttendanceServiceException::punchOutOverlapFound();
                }
                //if there are no overlaps
                $attendanceRecord->setPunchInUserTime($punchInDateTime);
                $attendanceRecord->setPunchInUtcTime($punchInUTCDateTime);
                $attendanceRecord->setPunchInNote($punchInNote);
                if ($this->isAllowedToEditTimezone($punchInOffset, $punchInTimezoneName)) {
                    $attendanceRecord->setPunchInTimeOffset($punchInOffset);
                    $attendanceRecord->setPunchInTimezoneName($punchInTimezoneName);
                }
                $attendanceRecord->setPunchOutUserTime($punchOutDateTime);
                $attendanceRecord->setPunchOutUtcTime($punchOutUTCDateTime);
                $attendanceRecord->setPunchOutNote($punchOutNote);
                if ($this->isAllowedToEditTimezone(
                    $punchOutOffset,
                    $punchOutTimezoneName,
                )) {
                    $attendanceRecord->setPunchOutTimeOffset($punchOutOffset);
                    $attendanceRecord->setPunchOutTimezoneName($punchOutTimezoneName);
                }
            }
            return $attendanceRecord;
        } catch (AttendanceServiceException $e) {
            throw $this->getBadRequestException($e->getMessage());
        }
    }

    /**
     * @return array
     */
    private function getRequestBodyParams(): array
    {
        return [
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_PUNCH_IN_DATE
            ),
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_PUNCH_IN_TIME
            ),
            $this->getRequestParams()->getFloatOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_PUNCH_IN_OFFSET
            ),
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_PUNCH_IN_TIMEZONE_NAME
            ),
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_PUNCH_IN_NOTE
            ),
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_PUNCH_OUT_DATE
            ),
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_PUNCH_OUT_TIME
            ),
            $this->getRequestParams()->getFloatOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_PUNCH_OUT_OFFSET
            ),
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_PUNCH_OUT_TIMEZONE_NAME
            ),
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_PUNCH_OUT_NOTE
            )
        ];
    }

    /**
     * @param string $dateTime
     * @param float $timezoneOffset
     * @return DateTime
     */
    protected function extractPunchDateTime(string $dateTime, float $timezoneOffset): DateTime
    {
        return new DateTime(
            $dateTime,
            $this->getDateTimeHelper()->getTimezoneByTimezoneOffset($timezoneOffset)
        );
    }

    /**
     * @param float|null $timezoneOffset
     * @param string|null $timezoneName
     * @return bool
     * @throws AttendanceServiceException
     */
    protected function isAllowedToEditTimezone(
        ?float $timezoneOffset,
        ?string $timezoneName
    ): bool {
        //auth user trying to update employee timezone, but either timezoneOffset or timezoneName
        //or both of them are missing
        if (is_null($timezoneOffset) && is_null($timezoneName)) {
            return false;
        } elseif ((is_null($timezoneOffset) && !is_null($timezoneName)) ||
            (!is_null($timezoneOffset) && is_null($timezoneName))) {
            throw AttendanceServiceException::invalidTimezoneDetails();
        } //auth user tyring to update employee timezone with valid timezoneOffset and timezoneName
        else {
            return true;
        }
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            new ParamRule(
                self::PARAMETER_PUNCH_IN_DATE,
                new Rule(Rules::API_DATE)
            ),
            new ParamRule(
                self::PARAMETER_PUNCH_IN_TIME,
                new Rule(Rules::TIME, ['H:i'])
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_PUNCH_IN_OFFSET,
                    new Rule(Rules::TIMEZONE_OFFSET)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_PUNCH_IN_TIMEZONE_NAME,
                    new Rule(Rules::TIMEZONE_NAME)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_PUNCH_IN_NOTE,
                    new Rule(Rules::STRING_TYPE)
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_PUNCH_OUT_DATE,
                    new Rule(Rules::API_DATE)
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_PUNCH_OUT_TIME,
                    new Rule(Rules::TIME, ['H:i'])
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_PUNCH_OUT_OFFSET,
                    new Rule(Rules::TIMEZONE_OFFSET)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_PUNCH_OUT_TIMEZONE_NAME,
                    new Rule(Rules::TIMEZONE_NAME)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_PUNCH_OUT_NOTE,
                    new Rule(Rules::STRING_TYPE)
                ),
                true
            )
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
