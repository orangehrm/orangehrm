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

namespace OrangeHRM\Attendance\Service;

use OrangeHRM\Attendance\Dao\AttendanceDao;
use OrangeHRM\Core\Service\AccessFlowStateMachineService;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\AttendanceRecord;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\WorkflowStateMachine;

class AttendanceService
{
    use AuthUserTrait;
    use UserRoleManagerTrait;

    public const ESS_USER = "ESS USER";
    public const SUPERVISOR = "SUPERVISOR";

    private ?bool $canUserChangeCurrentTime = null;

    /**
     * @var AttendanceDao|null
     */
    private ?AttendanceDao $attendanceDao = null;

    /**
     * @var AccessFlowStateMachineService|null
     */
    private ?AccessFlowStateMachineService $accessFlowStateMachineService = null;

    /**
     * @return AccessFlowStateMachineService
     */
    protected function getAccessFlowStateMachineService(): AccessFlowStateMachineService
    {
        if (is_null($this->accessFlowStateMachineService)) {
            $this->accessFlowStateMachineService = new AccessFlowStateMachineService();
        }
        return $this->accessFlowStateMachineService;
    }

    /**
     * Get Attendance Data Access Object
     * @return AttendanceDao
     */
    public function getAttendanceDao(): AttendanceDao
    {
        if (is_null($this->attendanceDao)) {
            $this->attendanceDao = new AttendanceDao();
        }
        return $this->attendanceDao;
    }

    /**
     * Set Attendance Data Access Object
     * @param AttendanceDao $AttendanceDao
     * @return void
     */
    public function setAttendanceDao(AttendanceDao $attendanceDao)
    {
        $this->attendanceDao = $attendanceDao;
    }

    /**
     * @return bool
     */
    public function canUserChangeCurrentTime(): bool
    {
        if (is_null($this->canUserChangeCurrentTime)) {
            $this->canUserChangeCurrentTime = $this->canUserChangeCurrentTimeConfiguration();
        }
        return $this->canUserChangeCurrentTime;
    }

    /**
     * save  Attendance Record
     * @param AttendanceRecord $attendanceRecord
     * @return AttendanceRecord
     */
    public function savePunchRecord(AttendanceRecord $attendanceRecord)
    {
        return $this->attendanceDao->savePunchRecord($attendanceRecord);
    }

    /**
     * check For Punch Out OverLapping Records
     * @param $punchInTime, $punchOutTime, $employeeId
     * @return string 1,0
     */
    public function checkForPunchOutOverLappingRecords($punchInTime, $punchOutTime, $employeeId, $recordId)
    {
        return $this->getAttendanceDao()->checkForPunchOutOverLappingRecords($punchInTime, $punchOutTime, $employeeId, $recordId);
    }

    /**
     * check For Punch In OverLapping Records
     * @param $punchInTime, $employeeId
     * @return string 1,0
     */
    public function checkForPunchInOverLappingRecords($punchInTime, $employeeId)
    {
        return $this->getAttendanceDao()->checkForPunchInOverLappingRecords($punchInTime, $employeeId);
    }

    /**
     * get Attendance record
     * @param $employeeId, $date
     * @return array of records
     */
    public function getAttendanceRecord($employeeId, $date)
    {
        return $this->getAttendanceDao()->getAttendanceRecord($employeeId, $date);
    }

    /**
     * delete Attendance record
     * @param $attendanceRecordId
     * @return boolean
     */
    public function deleteAttendanceRecords($attendanceRecordId)
    {
        return $this->getAttendanceDao()->deleteAttendanceRecords($attendanceRecordId);
    }

    /**
     * Get Attendance Record By Id
     * @param $attendanceRecordId
     * @return Attendance Record
     */
    public function getAttendanceRecordById($attendanceRecordId)
    {
        return $this->getAttendanceDao()->getAttendanceRecordById($attendanceRecordId);
    }

    /**
     * Get Time Zone
     * @param $value
     * @return Timezone offset
     */
    public function getTimezone($value)
    {
        $timezoneArray = $this->getTimezoneArray();
        return $timezoneArray[$value];
    }

    /**
     * Get Timezone Array
     * @param
     * @return time zone values array
     */
    public function getTimezoneArray()
    {
        $this->timezoneArray[0] = 'GMT';
        $this->timezoneArray[1] = '1.0';
        $this->timezoneArray[2] = '2.0';
        $this->timezoneArray[3] = '3.0';
        $this->timezoneArray[4] = '4.0';
        $this->timezoneArray[5] = '5.0';
        $this->timezoneArray[6] = '5.5';
        $this->timezoneArray[7] = '6.0';
        $this->timezoneArray[8] = '7.0';
        $this->timezoneArray[9] = '8.0';
        $this->timezoneArray[10] = '9.0';
        $this->timezoneArray[11] = '9.5';
        $this->timezoneArray[12] = '+10.00';
        $this->timezoneArray[13] = '+11.00';
        $this->timezoneArray[14] = '+12.00';
        $this->timezoneArray[15] = '-11.00';
        $this->timezoneArray[16] = '-10.00';
        $this->timezoneArray[17] = '-9.00';
        $this->timezoneArray[18] = '-8.00';
        $this->timezoneArray[19] = '-7.00';
        $this->timezoneArray[20] = '-6.00';
        $this->timezoneArray[21] = '-5.00';
        $this->timezoneArray[22] = '-4.00';
        $this->timezoneArray[23] = '-3.50';
        $this->timezoneArray[24] = '-3.00';
        $this->timezoneArray[25] = '-2.00';
        $this->timezoneArray[26] = '-1.00';

        return $this->timezoneArray;
    }

    /**
     * Get etLocal Timezone
     * @param $clientTimeZoneOffset
     * @return time zone string
     */
    public function getLocalTimezone($clientTimeZoneOffset)
    {
        $offset = $clientTimeZoneOffset;
        $zonelist =
                [
                    'Kwajalein' => -12.00,
                    'Pacific/Midway' => -11.00,
                    'Pacific/Honolulu' => -10.00,
                    'America/Anchorage' => -9.00,
                    'America/Los_Angeles' => -8.00,
                    'America/Denver' => -7.00,
                    'America/Tegucigalpa' => -6.00,
                    'America/New_York' => -5.00,
                    'America/Caracas' => -4.50,
                    'America/Halifax' => -4.00,
                    'America/St_Johns' => -3.50,
                    'America/Argentina/Buenos_Aires' => -3.00,
                    'America/Sao_Paulo' => -3.00,
                    'Atlantic/South_Georgia' => -2.00,
                    'Atlantic/Azores' => -1.00,
                    'Europe/Dublin' => 0,
                    'Europe/Belgrade' => 1.00,
                    'Europe/Minsk' => 2.00,
                    'Asia/Kuwait' => 3.00,
                    'Asia/Tehran' => 3.50,
                    'Asia/Muscat' => 4.00,
                    'Asia/Yekaterinburg' => 5.00,
                    'Asia/Kolkata' => 5.50,
                    'Asia/Katmandu' => 5.45,
                    'Asia/Dhaka' => 6.00,
                    'Asia/Rangoon' => 6.50,
                    'Asia/Krasnoyarsk' => 7.00,
                    'Asia/Brunei' => 8.00,
                    'Asia/Seoul' => 9.00,
                    'Australia/Darwin' => 9.50,
                    'Australia/Canberra' => 10.00,
                    'Asia/Magadan' => 11.00,
                    'Pacific/Fiji' => 12.00,
                    'Pacific/Tongatapu' => 13.00
        ];
        $index = array_keys($zonelist, $offset);
        if (sizeof($index) != 1) {
            return false;
        }
        return $index[0];
    }


    /**
    * check For Punch In OverLapping Records when Editing
    * @param $punchInTime, $employeeId
    * @return string 1,0
    */
    public function checkForPunchInOverLappingRecordsWhenEditing($punchInTime, $employeeId, $recordId, $punchOut)
    {
        return $this->getAttendanceDao()->checkForPunchInOverLappingRecordsWhenEditing($punchInTime, $employeeId, $recordId, $punchOut);
    }


    /**
    * check For Punch out OverLapping Records when Editing
    * @param $punchInTime, $employeeId
    * @return string 1,0
    */
    public function checkForPunchOutOverLappingRecordsWhenEditing($punchIn, $punchOut, $employeeId, $recordId)
    {
        return $this->getAttendanceDao()->checkForPunchInOutOverLappingRecordsWhenEditing($punchIn, $punchOut, $employeeId, $recordId);
    }

    /**
    * check For Punch out/in OverLapping Records when Editing
    * @param $punchInTime, $employeeId
    * @return string 1,0
    */
    public function checkForPunchInOutOverLappingRecordsWhenEditing($punchIn, $punchOut, $employeeId, $recordId)
    {
        return $this->getAttendanceDao()->checkForPunchInOutOverLappingRecordsWhenEditing($punchIn, $punchOut, $employeeId, $recordId);
    }

    /**
     *
     * @param int $employeeId
     * @param string $employeementStatus
     * @param int $subDivision
     * @param date $dateFrom
     * @param date $dateTo
     * @return array
     */
    public function searchAttendanceRecords($employeeId = null, $employeementStatus = null, $subDivision = null, $dateFrom = null, $dateTo = null)
    {
        return $this->getAttendanceDao()->searchAttendanceRecords($employeeId, $employeementStatus, $subDivision, $dateFrom, $dateTo);
    }

    /**
     * @param int $employeeId
     * @param string $state
     * @return array|bool|Doctrine_Record|float|int|mixed|string|null
     * @throws DaoException
     */
    public function getLatestPunchInRecord(int $employeeId, string $state)
    {
        return $this->getAttendanceDao()->getLatestPunchInRecord($employeeId, $state);
    }

    /**
     * @return bool
     */
    public function getPunchTimeUserConfiguration()
    {
        return $this->getSavedConfiguration(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_INITIAL,
            configureAction::ESS_USER,
            WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME,
            AttendanceRecord::STATE_INITIAL
        );
    }

    public function getAttendanceRecordsBetweenTwoDays(string $fromDate, string $toDate, int $employeeId, string $state)
    {
        return $this->getAttendanceDao()->getAttendanceRecordsBetweenTwoDays($fromDate, $toDate, $employeeId, $state);
    }

    /**
     * @param $empNumbers
     * @param null $dateFrom
     * @param null $dateTo
     * @return array|Doctrine_Collection
     * @throws DaoException
     */
    public function getAttendanceRecordsByEmpNumbers($empNumbers, $dateFrom = null, $dateTo = null)
    {
        return $this->getAttendanceDao()->getAttendanceRecordsByEmpNumbers($empNumbers, $dateFrom, $dateTo);
    }

    /**
     * @return bool
     */
    public function canUserChangeCurrentTimeConfiguration(): bool
    {
        return $this->getAttendanceDao()->hasSavedConfiguration(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_INITIAL,
            self::ESS_USER,
            WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME,
            AttendanceRecord::STATE_INITIAL
        );
    }

    /**
     * @return bool
     */
    public function canUserModifyAttendanceConfiguration(): bool
    {
        return $this->getAttendanceDao()->hasSavedConfiguration(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_PUNCHED_IN,
            self::ESS_USER,
            WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME,
            AttendanceRecord::STATE_PUNCHED_IN
        );
    }

    /**
     * @return bool
     */
    public function canSupervisorModifyAttendanceConfiguration(): bool
    {
        return $this->getAttendanceDao()->hasSavedConfiguration(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_PUNCHED_IN,
            self::SUPERVISOR,
            WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME,
            AttendanceRecord::STATE_PUNCHED_IN
        );
    }

    public function enableUserCanChangeCurrentTimeConfiguration(): void
    {
        $isPunchInEditable = $this->getAttendanceDao()->hasSavedConfiguration(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_INITIAL,
            self::ESS_USER,
            WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME,
            AttendanceRecord::STATE_INITIAL
        );

        if (!$isPunchInEditable) {
            $this->saveConfiguration(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_INITIAL,
                self::ESS_USER,
                WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME,
                AttendanceRecord::STATE_INITIAL
            );
        }
        $isPunchOutEditable = $this->getAttendanceDao()->hasSavedConfiguration(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_PUNCHED_IN,
            self::ESS_USER,
            WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME,
            AttendanceRecord::STATE_PUNCHED_IN
        );

        if (!$isPunchOutEditable) {
            $this->saveConfiguration(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_IN,
                self::ESS_USER,
                WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME,
                AttendanceRecord::STATE_PUNCHED_IN
            );
        }
    }

    public function disableUserCanChangeCurrentTimeConfiguration(): void
    {
        $this->getAccessFlowStateMachineService()->deleteWorkflowStateMachineRecord(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_INITIAL,
            self::ESS_USER,
            WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME,
            AttendanceRecord::STATE_INITIAL
        );
        $this->getAccessFlowStateMachineService()->deleteWorkflowStateMachineRecord(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_PUNCHED_IN,
            self::ESS_USER,
            WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME,
            AttendanceRecord::STATE_PUNCHED_IN
        );
    }

    public function enableUserCanModifyAttendanceConfiguration(): void
    {
        $isPunchInRecordEditable = $this->getAttendanceDao()->hasSavedConfiguration(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_PUNCHED_IN,
            self::ESS_USER,
            WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME,
            AttendanceRecord::STATE_PUNCHED_IN
        );

        if (!$isPunchInRecordEditable) {
            $this->saveConfiguration(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_IN,
                self::ESS_USER,
                WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME,
                AttendanceRecord::STATE_PUNCHED_IN
            );
        }
        $isPunchOutRecordEditable = $this->getAttendanceDao()->hasSavedConfiguration(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_PUNCHED_OUT,
            self::ESS_USER,
            WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_OUT_TIME,
            AttendanceRecord::STATE_PUNCHED_OUT
        );

        if (!$isPunchOutRecordEditable) {
            $this->saveConfiguration(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_OUT,
                self::ESS_USER,
                WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_OUT_TIME,
                AttendanceRecord::STATE_PUNCHED_OUT
            );
        }

        $isPunchInTimeEditableWhenTheStateIsPunchedIn = $this->getAttendanceDao()->hasSavedConfiguration(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_PUNCHED_OUT,
            self::ESS_USER,
            WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME,
            AttendanceRecord::STATE_PUNCHED_OUT
        );

        if (!$isPunchInTimeEditableWhenTheStateIsPunchedIn) {
            $this->saveConfiguration(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_OUT,
                self::ESS_USER,
                WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME,
                AttendanceRecord::STATE_PUNCHED_OUT
            );
        }

        $isPunchInRecordDeletable = $this->getAttendanceDao()->hasSavedConfiguration(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_PUNCHED_IN,
            self::ESS_USER,
            WorkflowStateMachine::ATTENDANCE_ACTION_DELETE,
            AttendanceRecord::STATE_NA
        );

        if (!$isPunchInRecordDeletable) {
            $this->saveConfiguration(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_IN,
                self::ESS_USER,
                WorkflowStateMachine::ATTENDANCE_ACTION_DELETE,
                AttendanceRecord::STATE_NA
            );
        }

        $isPunchOutRecordDeletable = $this->getAttendanceDao()->hasSavedConfiguration(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_PUNCHED_OUT,
            self::ESS_USER,
            WorkflowStateMachine::ATTENDANCE_ACTION_DELETE,
            AttendanceRecord::STATE_NA
        );

        if (!$isPunchOutRecordDeletable) {
            $this->saveConfiguration(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_OUT,
                self::ESS_USER,
                WorkflowStateMachine::ATTENDANCE_ACTION_DELETE,
                AttendanceRecord::STATE_NA
            );
        }
    }

    public function disableUserCanModifyAttendanceConfiguration(): void
    {
        $this->getAccessFlowStateMachineService()->deleteWorkflowStateMachineRecord(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_PUNCHED_IN,
            self::ESS_USER,
            WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME,
            AttendanceRecord::STATE_PUNCHED_IN
        );
        $this->getAccessFlowStateMachineService()->deleteWorkflowStateMachineRecord(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_PUNCHED_OUT,
            self::ESS_USER,
            WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_OUT_TIME,
            AttendanceRecord::STATE_PUNCHED_OUT
        );
        $this->getAccessFlowStateMachineService()->deleteWorkflowStateMachineRecord(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_PUNCHED_OUT,
            self::ESS_USER,
            WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME,
            AttendanceRecord::STATE_PUNCHED_OUT
        );
        $this->getAccessFlowStateMachineService()->deleteWorkflowStateMachineRecord(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_PUNCHED_IN,
            self::ESS_USER,
            WorkflowStateMachine::ATTENDANCE_ACTION_DELETE,
            AttendanceRecord::STATE_NA
        );
        $this->getAccessFlowStateMachineService()->deleteWorkflowStateMachineRecord(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_PUNCHED_OUT,
            self::ESS_USER,
            WorkflowStateMachine::ATTENDANCE_ACTION_DELETE,
            AttendanceRecord::STATE_NA
        );
    }

    public function enableSupervisorCanModifyAttendanceConfiguration(): void
    {
        $isPunchInEditable = $this->getAttendanceDao()->hasSavedConfiguration(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_PUNCHED_IN,
            self::SUPERVISOR,
            WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME,
            AttendanceRecord::STATE_PUNCHED_IN
        );
        if (!$isPunchInEditable) {
            $this->saveConfiguration(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_IN,
                self::SUPERVISOR,
                WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME,
                AttendanceRecord::STATE_PUNCHED_IN
            );
        }

        $isPunchInEditableInStatePunchedOut = $this->getAttendanceDao()->hasSavedConfiguration(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_PUNCHED_OUT,
            self::SUPERVISOR,
            WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME,
            AttendanceRecord::STATE_PUNCHED_OUT
        );
        if (!$isPunchInEditableInStatePunchedOut) {
            $this->saveConfiguration(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_OUT,
                self::SUPERVISOR,
                WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME,
                AttendanceRecord::STATE_PUNCHED_OUT
            );
        }

        $isPunchOutEditable = $this->getAttendanceDao()->hasSavedConfiguration(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_PUNCHED_OUT,
            self::SUPERVISOR,
            WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_OUT_TIME,
            AttendanceRecord::STATE_PUNCHED_OUT
        );

        if (!$isPunchOutEditable) {
            $this->saveConfiguration(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_OUT,
                self::SUPERVISOR,
                WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_OUT_TIME,
                AttendanceRecord::STATE_PUNCHED_OUT
            );
        }

        $isPunchInDeletable = $this->getAttendanceDao()->hasSavedConfiguration(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_PUNCHED_IN,
            self::SUPERVISOR,
            WorkflowStateMachine::ATTENDANCE_ACTION_DELETE,
            AttendanceRecord::STATE_NA
        );

        if (!$isPunchInDeletable) {
            $this->saveConfiguration(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_IN,
                self::SUPERVISOR,
                WorkflowStateMachine::ATTENDANCE_ACTION_DELETE,
                AttendanceRecord::STATE_NA
            );
        }

        $isPunchOutDeletable = $this->getAttendanceDao()->hasSavedConfiguration(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_PUNCHED_OUT,
            self::SUPERVISOR,
            WorkflowStateMachine::ATTENDANCE_ACTION_DELETE,
            AttendanceRecord::STATE_NA
        );

        if (!$isPunchOutDeletable) {
            $this->saveConfiguration(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_OUT,
                self::SUPERVISOR,
                WorkflowStateMachine::ATTENDANCE_ACTION_DELETE,
                AttendanceRecord::STATE_NA
            );
        }

        $isProxyPunchIn = $this->getAttendanceDao()->hasSavedConfiguration(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_INITIAL,
            self::SUPERVISOR,
            WorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_IN,
            AttendanceRecord::STATE_PUNCHED_IN
        );

        if (!$isProxyPunchIn) {
            $this->saveConfiguration(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_INITIAL,
                self::SUPERVISOR,
                WorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_IN,
                AttendanceRecord::STATE_PUNCHED_IN
            );
        }

        $isProxyPunchOut = $this->getAttendanceDao()->hasSavedConfiguration(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_PUNCHED_IN,
            self::SUPERVISOR,
            WorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_OUT,
            AttendanceRecord::STATE_PUNCHED_OUT
        );

        if (!$isProxyPunchOut) {
            $this->saveConfiguration(
                WorkflowStateMachine::FLOW_ATTENDANCE,
                AttendanceRecord::STATE_PUNCHED_IN,
                self::SUPERVISOR,
                WorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_OUT,
                AttendanceRecord::STATE_PUNCHED_OUT
            );
        }
    }

    public function disableSupervisorCanModifyAttendanceConfiguration(): void
    {
        $this->getAccessFlowStateMachineService()->deleteWorkflowStateMachineRecord(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_PUNCHED_IN,
            self::SUPERVISOR,
            WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME,
            AttendanceRecord::STATE_PUNCHED_IN
        );
        $this->getAccessFlowStateMachineService()->deleteWorkflowStateMachineRecord(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_PUNCHED_OUT,
            self::SUPERVISOR,
            WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_OUT_TIME,
            AttendanceRecord::STATE_PUNCHED_OUT
        );
        $this->getAccessFlowStateMachineService()->deleteWorkflowStateMachineRecord(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_PUNCHED_OUT,
            self::SUPERVISOR,
            WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME,
            AttendanceRecord::STATE_PUNCHED_OUT
        );
        $this->getAccessFlowStateMachineService()->deleteWorkflowStateMachineRecord(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_PUNCHED_IN,
            self::SUPERVISOR,
            WorkflowStateMachine::ATTENDANCE_ACTION_DELETE,
            AttendanceRecord::STATE_NA
        );
        $this->getAccessFlowStateMachineService()->deleteWorkflowStateMachineRecord(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_PUNCHED_OUT,
            self::SUPERVISOR,
            WorkflowStateMachine::ATTENDANCE_ACTION_DELETE,
            AttendanceRecord::STATE_NA
        );

        $this->getAccessFlowStateMachineService()->deleteWorkflowStateMachineRecord(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_INITIAL,
            self::SUPERVISOR,
            WorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_IN,
            AttendanceRecord::STATE_PUNCHED_IN
        );
        $this->getAccessFlowStateMachineService()->deleteWorkflowStateMachineRecord(
            WorkflowStateMachine::FLOW_ATTENDANCE,
            AttendanceRecord::STATE_PUNCHED_IN,
            self::SUPERVISOR,
            WorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_OUT,
            AttendanceRecord::STATE_PUNCHED_OUT
        );
    }

    /**
     * @param  string  $flow
     * @param  string  $state
     * @param  string  $role
     * @param  string  $action
     * @param  string  $resultingState
     * @return void
     */
    private function saveConfiguration(
        string $flow,
        string $state,
        string $role,
        string $action,
        string $resultingState
    ): void {
        $workflowStateMachineRecord = new WorkflowStateMachine();
        $workflowStateMachineRecord->setWorkflow(WorkflowStateMachine::FLOW_ATTENDANCE);
        $workflowStateMachineRecord->setState($state);
        $workflowStateMachineRecord->setRole($role);
        $workflowStateMachineRecord->setAction($action);
        $workflowStateMachineRecord->setResultingState($resultingState);
        $this->getAccessFlowStateMachineService()->saveWorkflowStateMachineRecord($workflowStateMachineRecord);
    }

    /**
     * @param AttendanceRecord $attendanceRecord
     * @return bool
     */
    public function isAuthUserAllowedToPerformTheEditActions(AttendanceRecord $attendanceRecord): bool
    {
        $attendanceRecordOwnedEmpNumber = $attendanceRecord->getEmployee()->getEmpNumber();
        $loggedInUserEmpNumber = $this->getAuthUser()->getEmpNumber();
        $rolesToInclude = [];
        if ($attendanceRecordOwnedEmpNumber === $loggedInUserEmpNumber) {
            $rolesToInclude = ['ESS'];
        }
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
        if (!in_array($workflowItem, array_keys($allowedWorkflowItems))) {
            return false;
        }
        return true;
    }
}
