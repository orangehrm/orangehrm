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

namespace Orangehrm\Rest\Api\Time;

use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Pim\Entity\Employee;
use Orangehrm\Rest\Api\Pim\Entity\EmployeeDependent;
use Orangehrm\Rest\Http\Response;

class ProjectAPI extends EndPoint
{

    const PARAMETER_CUSTOMER_ID = "customerId";
    const PARAMETER_NAME = "name";
    const PARAMETER_DESCRIPTION = "description";

    private $employeeEventService;
    private $employeeService;

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
     *
     * @return ProjectService
     */
    public function getProjectService() {
        if (is_null($this->projectService)) {
            $this->projectService = new \ProjectService();
        }
        return $this->projectService;
    }

    public function setEmployeeService($employeeService)
    {
        $this->employeeService = $employeeService;
    }

    /**
     * @return TimesheetService
     */
    public function getTimesheetService() {

        if (is_null($this->timesheetService)) {

            $this->timesheetService = new \TimesheetService();
        }

        return $this->timesheetService;
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
     * get employee timesheets
     *
     * @return Response
     * @throws InvalidParamException
     */
    public function getProjects()
    {


        $projects = $this->getProjectService()->getAllProjects();

        foreach ($projects as $project) {

//            $empDependant = new EmployeeDependent($dependent->getName(), $dependent->getRelationship(),
//                $dependent->getDateOfBirth(), $dependent->getSeqno());
            $responseArray[] = $project->toArray();
        }
//        $responseArray[] = $empId;
        return new Response($responseArray, array());
    }

    /**
     * Save employee Timesheet
     *
     * @return Response
     * @throws BadRequestException
     * @throws InvalidParamException
     */
    public function saveProject()
    {
        $filters = $this->filterParameters();
        $project = new \Project();
        $project->setCustomerId($filters[self::PARAMETER_CUSTOMER_ID]);
        $project->setName($filters[self::PARAMETER_NAME]);
        $project->setDescription($filters[self::PARAMETER_DESCRIPTION]);
        $project->save();
        return new Response($this->getRequestParams(), array());

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

        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_CUSTOMER_ID))) {
            $filters[self::PARAMETER_CUSTOMER_ID] = $this->getRequestParams()->getPostParam(self::PARAMETER_CUSTOMER_ID);
        }

        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_NAME))) {
            $filters[self::PARAMETER_NAME] = $this->getRequestParams()->getPostParam(self::PARAMETER_NAME);
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_DESCRIPTION))) {
            $filters[self::PARAMETER_DESCRIPTION] = $this->getRequestParams()->getPostParam(self::PARAMETER_DESCRIPTION);
        }

        return $filters;

    }


    public function getPostValidationRules()
    {
        return array();
//        return array(
//            self::PARAMETER_DOB => array('Date' => array('Y-m-d')),
//            self::PARAMETER_RELATIONSHIP => array('StringType' => true, 'NotEmpty' => true,'Length' => array(1,50)),
//            self::PARAMETER_NAME => array('Length' => array(0, 50)),
//        );
    }


}


