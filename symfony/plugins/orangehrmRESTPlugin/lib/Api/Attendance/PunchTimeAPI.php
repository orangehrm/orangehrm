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

namespace Orangehrm\Rest\Api\Attendance;

use Orangehrm\Rest\Api\Admin\Entity\User;
use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Http\Response;

class PunchTimeAPI extends EndPoint{

    const PARAMETER_ID = 'id';
    const PARAMETER_TIME_ZONE = 'timezone';
    const PARAMETER_NOTE = 'note';
    const PARAMETER_DATE_TIME = 'datetime';

    protected $employeeService;
    protected $attendanceService;

    /**
     * @return \EmployeeService
     */
    public function getEmployeeService(){
        if(!$this->employeeService){
            $this->employeeService = new \EmployeeService();
        }
        return $this->employeeService;
    }

    /**
     * @param $employeeService
     * @return $this
     */
    public function setEmployeeService($employeeService){
        $this->employeeService = $employeeService;
        return $this;
    }

    /**
     * @return \AttendanceService
     */
    public function getAttendanceService() {
        if (is_null($this->attendanceService)) {
            $this->attendanceService = new \AttendanceService();
        }
        return $this->attendanceService;
    }

    /**
     * @param \AttendanceService $attendanceService
     */
    public function setAttendanceService(\AttendanceService $attendanceService) {
        $this->attendanceService = $attendanceService;
    }


    /**
     * @param $empNumber
     * @return \Employee
     */
    public function checkValidEmployee($empNumber){
        try {
            return $this->getEmployeeService()->getEmployee($empNumber);
        }catch (\Exception $e){
            new BadRequestException($e->getMessage());
        }

    }

    /**
     * @param $remote_tz
     * @param null $origin_tz
     * @return int
     */
    function getTimezoneOffset($remote_tz, $origin_tz = null) {
        if($origin_tz === null) {
            if(!is_string($origin_tz = date_default_timezone_get())) {
                return false;
            }
        }
        $origin_dtz = new \DateTimeZone($origin_tz);
        $remote_dtz = new \DateTimeZone($remote_tz);
        $origin_dt = new \DateTime("now", $origin_dtz);
        $remote_dt = new \DateTime("now", $remote_dtz);
        $offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
        return $offset;
    }

}