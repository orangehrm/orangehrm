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
     * Saving Employee dependents
     *
     * @return Response|BadRequestException|InvalidParamException
     * @throws \PIMServiceException
     */
    public function saveEmployeeDependents()
    {

        $empId = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        $result = $this->getEmployeeService()->getEmpDependentMaxSeqNumber();
        $seqNo = is_null($result[0]['MAX']) ? 1 : $result[0]['MAX'] + 1;

        $filters = $this->filterParameters();

        if ($this->validateInputs($filters)) {

            $dependent = new \EmpDependent();
            $dependent->setEmpNumber($empId);
            $dependent->setSeqno($seqNo);
            $this->buildEmployeeDependants($dependent,$filters);
            $dependent->save();
            return new Response(array('success' => 'Successfully saved'));
        } else {
            return new InvalidParamException();
        }
    }

    public function updateEmployeeDependents(){

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
           return  false;

        }
        if (!date($format, strtotime($filters[self::PARAMETER_DOB])) == date($filters[self::PARAMETER_DOB])) {
            return  false;
        }

        if (!(preg_match("/^[a-zA-Z]*$/", $filters[self::PARAMETER_RELATIONSHIP]) === 1)) {
            return false;
        }
        if (!$filters[self::PARAMETER_TYPE] === 'other' || !$filters[self::PARAMETER_TYPE] === 'child') {   /// only 'other' and 'child' configured
            return false;
        }
        return $valid;
    }

}
