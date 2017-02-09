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
use Orangehrm\Rest\Api\Pim\Entity\EmployeeContactDetail;
use Orangehrm\Rest\Api\Pim\Entity\EmployeeJobDetail;
use Orangehrm\Rest\Http\Response;

class EmployeeContactDetailAPI extends EndPoint
{

    /**
     * @var EmployeeService
     */
    private $employeeService;
    private $countryService;

    const PARAMETER_ID = "id";

    /*
     * contact detail post parameters
     */

    const PARAMETER_ADDRESS = "address";
    const PARAMETER_PHONE = "phone";
    const PARAMETER_EMAIL = "email";
    const PARAMETER_COUNTRY = "country";

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
     * Returns Country Service
     * @returns \CountryService
     */
    public function getCountryService()
    {
        if (is_null($this->countryService)) {
            $this->countryService = new \CountryService();
        }
        return $this->countryService;
    }

    public function setEmployeeService($employeeService)
    {
        $this->employeeService = $employeeService;
    }

    /**
     * Getting employee dependants API call
     *
     * @param $request
     * @return Response
     */
    public function getEmployeeContactDetails()
    {

        $responseArray = null;
        $empId = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);

        if (!is_numeric($empId)) {
            throw new InvalidParamException("Invalid Parameter");

        }

        $employee = $this->getEmployeeService()->getEmployee($empId);

        if (empty($employee)) {
            throw new RecordNotFoundException("Employee Not Found");

        }

        $employeeContactDetails = new EmployeeContactDetail($employee->getFullName(), $employee->getEmployeeId());
        $employeeContactDetails->buildContactDetails($employee);
        return new Response($employeeContactDetails->toArray(), array());
    }

    /**
     * save employee contact details
     *
     * @return Response
     */
    public function saveEmployeeContactDetails()
    {
        $relationsArray = array();
        $returned = null;

        $empId = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        $employee = $this->getEmployeeService()->getEmployee($empId);
        $this->buildEmployeeContactDetails($employee);
        $returnedEmployee = $this->getEmployeeService()->saveEmployee($employee);

        if($returnedEmployee instanceof \Employee) {
            return new Response(array('success' => 'Contact details successfully saved'), $relationsArray);
        } else {
            return new Response(array('Failed' => 'Contact details saving failed'), $relationsArray);
        }


    }

    /**
     * Build employee contact details
     *
     * @param \Employee
     * @return mixed
     */
    private function buildEmployeeContactDetails(\Employee $employee)
    {

        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_ADDRESS))) {
            $employee->setStreet1($this->getRequestParams()->getPostParam(self::PARAMETER_ADDRESS));
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_PHONE))) {
            $employee->setEmpMobile($this->getRequestParams()->getPostParam(self::PARAMETER_PHONE));
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_EMAIL))) {
            $employee->setEmpWorkEmail($this->getRequestParams()->getPostParam(self::PARAMETER_EMAIL));
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_COUNTRY))) {
            $country = $this->getCountryService()->getCountryByCountryName($this->getRequestParams()->getPostParam(self::PARAMETER_COUNTRY));
            $employee->setCountry($country->getCouCode());
        }

        return $employee;
    }

}
