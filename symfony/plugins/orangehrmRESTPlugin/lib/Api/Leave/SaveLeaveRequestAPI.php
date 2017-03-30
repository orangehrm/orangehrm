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

namespace Orangehrm\Rest\Api\Leave;

use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Api\Leave\Service\APILeaveAssignmentService;

use Orangehrm\Rest\Http\Response;

class SaveLeaveRequestAPI extends EndPoint
{
    /**
     * @var \EmployeeService
     */
    private $employeeService;

    protected $leaveAssignmentService;


    private $subunit;

    /**
     * Constants
     */
    const PARAMETER_EMP_ID = "empId";
    const PARAMETER_LEAVE_TYPE = "type";
    const PARAMETER_FROM_DATE = "fromDate";
    const PARAMETER_TO_DATE = "toDate";
    const PARAMETER_DURATION = "duration";
    const PARAMETER_COMMENT = 'comment';
    const PARAMETER_MULTIDAY_LEAVE = 'multiDay';
    const PARAMETER_MULTIDAY_PARTIAL_OPTION = 'partialOption';

    const SINGLE_DAY_TYPE = 'singleType';
    const SINGLE_DAY_AMPM = 'singleAMPM';
    const SINGLE_DAY_FROM = 'singleFromTime';
    const SINGLE_DAY_TO = 'singleToTime';

    const START_DAY_TYPE = 'startDayType';
    const START_DAY_AMPM = 'startDayAMPM';
    const START_DAY_FROM = 'startDayFromTime';
    const START_DAY_TO = 'startDayToTime';

    const END_DAY_TYPE = 'endDayType';
    const END_DAY_AMPM = 'endDayAMPM';
    const END_DAY_FROM = 'endDayFromTime';
    const END_DAY_TO = 'endDayToTime';

    const DURATION = 'duration';
    const FIRST_DAY_DURATION = 'firstDayDuration';
    const SECOND_DAY_DURATION = 'secondDayDuration';

    const HALF_DAY = 'half_day';
    const FULL_DAY = 'full_day';
    const SPECIFY_TIME = 'specify_time';

    const AM = 'AM';
    const PM = 'PM';


    /**
     * @return \EmployeeService
     */
    public function getEmployeeService()
    {
        if (is_null($this->employeeService)) {
            $this->employeeService = new \EmployeeService();
        }
        return $this->employeeService;
    }

    /**
     * Sets EmployeeService
     * @param \EmployeeService $service
     */
    public function setEmployeeService(\EmployeeService $service)
    {
        $this->employeeService = $service;
    }


    /**
     * @return mixed
     */
    public function getSubunit()
    {
        return $this->subunit;
    }

    /**
     * @param mixed $subunit
     */
    public function setSubunit($subunit)
    {
        $this->subunit = $subunit;
    }

    /**
     * Get leave assignment service instance
     *
     * @return APILeaveAssignmentService
     */
    public function getLeaveAssignmentService()
    {
        if (!($this->leaveAssignmentService instanceof APILeaveAssignmentService)) {
            $this->leaveAssignmentService = new APILeaveAssignmentService();
        }
        return $this->leaveAssignmentService;
    }

    /**
     * Set leave assignment service instance
     * @param APILeaveAssignmentService $service
     */
    public function setLeaveAssignmentService(APILeaveAssignmentService $service)
    {
        $this->leaveAssignmentService = $service;
    }

    public function saveLeaveRequest()
    {

        $filters = $this->filterParameters();
        $leaveParameters = new \LeaveParameterObject($filters);

        $success = $this->getLeaveAssignmentService()->assignLeave($leaveParameters);

        if ($success) {
            return new Response(array('success' => 'Successfully Saved'));
        } else {
            throw new BadRequestException("Saving Failed");
        }
    }


    /**
     * Filters
     * partial days :: all ::
     * @return array
     */
    protected function filterParameters()
    {

        $filters['txtEmpID'] = ($this->getRequestParams()->getUrlParam(self::PARAMETER_EMP_ID));
        $filters['txtLeaveType'] = ($this->getRequestParams()->getPostParam(self::PARAMETER_LEAVE_TYPE));
        $filters['txtFromDate'] = ($this->getRequestParams()->getPostParam(self::PARAMETER_FROM_DATE));
        $filters['txtToDate'] = ($this->getRequestParams()->getPostParam(self::PARAMETER_TO_DATE));
        $filters[self::PARAMETER_DURATION] = ($this->getRequestParams()->getPostParam(self::PARAMETER_DURATION));
        $filters['partialDays'] = ($this->getRequestParams()->getPostParam(self::PARAMETER_MULTIDAY_PARTIAL_OPTION));
        $filters['txtComment'] = ($this->getRequestParams()->getPostParam(self::PARAMETER_COMMENT));
        $filters['txtEmpWorkShift'] = '8';

        $singleDayType = ($this->getRequestParams()->getPostParam(self::SINGLE_DAY_TYPE));
        $singleDayAmpm = ($this->getRequestParams()->getPostParam(self::SINGLE_DAY_AMPM));
        $singleDayFrom = ($this->getRequestParams()->getPostParam(self::SINGLE_DAY_FROM));
        $singleDayTo = ($this->getRequestParams()->getPostParam(self::SINGLE_DAY_TO));

        $firstDayType = ($this->getRequestParams()->getPostParam(self::START_DAY_TYPE));
        $firstDayAmpm = ($this->getRequestParams()->getPostParam(self::START_DAY_AMPM));
        $firstDayFrom = ($this->getRequestParams()->getPostParam(self::START_DAY_FROM));
        $firstDayTo = ($this->getRequestParams()->getPostParam(self::START_DAY_TO));

        $secondDayType = ($this->getRequestParams()->getPostParam(self::END_DAY_TYPE));
        $secondDayAmpm = ($this->getRequestParams()->getPostParam(self::END_DAY_AMPM));
        $secondDayFrom = ($this->getRequestParams()->getPostParam(self::END_DAY_FROM));
        $secondDayTo = ($this->getRequestParams()->getPostParam(self::END_DAY_TO));

        if($filters['txtToDate'] === $filters['txtFromDate'] ){

            $filters['duration'] = $this->createDuration('Single Day', $singleDayType, $singleDayAmpm, $singleDayFrom,
                $singleDayTo);
        } else {

            if ($filters['partialDays'] === 'all') {
                $filters['firstDayDuration'] = $this->createDuration('First Day', $firstDayType, $firstDayAmpm,
                    $firstDayFrom,
                    $firstDayTo);
                $filters['secondDayDuration'] = $this->createDuration('Second Day', $secondDayType, $secondDayAmpm,
                    $secondDayFrom,
                    $secondDayTo);
            }
            else if ($filters['partialDays'] === 'start'){
                $filters['firstDayDuration'] = $this->createDuration('First Day', $firstDayType, $firstDayAmpm, $firstDayFrom,
                    $firstDayTo);}

            else if ($filters['partialDays'] === 'end'){
                $filters['secondDayDuration'] = $this->createDuration('Second Day', $secondDayType, $secondDayAmpm,
                    $secondDayFrom,
                    $secondDayTo);
            }
        }




        return $filters;
    }


    /**
     * pass employee filter
     *
     * @param $pastEmp
     * @return bool
     */
    public function validatePassEmployee($pastEmp)
    {
        return $pastEmp === 'true';
    }

    public function getValidationRules()
    {
        return array(
            self::PARAMETER_TO_DATE => array('Date' => array('Y-m-d')),
            self::PARAMETER_FROM_DATE => array('Date' => array('Y-m-d')),
            self::PARAMETER_LEAVE_TYPE => array('IntVal' => true),
        );
    }

    /**
     * Create the duration object for each day type
     * EX Single day :: Start Day  :: End Day
     *
     * @param $durationName
     * @param $type
     * @param $ampm
     * @param $from
     * @param $to
     * @return null
     * @throws InvalidParamException
     */
    protected function createDuration($durationName, $type, $ampm, $from, $to)
    {
        $duration = null;
        $time = null;

        $duration['duration'] = $type;  // half_day ,full_day ,specify_time
        $duration['ampm'] = $ampm;     // AM /PM
        $time['from'] = $from;
        $time['to'] = $to;
        $duration['time'] = $time;

        if (self::HALF_DAY === $type) {

            if (!(($ampm === self::AM )|| ($ampm === self::PM))) {
                throw  new InvalidParamException("Please Add " . $durationName . " " . " AM or PM");
            }
        } else {
            if (self::SPECIFY_TIME === $type) {

                if (empty($from) && empty($to)) {
                    throw  new InvalidParamException("Please Add " . $durationName . " " . " From - To Values");

                }
            } else {

                if (self::FULL_DAY != $type) {

                    throw  new InvalidParamException("Invalid Type");
                }
            }
        }


        return $duration;

    }
}
