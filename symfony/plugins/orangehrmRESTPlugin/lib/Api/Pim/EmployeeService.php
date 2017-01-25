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

use Orangehrm\Rest\Api\Pim\Entity\Employee;

class EmployeeService
{
    protected $request;
    protected $employeeService;

    protected function getEmployeeService(){
        if($this->employeeService != null){
            return $this->employeeService;
        }else {
            return new \EmployeeService();
        }
    }

    /**
     * Search Employee Api call
     *
     * @param $request
     * @return array
     */
    public function getEmployeeList($request) {

        $responseArray = array();
        $searchQuery = new \SearchQuery();
        $searchParams = $searchQuery->getEmployeeSearchParams($request);

        $parameterHolder = new \EmployeeSearchParameterHolder();
        $filters = array('firstName' => $searchParams['empFirstName']);
        $parameterHolder->setFilters($filters);
        $parameterHolder->setLimit(NULL);
        $parameterHolder->setReturnType(\EmployeeSearchParameterHolder::RETURN_TYPE_OBJECT);
        $employees = $this->getEmployeeService()->searchEmployees($parameterHolder);

        foreach ($employees as $employee) {

            $emp = new Employee($employee->getFirstName(), $employee->getMiddleName(), $employee->getLastName(), 25);
            $responseArray[] = $emp->toArray();
        }

        return $responseArray;

    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param mixed $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

}
