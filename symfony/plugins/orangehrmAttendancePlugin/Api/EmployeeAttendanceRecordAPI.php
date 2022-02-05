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
use Exception;
use OrangeHRM\Attendance\Api\Model\AttendanceRecordModel;
use OrangeHRM\Attendance\Exception\AttendanceServiceException;
use OrangeHRM\Attendance\Traits\Service\AttendanceServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\AttendanceRecord;

class EmployeeAttendanceRecordAPI extends Endpoint implements CrudEndpoint
{
    use AttendanceServiceTrait;
    use AuthUserTrait;
    use DateTimeHelperTrait;

    public const PARAMETER_DATE = 'date';
    public const PARAMETER_TIME = 'time';
    public const PARAMETER_TIMEZONE_OFFSET = 'timezoneOffset';
    public const PARAMETER_NOTE = 'note';

    private ?bool $canUserChangeCurrentTime = null;
    private ?bool $canUserModifyAttendance = null;
    private ?bool $canSupervisorModifyAttendance = null;

    /**
     * @return bool
     */
    public function canUserChangeCurrentTime(): bool
    {
        if (is_null($this->canUserChangeCurrentTime)) {
            $this->canUserChangeCurrentTime = $this->getAttendanceService()->canUserChangeCurrentTimeConfiguration();
        }
        return $this->canUserChangeCurrentTime;
    }

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
            $attendanceRecord = new AttendanceRecord();
            //get request params and convert the keys of the array into variables
            extract($this->getCommonRequestParams());
            $attendanceRecord->getDecorator()->setEmployeeByEmpNumber($empNumber);
            $punchInTimestamp = $this->extractTimestamp($date, $time, $timezoneOffset);
            $punchInUTCDateTime = $this->getDateTimeHelper()->getUTCDateTime($punchInTimestamp, $timezoneOffset);
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
                $this->getDateTimeHelper()->getDateTimeByTimestamp($punchInTimestamp),
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
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER,
            $this->getAuthUser()->getEmpNumber()
        );
        $date = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_DATE
        );
        $time = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_TIME
        );
        $timezoneOffset = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_TIMEZONE_OFFSET
        );
        $note = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_NOTE
        );
        return [
            'empNumber' => $empNumber,
            'date' => $date,
            'time' => $time,
            'timezoneOffset' => $timezoneOffset,
            'note' => $note
        ];
    }

    /**
     * @param  string  $date
     * @param  string  $time
     * @param  float  $timezoneOffset
     * @return int
     * @throws Exception
     */
    protected function extractTimestamp(string $date, string $time, float $timezoneOffset): int
    {
        $dateTime = $this->getDateTimeHelper()->getDateTimeByString($date.' '.$time);
        $timeStamp = $this->getDateTimeHelper()->getTimestampByDateTime($dateTime);
        //user can change current time config disabled and system generated date time is not valid
        if (!$this->canUserChangeCurrentTime() && !$this->isCurrantDateTimeValid($timezoneOffset, $timeStamp)) {
            throw AttendanceServiceException::invalidDateTime();
        }
        return $timeStamp;
    }

    /**
     * If the configuration disabled for users to edit the date time, we should check the user provided timestamp with the
     * exact timestamp in the user's timezone. Those two should be same if the user provides true data. The margin of error
     * can be +/- 60 seconds
     * @param  float  $timezoneOffset
     * @param  int  $userProvidedTimestamp
     * @return bool
     */
    protected function isCurrantDateTimeValid(float $timezoneOffset, int $userProvidedTimestamp): bool
    {
        $serverDateTime = $this->getDateTimeHelper()->getNow();
        $timestampDiff = $this->getDateTimeHelper()->getTimestampDifference($serverDateTime, $timezoneOffset);
        $userActualTimestamp = $this->getDateTimeHelper()->getTimestampByDateTime($serverDateTime) + $timestampDiff;
        return (($userActualTimestamp - $userProvidedTimestamp) < 60 && ($userActualTimestamp - $userProvidedTimestamp) > -60);
    }

    /**
     * @param  AttendanceRecord  $attendanceRecord
     * @param  string  $state
     * @param  DateTime  $punchInUtcTime
     * @param  DateTime  $punchInUserTime
     * @param  float  $punchInTimezoneOffset
     * @param  string  $punchInNote
     */
    protected function setPunchInAttendanceRecord(
        AttendanceRecord $attendanceRecord,
        string $state,
        DateTime $punchInUtcTime,
        DateTime $punchInUserTime,
        float $punchInTimezoneOffset,
        string $punchInNote
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

    protected function getCommonValidationRules(): array
    {
        return [
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    CommonParams::PARAMETER_EMP_NUMBER,
                    new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
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
            extract($this->getCommonRequestParams());
            $lastPunchInRecord = $this->getAttendanceService()
                ->getAttendanceDao()
                ->getLastPunchRecordByEmployeeNumberAndActionableList($empNumber, [AttendanceRecord::STATE_PUNCHED_IN]);
            if (is_null($lastPunchInRecord)) {
                throw AttendanceServiceException::punchOutAlreadyExist();
            }
            $punchOutTimestamp = $this->extractTimestamp($date, $time, $timezoneOffset);
            $punchOutUTCDateTime = $this->getDateTimeHelper()->getUTCDateTime($punchOutTimestamp, $timezoneOffset);
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
                $this->getDateTimeHelper()->getDateTimeByTimestamp($punchOutTimestamp),
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
     * @param  AttendanceRecord  $attendanceRecord
     * @param  string  $state
     * @param  DateTime  $punchOutUtcTime
     * @param  DateTime  $punchOutUserTime
     * @param  float  $punchOutTimezoneOffset
     * @param  string  $punchOutNote
     */
    protected function setPunchOutAttendanceRecord(
        AttendanceRecord $attendanceRecord,
        string $state,
        DateTime $punchOutUtcTime,
        DateTime $punchOutUserTime,
        float $punchOutTimezoneOffset,
        string $punchOutNote
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
