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

namespace Orangehrm\Rest\Api\Time;

use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Pim\Entity\Employee;
use Orangehrm\Rest\Api\Pim\Entity\EmployeeDependent;
use Orangehrm\Rest\Http\Response;

class EmployeeTimeSheetAPI extends EndPoint
{

    const PARAMETER_EMPLOYEE_ID = "employeeId";
    const PARAMETER_TIMESHEET_ID = "timesheetId";
    const PARAMETER_INITIAL_ROWS = "initialRaws";
    const PARAMETER_START_DATE = "startDate";
    const PARAMETER_ID = "id";

    private $employeeEventService;
    private $employeeService;

    /**
     * @return \EmployeeService|null
     */
    protected function getEmployeeService()
    {

        if ($this->employeeService != null) {
            return $this->employeeService;
        } else {
            return new \EmployeeService();
        }
    }

    public function setEmployeeService($employeeService)
    {
        $this->employeeService = $employeeService;
    }

    /**
     * @return TimesheetService
     */
    public function getTimesheetService() {

        if (is_null($this->timesheetService)) {

            $this->timesheetService = new \TimesheetService();
        }

        return $this->timesheetService;
    }


    /**
     * Get employee event service
     *
     * @return \EmployeeEventService
     */
    private function getEmployeeEventService() {

        if(is_null($this->employeeEventService)) {
            $this->employeeEventService = new \EmployeeEventService();
        }

        return $this->employeeEventService;
    }

    /**
     * @param mixed $employeeEventService
     */
    public function setEmployeeEventService($employeeEventService)
    {
        $this->employeeEventService = $employeeEventService;
    }


    /**
     * get employee timesheets
     *
     * @return Response
     * @throws InvalidParamException
     */
    public function getEmployeeTimesheets()
    {
        $responseArray = null;
        $empId = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);

        $timesheets = $this->getTimesheetService()->getTimesheetByEmployeeId($empId);

        foreach ($timesheets as $timesheet) {

//            $empDependant = new EmployeeDependent($dependent->getName(), $dependent->getRelationship(),
//                $dependent->getDateOfBirth(), $dependent->getSeqno());
            $responseArray[] = $timesheet->toArray();
        }
//        $responseArray[] = $empId;
        return new Response($responseArray, array());
    }

    /**
     * Save employee Timesheet
     *
     * @return Response
     * @throws BadRequestException
     * @throws InvalidParamException
     */
    public function saveEmployeeTimeSheets($initialRaws)
    {


        $filters = $this->filterParameters();
//        return new Response($filters);
//        var_dump("dddddd");die;
//        return new Response($this->getRequestParams(), array());
        $this->getTimesheetService()->createTimesheet($filters[self::PARAMETER_EMPLOYEE_ID], $filters[self::PARAMETER_START_DATE]);
        $timesheet = $this->getTimesheetService()->getTimesheetByStartDateAndEmployeeId($filters[self::PARAMETER_START_DATE], $filters[self::PARAMETER_EMPLOYEE_ID]);
        $endDate = $timesheet->getEndDate();
        $startDate = $timesheet->getStartDate();
        $currentWeekDates = $this->getDatesOfTheTimesheetPeriod($startDate, $endDate);
//        var_dump($this->getRequestParams());
        $initialRaws = json_decode($initialRaws, true);
        $result = $this->getTimesheetService()->saveTimesheetItems($initialRaws, $filters[self::PARAMETER_EMPLOYEE_ID], $timesheet->getTimesheetId(), $currentWeekDates, 0);

//        if ($result instanceof \EmpDependent) {
//
//            $this->getEmployeeEventService()->saveEvent($result->getEmpNumber(),\PluginEmployeeEvent::EVENT_TYPE_DEPENDENT,\PluginEmployeeEvent::EVENT_SAVE,'Saving Employee Dependent','API');
            return new Response(array('success' => 'Successfully Saved'));
//        } else {
//            throw new BadRequestException("Saving Failed");
//        }

    }


    public function getDatesOfTheTimesheetPeriod($startDate, $endDate) {

//        $clientTimeZoneOffset = sfContext::getInstance()->getUser()->getUserTimeZoneOffset();
//        date_default_timezone_set($this->getLocalTimezone($clientTimeZoneOffset));

        if ($startDate < $endDate) {
            $dates_range[] = $startDate;

            $startDate = strtotime($startDate);
            $endDate = strtotime($endDate);


            while (date('Y-m-d', $startDate) != date('Y-m-d', $endDate)) {
                $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate) + 1, date("Y", $startDate));
                $dates_range[] = date('Y-m-d', $startDate);
            }
        }
        return $dates_range;
    }

    /**
     * Update employee dependents
     *
     * @return Response
     * @throws BadRequestException
     * @throws InvalidParamException
     */
    public function updateEmployeeDependents()
    {
//        $filters = $this->filterParameters();
//        if(!is_numeric( $filters[self::PARAMETER_SEQ_NUMBER] )) {
//            throw new InvalidParamException("Sequence Number Is Wrong");
//        }
//        $dependent = $this->buildEmployeeDependents($filters);
//        try {
//            $result = $this->getEmployeeService()->updateEmployeeDependent($dependent);
//
//        } catch (\Exception $pimEx) {
//            throw new BadRequestException('Updating Failed');
//        }
//
//        if ($result instanceof \EmpDependent) {
//            $this->getEmployeeEventService()->saveEvent($result->getEmpNumber(),\PluginEmployeeEvent::EVENT_TYPE_DEPENDENT,\PluginEmployeeEvent::EVENT_UPDATE,'Updating Employee Dependent','API');
//            return new Response(array('success' => 'Successfully Updated'));
//        } else {
//            throw new BadRequestException("Updating Failed");
//        }

    }

    /**
     * Deleting employee dependents
     *
     * @return Response
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     */
    public function deleteEmployeeDependents()
    {
//        $filters = $this->filterParameters();
//        $empId = $filters[self::PARAMETER_ID];
//        $sequenceNumber = $filters[self::PARAMETER_SEQ_NUMBER];
//
//        if (!empty($sequenceNumber) && is_numeric($sequenceNumber)) {
//
//            $count = $this->getEmployeeService()->deleteEmployeeDependents($empId, array($sequenceNumber));
//
//            if ($count > 0) {
//                $this->getEmployeeEventService()->saveEvent($empId,\PluginEmployeeEvent::EVENT_TYPE_DEPENDENT,\PluginEmployeeEvent::EVENT_DELETE,'Deleting Employee Dependent','API');
//                return new Response(array('success' => 'Successfully Deleted'));
//            } else {
//                throw new RecordNotFoundException("Deleting Failed");
//            }
//
//        } else {
//            throw new InvalidParamException("Sequence Number Is Wrong");
//        }


    }

    /**
     * Filter Post parameters to validate
     *
     * @return array
     *
     */
    protected function filterParameters()
    {
        $filters[] = array();

        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_EMPLOYEE_ID))) {
            $filters[self::PARAMETER_EMPLOYEE_ID] = $this->getRequestParams()->getPostParam(self::PARAMETER_EMPLOYEE_ID);
        }

        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_TIMESHEET_ID))) {
            $filters[self::PARAMETER_TIMESHEET_ID] = $this->getRequestParams()->getPostParam(self::PARAMETER_TIMESHEET_ID);
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_INITIAL_ROWS))) {
            $filters[self::PARAMETER_INITIAL_ROWS] = $this->getRequestParams()->getPostParam(self::PARAMETER_INITIAL_ROWS);
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_START_DATE))) {
            $filters[self::PARAMETER_START_DATE] = $this->getRequestParams()->getPostParam(self::PARAMETER_START_DATE);
        }

        return $filters;

    }

    /**
     * Build employee dependent
     *
     * @param $filters
     * @return \EmpDependent
     */
    protected function buildEmployeeDependents($filters)
    {
//        $employeeDependent = new \EmpDependent();
//        $employeeDependent->setSeqno($filters[self::PARAMETER_SEQ_NUMBER]);
//        $employeeDependent->setEmpNumber($filters[self::PARAMETER_ID]);
//        $employeeDependent->name = $filters[self::PARAMETER_NAME];
//        $employeeDependent->relationship = $filters[self::PARAMETER_RELATIONSHIP];
//        $employeeDependent ->relationship_type = 'other';
//        $dob = date("Y-m-d", strtotime($filters[self::PARAMETER_DOB]));
//        $employeeDependent->date_of_birth = $dob;
//
//        return $employeeDependent;
    }


    public function getPostValidationRules()
    {
        return array();
//        return array(
//            self::PARAMETER_DOB => array('Date' => array('Y-m-d')),
//            self::PARAMETER_RELATIONSHIP => array('StringType' => true, 'NotEmpty' => true,'Length' => array(1,50)),
//            self::PARAMETER_NAME => array('Length' => array(0, 50)),
//        );
    }

    public function getPutValidationRules()
    {
//        return array(
//            self::PARAMETER_DOB => array('Date' => array('Y-m-d')),
//            self::PARAMETER_RELATIONSHIP => array('StringType' => true, 'NotEmpty' => true,'Length' => array(1,50)),
//            self::PARAMETER_NAME => array('Length' => array(0, 50), 'NotEmpty' => true),
//            self::PARAMETER_SEQ_NUMBER=> array('NotEmpty' => true,'Length' => array(1,1000))
//        );
    }

    public function getDelValidationRules()
    {
//        return array(
//          self::PARAMETER_SEQ_NUMBER=> array( 'NotEmpty' => true,'Length' => array(1,1000))
//        );
    }

}


