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
use Orangehrm\Rest\Http\Response;

class EmployeeTerminateAPI extends EndPoint
{

    /**
     * Employee terminate constants
     */
    const PARAMETER_ID = "id";
    const PARAMETER_TERMINATION_DATE = "date";
    const PARAMETER_REASON = "reason";  // reason to terminate
    const PARAMETER_NOTE = "note";


    /**
     * @var EmployeeService
     */
    protected $employeeService = null;
    protected $filters;

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
     * @param $employeeService
     */
    public function setEmployeeService($employeeService)
    {
        $this->employeeService = $employeeService;
    }

    /**
     * Terminate employee
     *
     * @return Response
     * @throws BadRequestException
     */
    public function terminateEmployee()
    {
        $this->filters = $this->filterParameters();
        if ($this->validateInputs($this->filters)) {

           $employee = $this->getEmployeeService()->getEmployee($this->filters[self::PARAMETER_ID]);

            if($employee instanceof \Employee){
                $employeeTerminationRecord = $this->buildTerminationRecord($this->filters);

            }else {
                throw new BadRequestException('Employee Not Exists');
            }


            $returnRecord = $this->getEmployeeService()->terminateEmployment($employeeTerminationRecord);

            if ($returnRecord instanceof \EmployeeTerminationRecord) {
                return new Response(array('success' => 'Successfully Terminated'));
            } else {
                throw new BadRequestException('Termination Failed');
            }
        } else {
            throw new BadRequestException('Termination Failed');
        }

    }

    /**
     * Filter post parameters to validate
     *
     * @return array
     */
    protected function filterParameters()
    {
        $filters[] = array();

        if (!empty($this->getRequestParams()->getUrlParam(self::PARAMETER_ID))) {
            $filters[self::PARAMETER_ID] = ($this->getRequestParams()->getUrlParam(self::PARAMETER_ID));
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_NOTE))) {
            $filters[self::PARAMETER_NOTE] = ($this->getRequestParams()->getPostParam(self::PARAMETER_NOTE));
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_TERMINATION_DATE))) {
            $filters[self::PARAMETER_TERMINATION_DATE] = ($this->getRequestParams()->getPostParam(self::PARAMETER_TERMINATION_DATE));
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_REASON))) {
            $filters[self::PARAMETER_REASON] = ($this->getRequestParams()->getPostParam(self::PARAMETER_REASON));
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

        $format = "Y-m-d";


        if (!$this->validateReason($filters)) {
            $valid = false;

        }

        if (!date($format,
                strtotime($filters[self::PARAMETER_TERMINATION_DATE])) == date($filters[self::PARAMETER_TERMINATION_DATE])
        ) {
            $valid = false;
        }


        return $valid;
    }

    /**
     * Validate termination reason
     *
     * @param $filters
     * @return bool
     */
    protected function validateReason($filters)
    {
        $reasonList = $this->getEmployeeService()->getTerminationReasonList();

        foreach ($reasonList as $reason) {
            if ($filters[self::PARAMETER_REASON] === $reason->getName()) {
                $this->filters[self::PARAMETER_REASON] = $reason->getId();

                return true;
            }
        }

    }

    protected function buildTerminationRecord($filters){

        $employeeTerminationRecord = new \EmployeeTerminationRecord();
        $employeeTerminationRecord->setDate($this->filters[self::PARAMETER_TERMINATION_DATE]);
        $employeeTerminationRecord->setReasonId($this->filters[self::PARAMETER_REASON]);
        $employeeTerminationRecord->setEmpNumber($this->filters[self::PARAMETER_ID]);
        $employeeTerminationRecord->setNote($this->filters[self::PARAMETER_NOTE]);

        return $employeeTerminationRecord;
    }

    public function getPostValidationRules()
    {
        return array(
            self::PARAMETER_TERMINATION_DATE => array('Date' => array('Y-m-d')),
            self::PARAMETER_NOTE => array('StringType' => true, 'NotEmpty' => true,'Length' => array(1,250)),

        );
    }

}
