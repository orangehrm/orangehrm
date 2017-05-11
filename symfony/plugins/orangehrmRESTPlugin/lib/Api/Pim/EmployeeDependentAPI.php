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

        $dependants = $this->getEmployeeService()->getEmployeeDependents($empId);

        foreach ($dependants as $dependent) {

            $empDependant = new EmployeeDependent($dependent->getName(), $dependent->getRelationship(),
                $dependent->getDateOfBirth(), $dependent->getSeqno());
            $responseArray[] = $empDependant->toArray();
        }
        return new Response($responseArray, array());
    }

    /**
     * Save employee dependent
     *
     * @return Response
     * @throws BadRequestException
     * @throws InvalidParamException
     */
    public function saveEmployeeDependents()
    {
        $filters = $this->filterParameters();
        if (empty($filters[self::PARAMETER_RELATIONSHIP])) {
            throw new InvalidParamException('Dependent Relationship Cannot Be Empty');
        }
        if (empty($filters[self::PARAMETER_NAME])) {
            throw new InvalidParamException('Dependent Name Cannot Be Empty');
        }
        $dependent = $this->buildEmployeeDependents($filters);

        $result = $this->getEmployeeService()->saveEmployeeDependent($dependent);

        if ($result instanceof \EmpDependent) {
            return new Response(array('success' => 'Successfully Saved', 'sequenceNumber' => $result->getSeqno() ));
        } else {
            throw new BadRequestException("Saving Failed");
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
        $filters = $this->filterParameters();
        if(!is_numeric( $filters[self::PARAMETER_SEQ_NUMBER] )) {
            throw new InvalidParamException("Sequence Number Is Wrong");
        }
        $dependent = $this->buildEmployeeDependents($filters);
        try {
            $result = $this->getEmployeeService()->updateEmployeeDependent($dependent);

        } catch (\Exception $pimEx) {
            throw new BadRequestException('Updating Failed');
        }

        if ($result instanceof \EmpDependent) {
            return new Response(array('success' => 'Successfully Updated'));
        } else {
            throw new BadRequestException("Updating Failed");
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

                return new Response(array('success' => 'Successfully Deleted'));
            } else {
                throw new RecordNotFoundException("Deleting Failed");
            }

        } else {
            throw new InvalidParamException("Sequence Number Is Wrong");
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
        $filters[self::PARAMETER_RELATIONSHIP] = $this->getRequestParams()->getPostParam(self::PARAMETER_RELATIONSHIP);

        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_RELATIONSHIP))) {
            $filters[self::PARAMETER_RELATIONSHIP] = $this->getRequestParams()->getPostParam(self::PARAMETER_RELATIONSHIP);
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
     * Build employee dependent
     *
     * @param $filters
     * @return \EmpDependent
     */
    protected function buildEmployeeDependents($filters)
    {
        $employeeDependent = new \EmpDependent();
        $employeeDependent->setSeqno($filters[self::PARAMETER_SEQ_NUMBER]);
        $employeeDependent->setEmpNumber($filters[self::PARAMETER_ID]);
        $employeeDependent->name = $filters[self::PARAMETER_NAME];
        $employeeDependent->relationship = $filters[self::PARAMETER_RELATIONSHIP];
        $employeeDependent ->relationship_type = 'other';
        $dob = date("Y-m-d", strtotime($filters[self::PARAMETER_DOB]));
        $employeeDependent->date_of_birth = $dob;

        return $employeeDependent;
    }


    public function getPostValidationRules()
    {
        return array(
            self::PARAMETER_DOB => array('Date' => array('Y-m-d')),
            self::PARAMETER_RELATIONSHIP => array('StringType' => true, 'NotEmpty' => true,'Length' => array(1,50)),
            self::PARAMETER_NAME => array('Length' => array(0, 50)),
        );
    }

    public function getPutValidationRules()
    {
        return array(
            self::PARAMETER_DOB => array('Date' => array('Y-m-d')),
            self::PARAMETER_RELATIONSHIP => array('StringType' => true, 'NotEmpty' => true,'Length' => array(1,50)),
            self::PARAMETER_NAME => array('Length' => array(0, 50), 'NotEmpty' => true),
            self::PARAMETER_SEQ_NUMBER=> array('NotEmpty' => true,'Length' => array(1,1000))
        );
    }

    public function getDelValidationRules()
    {
        return array(
          self::PARAMETER_SEQ_NUMBER=> array( 'NotEmpty' => true,'Length' => array(1,1000))
        );
    }

}


