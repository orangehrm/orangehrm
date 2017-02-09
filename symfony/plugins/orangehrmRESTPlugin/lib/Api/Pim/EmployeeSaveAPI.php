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

class EmployeeSaveAPI extends EndPoint
{

    /**
     * Employee constants
     */
    const PARAMETER__FIRST_NAME = "first_name";
    const PARAMETER__MIDDLE_NAME = "middle_name";
    const PARAMETER__LAST_NAME = "last_name";
    const PARAMETER__EMPLOYEE_ID = "id";


    /**
     * @var EmployeeService
     */
    protected $employeeService = null;

    /**
     * Save employee
     *
     * @return Response
     */
    public function saveEmployee()
    {
        $relationsArray = array();
        $returnedEmployee = null;

        $employee = $this->buildEmployee();
        $employeeList = $this->getEmployeeService()->saveEmployee($employee);

        if (empty($returnedEmployee) && empty($returnedEmployee->getEmployeeNumber)) {
            return new Response(array('success' => 'Employee saved Success'), $relationsArray);
        } else {
            return new Response(array('Failed' => 'Employee saved Failed'), $relationsArray);
        }


    }

    /**
     * build employee
     *
     * @return \Employee
     * @throws InvalidParamException
     */
    private function buildEmployee()
    {

        $employee = new \Employee();


        if (!empty($this->getRequestParams()->getQueryParam(self::PARAMETER__FIRST_NAME))) {
            $employee->setFirstName($this->getRequestParams()->getQueryParam(self::PARAMETER__FIRST_NAME));
        } else {
            throw new InvalidParamException();
        }
        if (!empty($this->getRequestParams()->getQueryParam(self::PARAMETER__MIDDLE_NAME))) {
            $employee->setMiddleName($this->getRequestParams()->getQueryParam(self::PARAMETER__MIDDLE_NAME));
        }
        if (!empty($this->getRequestParams()->getQueryParam(self::PARAMETER__LAST_NAME))) {
            $employee->setLastName($this->getRequestParams()->getQueryParam(self::PARAMETER__LAST_NAME));
        } else {
            throw new InvalidParamException();
        }
        if (!empty($this->getRequestParams()->getQueryParam(self::PARAMETER__EMPLOYEE_ID))) {
            $employee->setEmployeeId($this->getRequestParams()->getQueryParam(self::PARAMETER__EMPLOYEE_ID));
        }

        return $employee;
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
     * get relations
     *
     */
    private function getRelations()
    {
        //TODO
    }

}
