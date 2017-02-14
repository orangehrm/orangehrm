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
use Orangehrm\Rest\Http\Response;
use Orangehrm\Rest\Api\Exception\BadRequestException;

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
     * Get employee contact details
     *
     * @return Response
     * @throws InvalidParamException
     * @throws RecordNotFoundException
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
        $countryName = $this->getCountryService()->getCountryByCountryCode($employee->getCountry())[0]->getName();
        $employee->setCountry($countryName);
        $employeeContactDetails = new EmployeeContactDetail($employee->getFullName(), $employee->getEmployeeId());
        $employeeContactDetails->buildContactDetails($employee);
        return new Response($employeeContactDetails->toArray(), array());
    }

    /**
     * Save employee contact details
     *
     * @return Response
     * @throws BadRequestException
     */
    public function saveEmployeeContactDetails()
    {
        $relationsArray = array();
        $returned = null;
        $filters = $this->filterParameters();
        if ($this->validateInputs($filters)) {

            $empId = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
            $employee = $this->getEmployeeService()->getEmployee($empId);
            $this->buildEmployeeContactDetails($employee,$filters);
            $returnedEmployee = $this->getEmployeeService()->saveEmployee($employee);

            if (!($returnedEmployee instanceof \Employee)) {
                throw new BadRequestException("Contact details saving failed");
            }
            return new Response(array('success' => 'Contact details successfully saved'), $relationsArray);
        } else {
            throw new BadRequestException("Contact details saving failed");
        }


    }

    /**
     * Build employee contact details
     *
     * @param \Employee
     * @return mixed
     */
    private function buildEmployeeContactDetails(\Employee $employee, $filters)
    {
        $employee->setStreet1($filters[self::PARAMETER_ADDRESS]);
        $employee->setEmpMobile($filters[self::PARAMETER_PHONE]);
        $employee->setEmpWorkEmail($filters[self::PARAMETER_EMAIL]);
        $country = $this->getCountryService()->getCountryByCountryName($filters[self::PARAMETER_COUNTRY]);
        $employee->setCountry($country->getCouCode());

        return $employee;
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

        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_ADDRESS))) {
            $filters[self::PARAMETER_ADDRESS] = $this->getRequestParams()->getPostParam(self::PARAMETER_ADDRESS);
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_PHONE))) {
            $filters[self::PARAMETER_PHONE] = $this->getRequestParams()->getPostParam(self::PARAMETER_PHONE);
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_EMAIL))) {
            $filters[self::PARAMETER_EMAIL] = $this->getRequestParams()->getPostParam(self::PARAMETER_EMAIL);
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_COUNTRY))) {
            $filters[self::PARAMETER_COUNTRY] = $this->getRequestParams()->getPostParam(self::PARAMETER_COUNTRY);
        }

        return $filters;

    }

    /**
     * validate input parameters
     *
     * @param $filters
     * @return bool
     */
    protected function validateInputs($filters)
    {
        $valid = true;

        if (!empty( $filters[self::PARAMETER_ADDRESS]) &&!(preg_match( '/\d+ [0-9a-zA-Z ]+/', $filters[self::PARAMETER_ADDRESS]) === 1)) {
            return false;
        }
        if (!empty( $filters[self::PARAMETER_COUNTRY]) && !(preg_match("/^[a-z ,.'-]+$/i", $filters[self::PARAMETER_COUNTRY]) === 1)) {
            return false;
        }
        if (!empty( $filters[self::PARAMETER_EMAIL]) && !filter_var($filters[self::PARAMETER_EMAIL], FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        if (!empty( $filters[self::PARAMETER_PHONE]) && !(preg_match('/^\(?[0-9]{3}\)?|[0-9]{3}[-. ]? [0-9]{3}[-. ]?[0-9]{4}$/', $filters[self::PARAMETER_PHONE]) === 1)) {
            return false;
        }

        return $valid;
    }

}
