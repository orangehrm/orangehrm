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
use OrangeHRM\Entity\AttendanceRecord;

class EmployeeAttendanceRecordAPI extends Endpoint implements CrudEndpoint
{
    use AttendanceServiceTrait;
    use AuthUserTrait;

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

        $attendanceRecord = new AttendanceRecord();
        $attendanceRecord->getDecorator()->setEmployeeByEmpNumber($empNumber);
        $punchInDateTime = $this->extractDateTime($date, $time, $timezoneOffset);
        $overlappingPunchInRecord = $this->getAttendanceService()
            ->getAttendanceDao()
            ->checkForPunchInOverLappingRecords(new DateTime($punchInDateTime), $empNumber);
        if ($overlappingPunchInRecord) {
            throw new Exception('Overlapping punch in record detected!');
        }
        $this->setPunchInAttendanceRecord(
            $attendanceRecord,
            AttendanceRecord::STATE_PUNCHED_IN,
            date('Y-m-d H:i', $punchInDateTime - $timezoneOffset * 3600),
            date('Y-m-d H:i', $punchInDateTime),
            $timezoneOffset,
            $note
        );
        $attendanceRecord = $this->getAttendanceService()->getAttendanceDao()->savePunchRecord($attendanceRecord);
        return new EndpointResourceResult(AttendanceRecordModel::class, $attendanceRecord);
    }

    /**
     * @param  string  $date
     * @param  string  $time
     * @param  float  $timezoneOffset
     * @return int
     * @throws Exception
     */
    protected function extractDateTime(string $date, string $time, float $timezoneOffset): int
    {
        //configuration - user can change current time disabled
        if (!$this->canUserChangeCurrentTime()) {
            $dateTime = strtotime($date.' '.$time);
            if (!$this->isCurrantDateTimeValid($timezoneOffset, $dateTime)) {
                throw new Exception('Provided date and time invalid');
            }
        } //configuration - user can change current time enabled (Edit Mode)
        else {
            $dateTime = mktime(
                date('H', strtotime($time)),
                date('i', strtotime($time)),
                0,
                date('m', strtotime($date)),
                date('d', strtotime($date)),
                date('Y', strtotime($date))
            );
        }
        return $dateTime;
    }

    /**
     * @param  float  $timeZoneOffset
     * @param  int  $dateTime
     * @return bool
     */
    protected function isCurrantDateTimeValid(float $timeZoneOffset, int $dateTime): bool
    {
        $timeStampDiff = $timeZoneOffset * 3600 - date('Z');
        $currentDate = date('Y-m-d', time() + $timeStampDiff);
        $currentTime = date('H:i', time() + $timeStampDiff);
        $currentDateTime = strtotime($currentDate.' '.$currentTime);
        return (($currentDateTime - $dateTime) < 60 && ($currentDateTime - $dateTime) > -60);
    }

    /**
     * @param  AttendanceRecord  $attendanceRecord
     * @param  string  $state
     * @param  string  $punchInUtcTime
     * @param  string  $punchInUserTime
     * @param  float  $punchInTimezoneOffset
     * @param  string  $punchInNote
     * @throws Exception
     */
    protected function setPunchInAttendanceRecord(
        AttendanceRecord $attendanceRecord,
        string $state,
        string $punchInUtcTime,
        string $punchInUserTime,
        float $punchInTimezoneOffset,
        string $punchInNote
    ): void {
        $attendanceRecord->setState($state);
        $attendanceRecord->setPunchInUtcTime(new DateTime($punchInUtcTime));
        $attendanceRecord->setPunchInUserTime(new DateTime($punchInUserTime));
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
