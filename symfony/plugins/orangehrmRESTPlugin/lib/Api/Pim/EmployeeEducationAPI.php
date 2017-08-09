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
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Pim\Entity\Education;
use Orangehrm\Rest\Http\Response;

class EmployeeEducationAPI extends EndPoint
{

    const PARAMETER_ID = "id";
    const PARAMETER_LEVEL = "level";
    const PARAMETER_INSTITUTE = "institute";
    const PARAMETER_GPA = "gpa";
    const PARAMETER_FROM_DATE = "startDate";
    const PARAMETER_TO_DATE = "endDate";
    const PARAMETER_SEQ_ID = "seqId";
    const PARAMETER_SPECIALIZATION = 'specialization';
    const PARAMETER_YEAR = 'year';

    private $educationService;

    /**
     * @return \EducationService
     */
    public function getEducationService()
    {
        if (!($this->educationService instanceof \EducationService)) {
            $this->educationService = new \EducationService();
        }

        return $this->educationService;
    }

    public function setEducationService($educationService)
    {
        $this->educationService = $educationService;
    }

    /**
     * Get Employee service
     *
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
     * Get employee education
     *
     * @return Response
     * @throws RecordNotFoundException
     */
    public function getEmployeeEducation()
    {
        $responseArray = null;
        $empId = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        $this->validateEmployee($empId);
        $educationRecords = $this->getEmployeeService()->getEmployeeEducations($empId);

        if(count($educationRecords)>0){
            foreach ($educationRecords as $education) {
                $educationEntity = new Education();
                $educationEntity->build($education);
                $responseArray[] = $educationEntity->toArray();
            }
            return new Response($responseArray, array());
        } else {
            throw new RecordNotFoundException("No Records Found");
        }

    }

    /**
     * Save employee education
     *
     * @return Response
     * @throws BadRequestException
     */
    public function saveEmployeeEducation()
    {
        $filters = $this->getFilterParameters();
        $this->validateEmployee($filters[self::PARAMETER_ID]);
        $this->validateDate($filters[self::PARAMETER_FROM_DATE], $filters[self::PARAMETER_TO_DATE]);
        $this->validateEducation($filters[self::PARAMETER_LEVEL]);
        $education = $this->buildEmployeeEducation($filters);
        $result = $this->getEmployeeService()->saveEmployeeEducation($education);

        if ($result instanceof \EmployeeEducation) {
            return new Response(array('success' => 'Successfully Saved', 'seqId' => $result->getId()));
        } else {
            throw new BadRequestException("Saving Failed");
        }

    }

    /**
     * Update employee education
     *
     * @return Response
     * @throws BadRequestException
     */
    public function updateEmployeeEducation()
    {
        $filters = $this->getFilterParameters();
        $this->validateEmployee($filters[self::PARAMETER_ID]);
        $this->validateDate($filters[self::PARAMETER_FROM_DATE], $filters[self::PARAMETER_TO_DATE]);
        $educationRecord = $this->getEmployeeService()->getEducation($filters[self::PARAMETER_SEQ_ID]);

        $this->validateEmployeeEducation($educationRecord);
        $this->validateEducation($filters[self::PARAMETER_LEVEL]);
        $result = $this->getEmployeeService()->saveEmployeeEducation($this->buildEmployeeEducation($filters,
            $educationRecord));

        if ($result instanceof \EmployeeEducation) {
            return new Response(array('success' => 'Successfully Updated', 'seqId' => $result->getId()));
        } else {
            throw new BadRequestException("Updating Failed");
        }
    }

    /**
     * Delete employee education
     *
     * @return Response
     * @throws BadRequestException
     */
    public function deleteEmployeeEducation()
    {
        $filters = $this->getFilterParameters();
        $this->validateEmployee($filters[self::PARAMETER_ID]);

        $educationRecord = $this->getEmployeeService()->getEmployeeEducations($filters[self::PARAMETER_ID],
            $filters[self::PARAMETER_SEQ_ID]);
        $this->validateEmployeeEducation($educationRecord[0]);
        $result = $this->getEmployeeService()->deleteEmployeeEducationRecords($filters[self::PARAMETER_ID],
            array($filters[self::PARAMETER_SEQ_ID]));

        if ($result === 1) {
            return new Response(array('success' => 'Successfully Deleted'));
        } else {
            if ($result === 0) {
                throw new BadRequestException("Deleting Failed");
            }
        }

    }

    /**
     * Filter Post parameters to validate
     *
     * @return array
     *
     */
    protected function getFilterParameters()
    {
        $filters[] = array();

        $filters[self::PARAMETER_LEVEL] = $this->getPostParam(self::PARAMETER_LEVEL, $this->getRequestParams());
        $filters[self::PARAMETER_TO_DATE] = $this->getPostParam(self::PARAMETER_TO_DATE, $this->getRequestParams());
        $filters[self::PARAMETER_FROM_DATE] = $this->getPostParam(self::PARAMETER_FROM_DATE, $this->getRequestParams());
        $filters[self::PARAMETER_INSTITUTE] = $this->getPostParam(self::PARAMETER_INSTITUTE, $this->getRequestParams());
        $filters[self::PARAMETER_GPA] = $this->getPostParam(self::PARAMETER_GPA, $this->getRequestParams());
        $filters[self::PARAMETER_SPECIALIZATION] = $this->getPostParam(self::PARAMETER_SPECIALIZATION,$this->getRequestParams());
        $filters[self::PARAMETER_SEQ_ID] = $this->getPostParam(self::PARAMETER_SEQ_ID, $this->getRequestParams());
        $filters[self::PARAMETER_YEAR] = $this->getPostParam(self::PARAMETER_YEAR, $this->getRequestParams());
        $filters[self::PARAMETER_ID] = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);

        return $filters;

    }

    /**
     * Getting post parameters
     *
     * @param $parameterName
     * @param $requestParams
     * @return null | array
     */
    protected function getPostParam($parameterName, $requestParams)
    {

        if (!empty($requestParams->getPostParam($parameterName))) {
            return $requestParams->getPostParam($parameterName);
        }
        return null;
    }

    /**
     * Build employee education
     *
     * @param $filters
     * @param null $employeeEducation
     * @return \EmployeeEducation|null
     */
    protected function buildEmployeeEducation($filters, $employeeEducation = null)
    {
        if ($employeeEducation == null) {

            $employeeEducation = new \EmployeeEducation();
        }

        if (!empty($filters[self::PARAMETER_LEVEL])) {
            $employeeEducation->setEducationId($filters[self::PARAMETER_LEVEL]);
        }
        if (!empty($filters[self::PARAMETER_INSTITUTE])) {
            $employeeEducation->setInstitute($filters[self::PARAMETER_INSTITUTE]);
        }
        if (!empty($filters[self::PARAMETER_FROM_DATE])) {
            $employeeEducation->setStartDate($filters[self::PARAMETER_FROM_DATE]);
        }
        if (!empty($filters[self::PARAMETER_TO_DATE])) {
            $employeeEducation->setEndDate($filters[self::PARAMETER_TO_DATE]);
        }
        if (!empty($filters[self::PARAMETER_GPA])) {
            $employeeEducation->setScore($filters[self::PARAMETER_GPA]);
        }
        if (!empty($filters[self::PARAMETER_SPECIALIZATION])) {
            $employeeEducation->setMajor($filters[self::PARAMETER_SPECIALIZATION]);
        }
        if (!empty($filters[self::PARAMETER_YEAR])) {
            $employeeEducation->setYear($filters[self::PARAMETER_YEAR]);
        }
        $employeeEducation->setEmpNumber($filters[self::PARAMETER_ID]);


        return $employeeEducation;
    }

    /**
     * Validation rules ( POST )
     *
     * @return array Validation rules
     */
    public function getPostValidationRules()
    {
        return array(
            self::PARAMETER_FROM_DATE => array('Date' => array('Y-m-d')),
            self::PARAMETER_TO_DATE => array('Date' => array('Y-m-d')),
            self::PARAMETER_GPA => array('StringType' => true, 'NotEmpty' => true, 'Length' => array(1, 25)),
            self::PARAMETER_INSTITUTE => array('StringType' => true, 'NotEmpty' => true, 'Length' => array(1, 100)),
            self::PARAMETER_SPECIALIZATION => array(
                'StringType' => true,
                'NotEmpty' => true,
                'Length' => array(1, 100)
            ),
            self::PARAMETER_YEAR => array('IntVal' => true, 'NotEmpty' => true, 'Length' => array(1, 4))

        );
    }

    /**
     * Validation rules ( PUT )
     *
     *
     * @return array
     */
    public function getPutValidationRules()
    {
        return array(
            self::PARAMETER_FROM_DATE => array('Date' => array('Y-m-d')),
            self::PARAMETER_TO_DATE => array('Date' => array('Y-m-d')),
            self::PARAMETER_GPA => array('StringType' => true, 'NotEmpty' => true, 'Length' => array(1, 25)),
            self::PARAMETER_INSTITUTE => array('StringType' => true, 'NotEmpty' => true, 'Length' => array(1, 100)),
            self::PARAMETER_SPECIALIZATION => array(
                'StringType' => true,
                'NotEmpty' => true,
                'Length' => array(1, 100)
            ),
            self::PARAMETER_YEAR => array('StringType' => true, 'NotEmpty' => true, 'Length' => array(1, 4))
        );
    }

    /**
     * Check the employee is valid
     *
     * @param $empId
     * @throws BadRequestException
     */
    protected function validateEmployee($empId)
    {
        $employee = $this->getEmployeeService()->getEmployee($empId);
        if (!$employee instanceof \Employee) {
            throw new BadRequestException("Employee Not Found");
        }
    }

    /**
     * Validate employee education
     *
     * @param $employeeEducation
     * @throws BadRequestException
     */
    protected function validateEmployeeEducation($employeeEducation)
    {
        if (!$employeeEducation instanceof \EmployeeEducation) {
            throw new BadRequestException("Employee Education Record Not Found");
        }
    }

    /**
     * Validate from and to dates
     *
     * @param $from
     * @param $to
     * @throws InvalidParamException
     */
    protected function validateDate($from, $to)
    {
        if (!empty($from) && !empty($to)) {
            if ((strtotime($from)) > (strtotime($to))) {
                throw new InvalidParamException('End Date Should Be After Start Date');
            }
        }
    }

    /**
     * Validate Education
     *
     * @param $educationId
     * @throws BadRequestException
     */
    protected function validateEducation($educationId)
    {
        $education = $this->getEducationService()->getEducationById($educationId);
        if (!$education instanceof \Education) {
            throw new BadRequestException("Employee Education Level Not Found");
        }
    }
}


