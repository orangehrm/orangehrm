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
class AttendanceService {

    private $attendanceDao;

    /**
     * Get Attendance Data Access Object
     * @return AttendanceDao
     */
    public function getAttendanceDao() {

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
    public function setAttendanceDao(AttendanceDao $attendanceDao) {

        $this->attendanceDao = $attendanceDao;
    }

    /**
     * save  Attendance Record
     * @param AttendanceRecord $attendanceRecord
     * @return AttendanceRecord
     */
    public function savePunchRecord(AttendanceRecord $attendanceRecord) {

        return $this->attendanceDao->savePunchRecord($attendanceRecord);
    }

    /**
     * get last punched Attendance Record
     * @param $employeeId,$actionaleStatesList
     * @return AttendanceRecord
     */
    public function getLastPunchRecord($employeeId, $actionableStatesList) {

        return $this->getAttendanceDao()->getLastPunchRecord($employeeId, $actionableStatesList);
    }

    /**
     * check For Punch Out OverLapping Records
     * @param $punchInTime, $punchOutTime, $employeeId
     * @return string 1,0
     */
    public function checkForPunchOutOverLappingRecords($punchInTime, $punchOutTime, $employeeId) {

        return $this->getAttendanceDao()->checkForPunchOutOverLappingRecords($punchInTime, $punchOutTime, $employeeId);
    }

    /**
     * check For Punch In OverLapping Records
     * @param $punchInTime, $employeeId
     * @return string 1,0
     */
    public function checkForPunchInOverLappingRecords($punchInTime, $employeeId) {
        return $this->getAttendanceDao()->checkForPunchInOverLappingRecords($punchInTime, $employeeId);
    }

    /**
     * get saved Attendance configuration
     * @param $workflow, $state, $role, $action, $resultingState
     * @return boolean 
     */
    public function getSavedConfiguration($workflow, $state, $role, $action, $resultingState) {

        return $this->getAttendanceDao()->getSavedConfiguration($workflow, $state, $role, $action, $resultingState);
    }

    /**
     * get Attendance record 
     * @param $employeeId, $date
     * @return array of records 
     */
    public function getAttendanceRecord($employeeId, $date) {

        return $this->getAttendanceDao()->getAttendanceRecord($employeeId, $date);
    }

    /**
     * delete Attendance record 
     * @param $attendanceRecordId
     * @return boolean
     */
    public function deleteAttendanceRecords($attendanceRecordId) {

        return $this->getAttendanceDao()->deleteAttendanceRecords($attendanceRecordId);
    }

    public function allowedToPerformAction($flow, $action, $state) {
        $userObj = sfContext::getInstance()->getUser()->getAttribute('user');
        $actionsArray = $userObj->getAllowedActions($flow, $state);
        
        if (in_array($action, $actionsArray)) {
            return true;
        } else {
            return false;
        }
    }
    
    public function getAttendanceRecordById($id){
        
        
        return $this->getAttendanceDao()->getAttendanceRecordById($id);
    }

    
     public function getTimezone($value) {
        $timezoneArray = $this->getTimezoneArray();
        return $timezoneArray[$value];
    }

    public function getTimezoneArray() {


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
        $this->timezoneArray[21] = '-7.00';
        $this->timezoneArray[22] = '-5.00';
        $this->timezoneArray[23] = '-4.00';
        $this->timezoneArray[24] = '-3.50';
        $this->timezoneArray[25] = '-3.00';
        $this->timezoneArray[26] = '-1.00';

        return $this->timezoneArray;
    }
}

?>
