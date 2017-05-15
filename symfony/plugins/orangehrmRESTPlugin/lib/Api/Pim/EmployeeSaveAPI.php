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

namespace Orangehrm\Rest\Api\Pim;


use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Http\Response;
use Orangehrm\Rest\Api\Exception\BadRequestException;

class EmployeeSaveAPI extends EndPoint
{

    /**
     * Employee constants
     */
    const PARAMETER_FIRST_NAME = "firstName";
    const PARAMETER_MIDDLE_NAME = "middleName";
    const PARAMETER_LAST_NAME = "lastName";
    const PARAMETER_EMPLOYEE_ID = "code";


    /**
     * @var EmployeeService
     */
    protected $employeeService = null;
    private $employeeEventService;

    /**
     * Saving Employee
     *
     * @return Response
     * @throws BadRequestException
     * @throws InvalidParamException
     */
    public function saveEmployee()
    {
        $returned = null;
        $filters = $this->filterParameters();
        $employee = null;
        if (!empty($filters[self::PARAMETER_EMPLOYEE_ID])) {

            $employee = $this->getEmployeeService()->getEmployeeByEmployeeId($filters[self::PARAMETER_EMPLOYEE_ID]);

        }

        if ($employee instanceof \Employee) {
            throw new BadRequestException('Failed To Save: Employee Code Exists');
        }

        try {
            $employee = $this->buildEmployee($filters);
        } catch (\Exception $e) {
            throw new InvalidParamException();
        }

        $returnedEmployee = $this->getEmployeeService()->saveEmployee($employee);

        if (!$returnedEmployee instanceof \Employee) {
            throw new BadRequestException('Employee Saving Failed');
        } else {

            $this->getEmployeeEventService()->saveEvent($returnedEmployee->getEmpNumber(),\PluginEmployeeEvent::EVENT_TYPE_EMPLOYEE,\PluginEmployeeEvent::EVENT_SAVE,'Saving Employee','API');
            return new Response(array('success' => 'Successfully Saved', 'id' => ltrim($returnedEmployee->getEmpNumber(), '0')));
        }
    }

    /**
     * build employee
     *
     * @return \Employee
     * @throws InvalidParamException
     */
    private function buildEmployee($filters)
    {
        $employee = new \Employee();

        if (!empty($filters[self::PARAMETER_FIRST_NAME])) {

            $employee->setFirstName($filters[self::PARAMETER_FIRST_NAME]);

        } else {
            throw new InvalidParamException();
        }

        $employee->setMiddleName($filters[self::PARAMETER_MIDDLE_NAME]);

        if (!empty($filters[self::PARAMETER_LAST_NAME])) {

            $employee->setLastName($filters[self::PARAMETER_LAST_NAME]);

        } else {
            throw new InvalidParamException();
        }

        $employee->setEmployeeId($filters[self::PARAMETER_EMPLOYEE_ID]);

        return $employee;
    }

    public function getValidationRules()
    {
        return array(
            self::PARAMETER_FIRST_NAME => array('StringType' => true, 'NotEmpty' => true, 'Length' => array(1, 30)),
            self::PARAMETER_MIDDLE_NAME => array('StringType' => true,  'Length' => array(1, 30)),
            self::PARAMETER_LAST_NAME => array('StringType' => true, 'NotEmpty' => true, 'Length' => array(1, 30)),
            self::PARAMETER_EMPLOYEE_ID => array('StringType' => true, 'Length' => array(1, 10))
        );
    }

    /**
     * Filter parameters
     *
     * @return array
     * @throws InvalidParamException
     */
    protected function filterParameters()
    {
        $filters[] = array();

        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_FIRST_NAME))) {
            $filters[self::PARAMETER_FIRST_NAME] = ($this->getRequestParams()->getPostParam(self::PARAMETER_FIRST_NAME));
        } else {
            throw new InvalidParamException('First Name Cannot Be Empty');
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_MIDDLE_NAME))) {
            $filters[self::PARAMETER_MIDDLE_NAME] = ($this->getRequestParams()->getPostParam(self::PARAMETER_MIDDLE_NAME));
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_LAST_NAME))) {
            $filters[self::PARAMETER_LAST_NAME] = ($this->getRequestParams()->getPostParam(self::PARAMETER_LAST_NAME));
        } else {
            throw new InvalidParamException('Last Name Cannot Be Empty');
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_EMPLOYEE_ID))) {
            $filters[self::PARAMETER_EMPLOYEE_ID] = ($this->getRequestParams()->getPostParam(self::PARAMETER_EMPLOYEE_ID));
        }
        return $filters;

    }

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

    /**
     * @param $employeeService
     */
    public function setEmployeeService($employeeService)
    {
        $this->employeeService = $employeeService;
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


}
