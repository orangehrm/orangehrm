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
use Orangehrm\Rest\Api\Pim\Entity\WorkExperience;
use Orangehrm\Rest\Http\Response;

class EmployeeWorkExperienceAPI extends EndPoint
{

    const PARAMETER_ID = "id";
    const PARAMETER_COMPANY = "company";
    const PARAMETER_JOB_TITLE= "title";
    const PARAMETER_FROM_DATE = "fromDate";
    const PARAMETER_TO_DATE = "toDate";
    const PARAMETER_SEQ_ID = "seqId";
    const PARAMETER_COMMENT = 'comment';


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
     * Get Employee work experience
     *
     * @return Response
     * @throws RecordNotFoundException
     */
    public function getEmployeeWorkExperience()
    {
        $responseArray = null;
        $empId = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        $this->validateEmployee($empId);
        $experienceRecords = $this->getEmployeeService()->getEmployeeWorkExperienceRecords($empId);

        if (count($experienceRecords)>0) {
            foreach ($experienceRecords as $experience) {
                $experienceEntity = new WorkExperience();
                $experienceEntity->build($experience);
                $responseArray[] = $experienceEntity->toArray();
            }
            return new Response($responseArray, array());
        } else {
            throw new RecordNotFoundException("No Records Found");
        }
    }

    /**
     * Save employee work experience
     *
     * @return Response
     * @throws BadRequestException
     */
    public function saveEmployeeWorkExperience()
    {
        $filters = $this->getFilterParameters();
        $this->validateEmployee($filters[self::PARAMETER_ID]);
        $this->validateDate($filters[self::PARAMETER_FROM_DATE],$filters[self::PARAMETER_TO_DATE]);
        $workExperience = $this->buildEmployeeWorkExperience($filters);
        $result = $this->getEmployeeService()->saveEmployeeWorkExperience($workExperience);

        if ($result instanceof \EmpWorkExperience) {
            return new Response(array('success' => 'Successfully Saved', 'seqId' => $result->getSeqno() ));
        } else {
            throw new BadRequestException("Saving Failed");
        }

    }

    /**
     * Update Employee work experience
     *
     * @return Response
     * @throws BadRequestException
     */
    public function updateEmployeeWorkExperience()
    {
        $filters = $this->getFilterParameters();
        $this->validateDate($filters[self::PARAMETER_FROM_DATE],$filters[self::PARAMETER_TO_DATE]);
        $this->validateEmployee($filters[self::PARAMETER_ID]);
        $experienceRecord = $this->getEmployeeService()->getEmployeeWorkExperienceRecords($filters[self::PARAMETER_ID], $filters[self::PARAMETER_SEQ_ID]);
        $this->validateWorkExperience($experienceRecord);
        $result = $this->getEmployeeService()->saveEmployeeWorkExperience($this->buildEmployeeWorkExperience($filters,$experienceRecord));

        if ($result instanceof \EmpWorkExperience) {
            return new Response(array('success' => 'Successfully Updated', 'seqId' => $result->getSeqno() ));
        } else {
            throw new BadRequestException("Updating Failed");
        }
    }

    /**
     * Delete employee work experience
     *
     * @return Response
     * @throws BadRequestException
     */
    public function deleteEmployeeWorkExperience()
    {
        $filters = $this->getFilterParameters();
        $this->validateEmployee($filters[self::PARAMETER_ID]);
        $experienceRecord = $this->getEmployeeService()->getEmployeeWorkExperienceRecords($filters[self::PARAMETER_ID], $filters[self::PARAMETER_SEQ_ID]);
        $this->validateWorkExperience($experienceRecord);
        $result = $this->getEmployeeService()->deleteEmployeeWorkExperienceRecords($filters[self::PARAMETER_ID], array($filters[self::PARAMETER_SEQ_ID]));

        if($result === 1){
            return new Response(array('success' => 'Successfully Deleted' ));
        }else if ( $result === 0) {
            throw new BadRequestException("Deleting Failed");
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

        $filters[self::PARAMETER_COMPANY] = $this->getPostParam(self::PARAMETER_COMPANY,$this->getRequestParams());
        $filters[self::PARAMETER_FROM_DATE] = $this->getPostParam(self::PARAMETER_FROM_DATE,$this->getRequestParams());
        $filters[self::PARAMETER_TO_DATE] = $this->getPostParam(self::PARAMETER_TO_DATE,$this->getRequestParams());
        $filters[self::PARAMETER_COMMENT] = $this->getPostParam(self::PARAMETER_COMMENT,$this->getRequestParams());
        $filters[self::PARAMETER_JOB_TITLE] = $this->getPostParam(self::PARAMETER_JOB_TITLE,$this->getRequestParams());
        $filters[self::PARAMETER_SEQ_ID] = $this->getPostParam(self::PARAMETER_SEQ_ID,$this->getRequestParams());
        $filters[self::PARAMETER_ID] = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);

        return $filters;

    }

    /**
     * Getting post parameters
     *
     * @param $parameterName
     * @param $requestParams
     * @return null| array
     */
    protected function getPostParam($parameterName,$requestParams){

        if( !empty($requestParams->getPostParam($parameterName))){
            return $requestParams->getPostParam($parameterName);
        }
        return null;
    }

    /**
     * Build work experience
     *
     * @param $filters
     * @param null $employeeWorkExperience
     * @return \EmpWorkExperience|null
     * @throws InvalidParamException
     */
    protected function buildEmployeeWorkExperience($filters, $employeeWorkExperience = null)
    {
        if($employeeWorkExperience == null) {

            $employeeWorkExperience = new \EmpWorkExperience();
        }

        if(!empty($filters[self::PARAMETER_COMPANY])){
            $employeeWorkExperience->setEmployer($filters[self::PARAMETER_COMPANY]);
        } else {
            throw new InvalidParamException('Company Cannot Be Empty');
        }
        if(!empty($filters[self::PARAMETER_FROM_DATE])){
            $employeeWorkExperience->setFromDate($filters[self::PARAMETER_FROM_DATE]);
        }
        if(!empty($filters[self::PARAMETER_TO_DATE])){
            $employeeWorkExperience->setToDate($filters[self::PARAMETER_TO_DATE]);
        }
        if(!empty($filters[self::PARAMETER_JOB_TITLE])){
            $employeeWorkExperience->setJobtitle($filters[self::PARAMETER_JOB_TITLE]);
        }else {
            throw new InvalidParamException('Job Title Cannot Be Empty');
        }
        if(!empty($filters[self::PARAMETER_COMMENT])){
            $employeeWorkExperience->setComments($filters[self::PARAMETER_COMMENT]);
        }
        $employeeWorkExperience->setEmpNumber($filters[self::PARAMETER_ID]);

        return $employeeWorkExperience;
    }

    /**
     *  GET|DEL validation rules are handled from the code itself
     *  so no validation rules written here
     */

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
            self::PARAMETER_COMPANY => array('StringType' => true, 'NotEmpty' => true,'Length' => array(1,100)),
            self::PARAMETER_JOB_TITLE => array('StringType' => true, 'NotEmpty' => true,'Length' => array(1,100)),
            self::PARAMETER_COMMENT => array('Length' => array(0, 200)),
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
            self::PARAMETER_COMPANY => array('StringType' => true, 'NotEmpty' => true,'Length' => array(1,100)),
            self::PARAMETER_JOB_TITLE => array('StringType' => true, 'NotEmpty' => true,'Length' => array(1,100)),
            self::PARAMETER_COMMENT => array('Length' => array(0, 200)),
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
     * Check for valid work experience objects
     *
     * @param $WorkExperience
     * @throws BadRequestException
     */
    protected function validateWorkExperience($WorkExperience){

        if (!$WorkExperience instanceof \EmpWorkExperience) {
            throw new BadRequestException("Work Experience Record Not Found");
        }
    }

    /**
     * Validate date
     *
     * @param $from
     * @param $to
     * @throws InvalidParamException
     */
    protected function validateDate($from,$to){

        if (!empty($from) && !empty($to)) {
            if ((strtotime($from)) > (strtotime($to))) {
                throw new InvalidParamException('To Date Should Be After From Date');
            }
        }
    }

}


