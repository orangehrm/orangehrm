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
use OrangeHRM\Attendance\Traits\Service\AttendanceServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
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
    public const PARAMETER_PUNCH_OUT_DATE = 'punchOutDate';
    public const PARAMETER_PUNCH_OUT_TIME = 'punchOutTime';
    public const PARAMETER_PUNCH_OUT_NOTE = 'punchOutNote';

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
        $id = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
        //get the attendance record from id
        $attendanceRecord = $this->getAttendanceService()->getAttendanceDao()->getAttendanceRecordById($id);
        $attendanceRecordOwnedEmpNumber = $attendanceRecord->getEmployee()->getEmpNumber();
        $loggedInUserEmpNumber = $this->getAuthUser()->getEmpNumber();

        //If own attendance record, get the allowed actions list as an ESS user
        if ($loggedInUserEmpNumber === $attendanceRecordOwnedEmpNumber) {
            $allowedWorkflowItems = $this->getUserRoleManager()->getAllowedActions(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                $attendanceRecord->getState(),
                [],
                [],
                [Employee::class => $loggedInUserEmpNumber]
            );
        } //If edit for others, get the allowed actions list as a Supervisor
        else {
            $allowedWorkflowItems = $this->getUserRoleManager()->getAllowedActions(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                $attendanceRecord->getState(),
                [],
                [],
                [Employee::class => $attendanceRecordOwnedEmpNumber]
            );
        }
        //check whether edit action allowed for the user
        if (!in_array(WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME, array_keys($allowedWorkflowItems))) {
            throw $this->getForbiddenException();
        }

        list(
            $punchInDate,
            $punchInTime,
            $punchInNote,
            $punchOutDate,
            $punchOutTime,
            $punchOutNote
            ) = $this->getRequestBodyParams();

        //current state is punched in and editing it
        $punchInTimezoneOffset = $attendanceRecord->getPunchInTimeOffset();
        $punchInDateTime = $this->extractPunchDateTime($punchInDate.' '.$punchInTime, $punchInTimezoneOffset);
        $punchInUTCDateTime = (clone $punchInDateTime)->setTimezone(
            new DateTimeZone(DateTimeHelperService::TIMEZONE_UTC)
        );
        //TODO::check for overlap
        //if there are no overlaps
        $attendanceRecord->setPunchInUserTime($punchInDateTime);
        $attendanceRecord->setPunchInUtcTime($punchInUTCDateTime);
        $attendanceRecord->setPunchInNote($punchInNote);
        
        //current state is punched out and editing it
        if ($attendanceRecord->getState() === AttendanceRecord::STATE_PUNCHED_OUT) {
            $punchOutTimezoneOffset = $attendanceRecord->getPunchInTimeOffset();
            $punchOutDateTime = $this->extractPunchDateTime($punchOutDate.' '.$punchOutTime, $punchOutTimezoneOffset);
            $punchOutUTCDateTime = (clone $punchOutDateTime)->setTimezone(
                new DateTimeZone(DateTimeHelperService::TIMEZONE_UTC)
            );
            //TODO::check for overlap
            //if there are no overlaps
            $attendanceRecord->setPunchOutUserTime($punchOutDateTime);
            $attendanceRecord->setPunchOutUtcTime($punchOutUTCDateTime);
            $attendanceRecord->setPunchOutNote($punchOutNote);
        }
        $attendanceRecord = $this->getAttendanceService()->getAttendanceDao()->savePunchRecord($attendanceRecord);
        return new EndpointResourceResult(AttendanceRecordModel::class, $attendanceRecord);
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
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_PUNCH_IN_NOTE
            ),
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_PUNCH_OUT_DATE
            ),
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_PUNCH_OUT_TIME
            ),
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_PUNCH_OUT_NOTE
            )
        ];
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
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::ENTITY_ID_EXISTS, [AttendanceRecord::class])
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
                    self::PARAMETER_PUNCH_IN_NOTE,
                    new Rule(Rules::STRING_TYPE)
                ),
                true
            ),
            new ParamRule(
                self::PARAMETER_PUNCH_OUT_DATE,
                new Rule(Rules::API_DATE)
            ),
            new ParamRule(
                self::PARAMETER_PUNCH_OUT_TIME,
                new Rule(Rules::TIME, ['H:i'])
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_PUNCH_OUT_NOTE,
                    new Rule(Rules::STRING_TYPE)
                )
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
