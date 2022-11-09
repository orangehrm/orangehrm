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
     * @var \EmployeeService
     */
    protected $employeeService;
    protected $countryService;
    private $employeeEventService;

    const PARAMETER_ID = "id";

    /*
     * contact detail post parameters
     */

    const PARAMETER_ADDRESS_STREET_1 = "addressStreet1";
    const PARAMETER_ADDRESS_STREET_2 = "addressStreet2";
    const PARAMETER_MOBILE = "mobile";
    const PARAMETER_WORK_EMAIL = "workEmail";
    const PARAMETER_COUNTRY = "country";
    const PARAMETER_CITY = "city";
    const PARAMETER_STATE = "state";
    const PARAMETER_ZIP = "zip";
    const PARAMETER_HOME_TELEPHONE = "homeTelephone";
    const PARAMETER_WORK_TELEPHONE = "workTelephone";
    const PARAMETER_OTHER_EMAIL = "otherEmail";


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
     * Set Country Service
     * @param CountryService $countryService
     */
    public function setCountryService($countryService)
    {
        $this->countryService = $countryService;
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
        $returned = null;
        $filters = $this->filterParameters();
        $empId = $filters[self::PARAMETER_ID];
        $employee = $this->getEmployeeService()->getEmployee($empId);

        if (!empty($employee) && $this->validateEmployeeEmails($employee,$filters[self::PARAMETER_WORK_EMAIL],
                $filters[self::PARAMETER_OTHER_EMAIL])
        ) {

            $this->buildEmployeeContactDetails($employee, $filters);
            $returnedEmployee = $this->getEmployeeService()->saveEmployee($employee);

            if (!($returnedEmployee instanceof \Employee)) {
                throw new BadRequestException("Saving Failed");
            } else {
                $this->getEmployeeEventService()->saveEvent($returnedEmployee->getEmpNumber(),\PluginEmployeeEvent::EVENT_TYPE_CONTACT_DETAIL,\PluginEmployeeEvent::EVENT_UPDATE,'Updating Contact Details','API');
                return new Response(array('success' => 'Successfully Saved'));
            }


        } else {
            throw new BadRequestException("Employee Not Found");
        }
    }

    /**
     * Update employee contact details
     *
     * @return Response
     * @throws BadRequestException
     */
    public function updateEmployeeContactDetails()
    {
        $returned = null;
        $filters = $this->filterParameters();

        $empId = $filters[self::PARAMETER_ID];
        $employee = $this->getEmployeeService()->getEmployee($empId);

        if (!empty($employee) && $this->validateEmployeeEmails($employee,$filters[self::PARAMETER_WORK_EMAIL],
                $filters[self::PARAMETER_OTHER_EMAIL])
        ) {

            $this->buildEmployeeContactDetails($employee, $filters);
            $returnedEmployee = $this->getEmployeeService()->saveEmployee($employee);

            if (!($returnedEmployee instanceof \Employee)) {
                throw new BadRequestException("Updating Failed");
            }else {
                $this->getEmployeeEventService()->saveEvent($returnedEmployee->getEmpNumber(),\PluginEmployeeEvent::EVENT_TYPE_CONTACT_DETAIL,\PluginEmployeeEvent::EVENT_UPDATE,'Updating Contact Details','API');
                return new Response(array('success' => 'Successfully Updated'));
            }
        } else {
            throw new BadRequestException("Employee Not Found");
        }

    }

    /**
     * Build Employee contact details
     *
     * @param \Employee $employee
     * @param $filters
     * @return \Employee
     * @throws InvalidParamException
     */
    protected function buildEmployeeContactDetails(\Employee $employee, $filters)
    {
        if (!empty($filters[self::PARAMETER_ADDRESS_STREET_1])) {
            $employee->setStreet1($filters[self::PARAMETER_ADDRESS_STREET_1]);
        }
        if (!empty($filters[self::PARAMETER_ADDRESS_STREET_2])) {
            $employee->setStreet2($filters[self::PARAMETER_ADDRESS_STREET_2]);
        }
        if (!empty($filters[self::PARAMETER_MOBILE])) {
            $employee->setEmpMobile($filters[self::PARAMETER_MOBILE]);
        }
        if (!empty($filters[self::PARAMETER_WORK_EMAIL])) {
            $employee->setEmpWorkEmail($filters[self::PARAMETER_WORK_EMAIL]);
        }

        if (!empty($filters[self::PARAMETER_COUNTRY])) {

            $country = $this->getCountryService()->getCountryByCountryName($filters[self::PARAMETER_COUNTRY]);
            if (!empty($country)) {
                $employee->setCountry($country->getCouCode());
            } else {
                throw new InvalidParamException('Invalid Country Name');
            }

        }
        if (!empty($filters[self::PARAMETER_CITY])) {
            $employee->setCity($filters[self::PARAMETER_CITY]);
        }
        if (!empty($filters[self::PARAMETER_STATE])) {
            $employee->setProvince($filters[self::PARAMETER_STATE]);
        }
        if (!empty($filters[self::PARAMETER_ZIP])) {
            $employee->setEmpZipcode($filters[self::PARAMETER_ZIP]);
        }
        if (!empty($filters[self::PARAMETER_HOME_TELEPHONE])) {
            $employee->setEmpHmTelephone($filters[self::PARAMETER_HOME_TELEPHONE]);
        }
        if (!empty($filters[self::PARAMETER_WORK_TELEPHONE])) {
            $employee->setEmpWorkTelephone($filters[self::PARAMETER_WORK_TELEPHONE]);
        }
        if (!empty($filters[self::PARAMETER_OTHER_EMAIL])) {
            $employee->setEmpOthEmail($filters[self::PARAMETER_OTHER_EMAIL]);
        }


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

        $filters[self::PARAMETER_ADDRESS_STREET_1] = $this->getRequestParams()->getPostParam(self::PARAMETER_ADDRESS_STREET_1);
        $filters[self::PARAMETER_ADDRESS_STREET_2] = $this->getRequestParams()->getPostParam(self::PARAMETER_ADDRESS_STREET_2);
        $filters[self::PARAMETER_MOBILE] = $this->getRequestParams()->getPostParam(self::PARAMETER_MOBILE);
        $filters[self::PARAMETER_WORK_EMAIL] = $this->getRequestParams()->getPostParam(self::PARAMETER_WORK_EMAIL);
        $filters[self::PARAMETER_COUNTRY] = $this->getRequestParams()->getPostParam(self::PARAMETER_COUNTRY);
        $filters[self::PARAMETER_CITY] = $this->getRequestParams()->getPostParam(self::PARAMETER_CITY);
        $filters[self::PARAMETER_STATE] = $this->getRequestParams()->getPostParam(self::PARAMETER_STATE);
        $filters[self::PARAMETER_ZIP] = $this->getRequestParams()->getPostParam(self::PARAMETER_ZIP);
        $filters[self::PARAMETER_HOME_TELEPHONE] = $this->getRequestParams()->getPostParam(self::PARAMETER_HOME_TELEPHONE);
        $filters[self::PARAMETER_WORK_TELEPHONE] = $this->getRequestParams()->getPostParam(self::PARAMETER_WORK_TELEPHONE);
        $filters[self::PARAMETER_OTHER_EMAIL] = $this->getRequestParams()->getPostParam(self::PARAMETER_OTHER_EMAIL);

        if (!empty($this->getRequestParams()->getUrlParam(self::PARAMETER_ID))) {
            $filters[self::PARAMETER_ID] = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        }

        return $filters;

    }

    /**
     * Respect validation rules
     *
     * @return array
     */
    public function getValidationRules()
    {
        return array(
            self::PARAMETER_ADDRESS_STREET_1 => array('Length' => array(0, 70)),
            self::PARAMETER_ADDRESS_STREET_2 => array('Length' => array(0, 70)),
            self::PARAMETER_ZIP => array('Length' => array(0, 10)),
            self::PARAMETER_STATE => array('Length' => array(0, 70)),
            self::PARAMETER_STATE => array('Length' => array(0, 70)),
            self::PARAMETER_CITY => array('Length' => array(0, 70)),
            self::PARAMETER_COUNTRY => array('Length' => array(0, 70)), //string
            self::PARAMETER_WORK_EMAIL => array('Email' => true),
            self::PARAMETER_OTHER_EMAIL => array('Email' => true),
            self::PARAMETER_MOBILE => array('Phone' => true),
            self::PARAMETER_WORK_TELEPHONE => array('Phone' => true),
            self::PARAMETER_HOME_TELEPHONE => array('Phone' => true)
        );
    }

    /**
     * Validate emails
     *
     * @param $workEmail
     * @param $otherEmail
     * @return bool
     * @throws BadRequestException
     */
        protected function validateEmployeeEmails(\Employee $employee, $workEmail, $otherEmail)
    {

        $emailList = $this->getEmployeeService()->getEmailList();
        if(!empty($workEmail) && !empty($workEmail) && $workEmail=== $otherEmail){
            throw new BadRequestException('Work Email And Other Email Cannot Be Same');
        }
        foreach ($emailList as $emails) {

            if(!$employee->getEmpWorkEmail() == $workEmail) {
                if($emails[emp_work_email] === $workEmail || $emails[emp_oth_email] === $workEmail ){
                    throw new BadRequestException('Work Email Exists');
                }
            }

            if(!$employee->getEmpOthEmail() == $otherEmail) {
                if($emails[emp_work_email] === $otherEmail || $emails[emp_oth_email] === $otherEmail ){
                    throw new BadRequestException('Other Email Exists');
                }
            }

        }
        return true;
    }

}
