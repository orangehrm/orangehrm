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
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Pim\Entity\Employee;
use Orangehrm\Rest\Api\Pim\Entity\EmployeeDependent;
use Orangehrm\Rest\Http\Response;

class EmployeeDependentAPI extends EndPoint
{

    const PARAMETER_ID = "id";
    const PARAMETER_NAME = "name";
    const PARAMETER_RELATIONSHIP = "relationship";
    const PARAMETER_DOB = "dob";
    const PARAMETER_TYPE = "type";
    const PARAMETER_SEQ_NUMBER = "sequenceNumber";

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
     * get employee dependants
     *
     * @return Response
     * @throws InvalidParamException
     */
    public function getEmployeeDependents()
    {

        $responseArray = null;
        $empId = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);

        if (!is_numeric($empId)) {
            throw new InvalidParamException("Invalid Parameter");

        }
        $dependants = $this->getEmployeeService()->getEmployeeDependents($empId);

        foreach ($dependants as $dependant) {
            $relationship = '';
            if ($dependant->getRelationshipType() == 'other') {
                $relationship = $dependant->getRelationship();
            } else {
                $relationship = $dependant->getRelationshipType();
            }
            $empDependant = new EmployeeDependent($dependant->getName(), $relationship, $dependant->getDateOfBirth());
            $responseArray[] = $empDependant->toArray();
        }
        return new Response($responseArray, array());
    }

    /**
     * Saving Employee details
     *
     * @return Response
     * @throws BadRequestException
     */
    public function saveEmployeeDependents()
    {

        $empId = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);

        $filters = $this->filterParameters();

        if ($this->validateInputs($filters)) {

            $dependent = new \EmpDependent();
            $dependent->setEmpNumber($empId);

            $this->buildEmployeeDependants($dependent, $filters);
            $result = $this->getEmployeeService()->saveEmployeeDependent($dependent); // saving = true

            if ($result instanceof \EmpDependent) {
                return new Response(array('success' => 'Successfully saved'));
            } else {
                throw new BadRequestException("Saving Failed");
            }

        } else {
            throw new BadRequestException("Invalid parameter");
        }


    }

    /**
     * Update employee dependents
     *
     * @return Response
     * @throws BadRequestException
     * @throws InvalidParamException
     */
    public function updateEmployeeDependents()
    {
        $empId = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);

        $filters = $this->filterParameters();

        if ($this->validateInputs($filters)) {

            $dependent = new \EmpDependent();
            $dependent->setEmpNumber($empId);
            $dependent->setSeqno($filters[self::PARAMETER_SEQ_NUMBER]);

            $this->buildEmployeeDependants($dependent, $filters);
            try {
                $result = $this->getEmployeeService()->updateEmployeeDependent($dependent); // saving = true

            } catch (\PIMServiceException $pimEx) {
                throw new BadRequestException('Updating failed');
            }

            if ($result instanceof \EmpDependent) {
                return new Response(array('success' => 'Successfully updated'));
            } else {
                throw new BadRequestException("Updating failed");
            }

        } else {
            throw new InvalidParamException("Invalid parameter");
        }
    }

    /**
     * Deleting employee dependents
     *
     * @return Response
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     */
    public function deleteEmployeeDependents()
    {
        $filters = $this->filterParameters();
        $empId = $filters[self::PARAMETER_ID];
        $sequenceNumber = $filters[self::PARAMETER_SEQ_NUMBER];

        if (!empty($sequenceNumber) && is_numeric($sequenceNumber)) {

            $count = $this->getEmployeeService()->deleteEmployeeDependents($empId, array($sequenceNumber));

            if ($count > 0) {

                return new Response(array('success' => 'Successfully deleted'));
            } else {
                throw new RecordNotFoundException("Deleting failed");
            }

        } else {
            throw new InvalidParamException("Sequence number is wrong");
        }


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

        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_NAME))) {
            $filters[self::PARAMETER_NAME] = $this->getRequestParams()->getPostParam(self::PARAMETER_NAME);
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_RELATIONSHIP))) {
            $filters[self::PARAMETER_RELATIONSHIP] = $this->getRequestParams()->getPostParam(self::PARAMETER_RELATIONSHIP);
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_TYPE))) {
            $filters[self::PARAMETER_TYPE] = $this->getRequestParams()->getPostParam(self::PARAMETER_TYPE);
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_DOB))) {
            $filters[self::PARAMETER_DOB] = $this->getRequestParams()->getPostParam(self::PARAMETER_DOB);
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_SEQ_NUMBER))) {
            $filters[self::PARAMETER_SEQ_NUMBER] = $this->getRequestParams()->getPostParam(self::PARAMETER_SEQ_NUMBER);
        }
        if (!empty($this->getRequestParams()->getUrlParam(self::PARAMETER_ID))) {
            $filters[self::PARAMETER_ID] = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        }
        return $filters;

    }

    /**
     * Building employee dependent details
     *
     * @param \EmpDependent $employeeDependent
     *
     */
    protected function buildEmployeeDependants(\EmpDependent $employeeDependent, $filters)
    {
        $employeeDependent->name = $filters[self::PARAMETER_NAME];
        $employeeDependent->relationship = $filters[self::PARAMETER_RELATIONSHIP];
        $employeeDependent->relationship_type = $filters[self::PARAMETER_TYPE];
        $dob = date("Y-m-d", strtotime($filters[self::PARAMETER_DOB]));
        $employeeDependent->date_of_birth = $dob;
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

        $format = "Y-m-d";


        if (!(preg_match("/^[a-z ,.'-]+$/i", $filters[self::PARAMETER_NAME]) === 1)) {
            return false;

        }
        if (!empty($filters[self::PARAMETER_DOB]) && !date($format,
                strtotime($filters[self::PARAMETER_DOB])) == date($filters[self::PARAMETER_DOB])
        ) {
            return false;
        }

        if (empty($filters[self::PARAMETER_RELATIONSHIP]) || !(preg_match("/^[a-zA-Z]*$/",
                    $filters[self::PARAMETER_RELATIONSHIP]) === 1)
        ) {
            return false;
        }
        if (!empty($filters[self::PARAMETER_TYPE]) && !($filters[self::PARAMETER_TYPE] == 'other')) {
            return false;
        }
        return $valid;
    }

}
