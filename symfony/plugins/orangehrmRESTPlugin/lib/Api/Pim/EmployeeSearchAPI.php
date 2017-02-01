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

class EmployeeSearchAPI extends EndPoint{

    /**
     * Employee constants
     */
    const PARAMETER_NAME       = "name";
    const PARAMETER_ID         = "id";
    const PARAMETER_JOB_TITLE  = "jobTitle";
    const PARAMETER_STATUS     = "status";
    const PARAMETER_UNIT       = "unit";
    const PARAMETER_SUPERVISOR = "supervisor";
    const PARAMETER_LIMIT      = 'limit';

    /**
     * @var EmployeeService
     */
    protected $employeeService = null;

    /**
     * @return \EmployeeService|null
     */
    protected function getEmployeeService() {

        if ($this->employeeService != null) {
            return $this->employeeService;
        } else {
            return new \EmployeeService();
        }
    }

    /**
     * @param $employeeService
     */
    public function setEmployeeService($employeeService){
        $this->employeeService = $employeeService;
    }

    /**
     * @return Response
     * @throws RecordNotFoundException
     */
    public function getEmployeeList() {
        $employeeList = array ();
        $parameterHolder = $this->buildSearchParamHolder();
        if(empty($parameterHolder)) {
            $employeeList = $this->getEmployeeService()->getEmployeeList();
        }else{
            $employeeList = $this->getEmployeeService()->searchEmployees($parameterHolder);
        }

        if(empty($employeeList)) {
            throw new RecordNotFoundException("Employee not found");
        }

        return new Response($this->buildEmployeeData($employeeList),array());

    }

    /**
     * @return \EmployeeSearchParameterHolder|null
     */
    private function buildSearchParamHolder( ) {

        $filters = array();
        if(!empty($this->getRequestParams()->getQueryParam(self::PARAMETER_NAME))){
            $filters['employee_name'] = $this->getRequestParams()->getQueryParam(self::PARAMETER_NAME);
        }

        if(!empty($this->getRequestParams()->getQueryParam(self::PARAMETER_ID))){
            $filters['id'] = $this->getRequestParams()->getQueryParam(self::PARAMETER_ID);
        }
        if(!empty($this->getRequestParams()->getQueryParam(self::PARAMETER_JOB_TITLE))){
            $filters['job_title'] = $this->getRequestParams()->getQueryParam(self::PARAMETER_JOB_TITLE);
        }
        if(!empty($this->getRequestParams()->getQueryParam(self::PARAMETER_STATUS))){
            $filters['employee_status'] = $this->getRequestParams()->getQueryParam(self::PARAMETER_STATUS);
        }
        if(!empty($this->getRequestParams()->getQueryParam(self::PARAMETER_SUPERVISOR))){
            $filters['supervisor_name'] = $this->getRequestParams()->getQueryParam(self::PARAMETER_SUPERVISOR);
        }
        if(empty($filters)){
            return null;
        }
        $parameterHolder = new \EmployeeSearchParameterHolder();
        $parameterHolder->setFilters($filters);
        $parameterHolder->setReturnType(\EmployeeSearchParameterHolder::RETURN_TYPE_OBJECT);

        return $parameterHolder;
    }

    /**
     * @param $employeeList
     *
     * @return array
     */
    private function buildEmployeeData( $employeeList ) {
        $data = array();
        foreach ($employeeList as $employee) {
            $emp = new Employee($employee->getFirstName(), $employee->getMiddleName(), $employee->getLastName(), $employee->getEmployeeId());

            $emp->buildEmployee($employee);

            $data[] = $emp->toArray();
        }
        return $data;
    }
}
