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
use Orangehrm\Rest\Api\Pim\Entity\Employee;
use Orangehrm\Rest\Http\Response;
use Orangehrm\Rest\Api\Exception\BadRequestException;

class EmployeeDetailAPI extends EndPoint
{

    const PARAMETER_ID = "id";

    /*
     * relations
     */
    const EMPLOYEE_CONTACT_DETAIL = "/employee/:id/contact-detail";
    const EMPLOYEE_JOB_DETAIL = "/employee/:id/job-detail";
    const EMPLOYEE_DEPENDENT = "/employee/:id/dependent";

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
     * Get Employee details from Employee service
     *
     * @return Response
     * @throws RecordNotFoundException
     * @throws \HttpInvalidParamException
     */
    public function getEmployeeDetails()
    {

        $empId = -1;
        $employeeList [] = array();
        if (!empty($this->getRequestParams()->getUrlParam(self::PARAMETER_ID))) {
            $empId = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        }
        if (!is_numeric($empId)) {
            throw new InvalidParamException("Invalid Parameter");

        }
        $employee = $this->getEmployeeService()->getEmployee($empId);


        if (empty($employee)) {
            throw new RecordNotFoundException("Employee not found");
        }
        return new Response($this->buildEmployeeData($employee), array(
            'contact-detail' => self::EMPLOYEE_CONTACT_DETAIL,
            'job-detail' => self::EMPLOYEE_JOB_DETAIL,
            'dependent' => self::EMPLOYEE_DEPENDENT
        ));

    }

    /**
     * Creating the Employee serializable object
     *
     * @param $employee
     * @return array
     */
    private function buildEmployeeData(\Employee $employee)
    {

        $emp = new Employee($employee->getFirstName(), $employee->getMiddleName(), $employee->getLastName(),
            $employee->getEmployeeId());
        $emp->buildEmployee($employee);
        return $emp->toArray();

    }


}
