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
use Orangehrm\Rest\Api\Pim\Entity\EmployeeJobDetail;
use Orangehrm\Rest\Http\Response;

class EmployeeJobDetailAPI extends EndPoint {

    const PARAMETER_ID         = "id";
    const PARAMETER_TITILE         = "title";
    const PARAMETER_CATEGORY       = "category";
    const PARAMETER_JOINED_DATE       = "joinedDate";
    const PARAMETER_START_DATE       = "startDate";
    const PARAMETER_END_DATE       = "endDate";


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

    public function setEmployeeService($employeeService){
        $this->employeeService = $employeeService;
    }

    /**
     * Get employee job details
     *
     * @return Response
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     */
    public function getEmployeeJobDetails() {

        $responseArray = null;
        $empId = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);

        if (!is_numeric($empId)) {
            throw new InvalidParamException("Invalid Parameter");

        }

        $employee = $this->getEmployeeService()->getEmployee($empId);

        if (empty($employee)) {
            throw new RecordNotFoundException("Employee Not Found");

        }

        $employeeJobDetails = new EmployeeJobDetail();
        $employeeJobDetails->build($employee);
        return new Response($employeeJobDetails->toArray() ,array());
    }

    /**
     * Save employee job details
     *
     * @return Response
     */
    public function saveEmployeeJobDetails()
    {

        $relationsArray = array();
        $returned = null;

        $empId = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        $employee = $this->getEmployeeService()->getEmployee($empId);
        $this->buildEmployeeJobDetails($employee);
        $returnedEmployee = $this->getEmployeeService()->saveEmployee($employee);

        if($returnedEmployee instanceof \Employee) {
            return new Response(array('success' => 'successfully saved'), $relationsArray);
        } else {
            return new Response(array('Failed' => 'saving failed'), $relationsArray);
        }

    }

    /**
     * Build employee job details
     *
     * @param \Employee $employee
     *
     */
    private function buildEmployeeJobDetails(\Employee $employee)
    {
        $empContract = new \EmpContract();
        $empContract->emp_number = $employee->empNumber;
        $empContract->contract_id = 1;

        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_TITILE))) {
            $employee->job_title_code = $this->getRequestParams()->getPostParam(self::PARAMETER_TITILE);
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_CATEGORY))) {
            $employee->eeo_cat_code = $this->getRequestParams()->getPostParam(self::PARAMETER_CATEGORY);
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_JOINED_DATE))) {
            $joinedDate = date("Y-m-d", strtotime($this->getRequestParams()->getQueryParam(self::PARAMETER_JOINED_DATE)));
            $employee->setJoinedDate($joinedDate);
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_START_DATE))) {
            $startDate= date("Y-m-d", strtotime($this->getRequestParams()->getQueryParam(self::PARAMETER_START_DATE)));
            $empContract->start_date = $startDate;
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_END_DATE))) {
            $endDate = date("Y-m-d", strtotime($this->getRequestParams()->getQueryParam(self::PARAMETER_END_DATE)));
            $empContract->end_date = $endDate;

        }

        $employee->contracts[0] = $empContract;
    }
}
