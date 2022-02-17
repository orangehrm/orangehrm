<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Attendance\Api;

use DateTime;
use DateTimeZone;
use Exception;
use OrangeHRM\Attendance\Api\Model\AttendanceRecordModel;
use OrangeHRM\Attendance\Exception\AttendanceServiceException;
use OrangeHRM\Attendance\Traits\Service\AttendanceServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Api\V2\RequestParams;
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

class EmployeeAttendanceRecordAPI extends Endpoint implements CrudEndpoint
{
    use AttendanceServiceTrait;
    use AuthUserTrait;
    use DateTimeHelperTrait;
    use UserRoleManagerTrait;

    public const PARAMETER_DATE = 'date';
    public const PARAMETER_TIME = 'time';
    public const PARAMETER_TIMEZONE_OFFSET = 'timezoneOffset';
    public const PARAMETER_NOTE = 'note';

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create(): EndpointResourceResult
    {
        try {
            list($empNumber, $date, $time, $timezoneOffset, $note) = $this->getCommonRequestParams();
            $allowedWorkflowItems = $this->getUserRoleManager()->getAllowedActions(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_INITIAL,
                [],
                [],
                [Employee::class => $empNumber]
            );
            $this->userAllowedPunchInActions(array_keys($allowedWorkflowItems));
            $attendanceRecord = new AttendanceRecord();
            $attendanceRecord->getDecorator()->setEmployeeByEmpNumber($empNumber);
            $punchInDateTime = $this->extractPunchDateTime($date.' '.$time, $timezoneOffset);
            $punchInUTCDateTime = (clone $punchInDateTime)->setTimezone(
                new DateTimeZone(DateTimeHelperService::TIMEZONE_UTC)
            );
            $overlappingPunchInRecords = $this->getAttendanceService()
                ->getAttendanceDao()
                ->checkForPunchInOverLappingRecords($punchInUTCDateTime, $empNumber);
            if ($overlappingPunchInRecords) {
                throw AttendanceServiceException::punchInOverlapFound();
            }
            $this->setPunchInAttendanceRecord(
                $attendanceRecord,
                AttendanceRecord::STATE_PUNCHED_IN,
                $punchInUTCDateTime,
                $punchInDateTime,
                $timezoneOffset,
                $note
            );
            $attendanceRecord = $this->getAttendanceService()->getAttendanceDao()->savePunchRecord($attendanceRecord);
            return new EndpointResourceResult(AttendanceRecordModel::class, $attendanceRecord);
        } catch (AttendanceServiceException $e) {
            throw $this->getBadRequestException($e->getMessage());
        }
    }

    /**
     * @return array
     */
    protected function getCommonRequestParams(): array
    {
        return [
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_ATTRIBUTE,
                CommonParams::PARAMETER_EMP_NUMBER,
                $this->getAuthUser()->getEmpNumber()
            ),
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_DATE
            ),
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_TIME
            ),
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_TIMEZONE_OFFSET
            ),
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_NOTE
            )
        ];
    }

    /**
     * @param  array  $allowedActions
     * @return void
     * @throws ForbiddenException
     */
    protected function userAllowedPunchInActions(array $allowedActions): void
    {
        if (!in_array(
            WorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_IN,
            $allowedActions
        )) {
            throw $this->getForbiddenException();
        }
    }

    /**
     * @param  string  $dateTime
     * @param  float  $timezoneOffset
     * @return DateTime
     * @throws Exception
     */
    protected function extractPunchDateTime(string $dateTime, float $timezoneOffset): DateTime
    {
        return new DateTime(
            $dateTime,
            $this->getDateTimeHelper()->getTimezoneByTimezoneOffset($timezoneOffset)
        );
    }

    /**
     * @param  AttendanceRecord  $attendanceRecord
     * @param  string  $state
     * @param  DateTime  $punchInUtcTime
     * @param  DateTime  $punchInUserTime
     * @param  float  $punchInTimezoneOffset
     * @param  string|null  $punchInNote
     */
    protected function setPunchInAttendanceRecord(
        AttendanceRecord $attendanceRecord,
        string $state,
        DateTime $punchInUtcTime,
        DateTime $punchInUserTime,
        float $punchInTimezoneOffset,
        ?string $punchInNote
    ): void {
        $attendanceRecord->setState($state);
        $attendanceRecord->setPunchInUtcTime($punchInUtcTime);
        $attendanceRecord->setPunchInUserTime($punchInUserTime);
        $attendanceRecord->setPunchInTimeOffset($punchInTimezoneOffset);
        $attendanceRecord->setPunchInNote($punchInNote);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getCommonValidationRules()
        );
    }

    /**
     * @return array
     */
    protected function getCommonValidationRules(): array
    {
        return [
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    CommonParams::PARAMETER_EMP_NUMBER,
                    new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS, [false])
                )
            ),
            new ParamRule(
                self::PARAMETER_DATE,
                new Rule(Rules::API_DATE)
            ),
            new ParamRule(
                self::PARAMETER_TIME,
                new Rule(Rules::TIME, ['H:i'])
            ),
            new ParamRule(
                self::PARAMETER_TIMEZONE_OFFSET,
                new Rule(Rules::FLOAT_TYPE)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_NOTE,
                    new Rule(Rules::STRING_TYPE)
                )
            )
        ];
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
     * @inheritDoc
     * @throws Exception
     */
    public function update(): EndpointResult
    {
        try {
            list($empNumber, $date, $time, $timezoneOffset, $note) = $this->getCommonRequestParams();
            $allowedWorkflowItems = $this->getUserRoleManager()->getAllowedActions(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_IN,
                [],
                [],
                [Employee::class => $empNumber]
            );
            $this->userAllowedPunchOutActions(array_keys($allowedWorkflowItems));
            $lastPunchInRecord = $this->getAttendanceService()
                ->getAttendanceDao()
                ->getLastPunchRecordByEmployeeNumberAndActionableList($empNumber, [AttendanceRecord::STATE_PUNCHED_IN]);
            if (is_null($lastPunchInRecord)) {
                throw AttendanceServiceException::punchOutAlreadyExist();
            }
            $punchOutDateTime = $this->extractPunchDateTime($date.' '.$time, $timezoneOffset);
            $punchOutUTCDateTime = (clone $punchOutDateTime)->setTimezone(
                new DateTimeZone(DateTimeHelperService::TIMEZONE_UTC)
            );
            $overlappingPunchOutRecords = $this->getAttendanceService()
                ->getAttendanceDao()
                ->checkForPunchOutOverLappingRecords($punchOutUTCDateTime, $empNumber);
            if (!$overlappingPunchOutRecords) {
                throw AttendanceServiceException::punchOutOverlapFound();
            }
            $this->setPunchOutAttendanceRecord(
                $lastPunchInRecord,
                AttendanceRecord::STATE_PUNCHED_OUT,
                $punchOutUTCDateTime,
                $punchOutDateTime,
                $timezoneOffset,
                $note
            );
            $attendanceRecord = $this->getAttendanceService()->getAttendanceDao()->savePunchRecord($lastPunchInRecord);
            return new EndpointResourceResult(AttendanceRecordModel::class, $attendanceRecord);
        } catch (AttendanceServiceException $e) {
            throw $this->getBadRequestException($e->getMessage());
        }
    }

    /**
     * @param  array  $allowedActions
     * @return void
     * @throws ForbiddenException
     */
    protected function userAllowedPunchOutActions(array $allowedActions): void
    {
        if (!in_array(
            WorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_OUT,
            $allowedActions
        )) {
            throw $this->getForbiddenException();
        }
    }

    /**
     * @param  AttendanceRecord  $attendanceRecord
     * @param  string  $state
     * @param  DateTime  $punchOutUtcTime
     * @param  DateTime  $punchOutUserTime
     * @param  float  $punchOutTimezoneOffset
     * @param  string|null  $punchOutNote
     */
    protected function setPunchOutAttendanceRecord(
        AttendanceRecord $attendanceRecord,
        string $state,
        DateTime $punchOutUtcTime,
        DateTime $punchOutUserTime,
        float $punchOutTimezoneOffset,
        ?string $punchOutNote
    ): void {
        $attendanceRecord->setState($state);
        $attendanceRecord->setPunchOutUtcTime($punchOutUtcTime);
        $attendanceRecord->setPunchOutUserTime($punchOutUserTime);
        $attendanceRecord->setPunchOutTimeOffset($punchOutTimezoneOffset);
        $attendanceRecord->setPunchOutNote($punchOutNote);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getCommonValidationRules()
        );
    }
}
