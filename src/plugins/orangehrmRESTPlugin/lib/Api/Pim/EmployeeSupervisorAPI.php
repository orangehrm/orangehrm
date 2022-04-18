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
use Orangehrm\Rest\Api\Pim\Entity\Supervisor;
use Orangehrm\Rest\Http\Response;

class EmployeeSupervisorAPI extends EndPoint
{

    const PARAMETER_ID = "id";
    const PARAMETER_SUPERVISOR_ID = 'supervisorId';
    const PARAMETER_REPORTING_METHOD = 'reportingMethod';

    private $reportingMethodConfigurationService;
    protected $employeeService;
    private $employeeEventService;


    /**
     * @return \EmployeeService|null
     */
    public function getEmployeeService()
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

    public function getReportingMethodConfigurationService()
    {
        if (is_null($this->reportingMethodConfigurationService)) {
            $this->reportingMethodConfigurationService = new \ReportingMethodConfigurationService();
        }

        return $this->reportingMethodConfigurationService;

    }

    public function setReportingMethodConfigurationService($reportingMethodConfigurationService)
    {
        $this->reportingMethodConfigurationService = $reportingMethodConfigurationService;
    }

    /**
     * Get employee supervisor details
     *
     * @return Response
     * @throws RecordNotFoundException
     */
    public function getEmployeeSupervisors()
    {
        $employeeId = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        $employee = $this->getEmployeeService()->getEmployee($employeeId);
        if (empty($employee)) {
            throw new RecordNotFoundException('Employee Not Found');
        }
        $supervisors = $this->getEmployeeService()->getImmediateSupervisors($employeeId);

        foreach ($supervisors as $supervisorRM) {

            $empSupervisor = $supervisorRM->getSupervisor();
            $supervisor = new Supervisor($empSupervisor->getFullName(), $empSupervisor->getempNumber(),
            $empSupervisor->getEmployeeId(), $supervisorRM->getReportingMethod()->getName());
            $responseArray[] = $supervisor->toArray();
        }
        if(count($responseArray) > 0){
            return new Response($responseArray, array());
        } else {
            throw new RecordNotFoundException("No Records Found");
        }

    }

    /**
     * Save supervisor
     *
     * @return Response
     */
    public function saveEmployeeSupervisor()
    {
        $employeeId = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        $supervisorId = $this->getRequestParams()->getPostParam(self::PARAMETER_SUPERVISOR_ID);
        $reportingMethodName = $this->getRequestParams()->getPostParam(self::PARAMETER_REPORTING_METHOD);

        $this->validateEmployee($employeeId);

        if (empty($supervisorId) || empty($reportingMethodName) ) {
            throw new InvalidParamException('Invalid Parameter');
        }
        if($supervisorId == $employeeId){
            throw new InvalidParamException('Cannot Add Same Employee As Supervisor');
        }
        $supervisor = $this->getEmployeeService()->getEmployee($supervisorId);
        if (!empty($supervisor)) {
            $reportingMethod = $this->getReportingMethodConfigurationService()->getReportingMethodByName($reportingMethodName);

            $reportingMethodId = null;

            if (empty($reportingMethod)) {
                $newReportingMethod = new \ReportingMethod();
                $newReportingMethod->name = $reportingMethodName;
                $savedReportingMethod = $this->getReportingMethodConfigurationService()->saveReportingMethod($newReportingMethod);
                $reportingMethodId = $savedReportingMethod->id;
            } else {
                $reportingMethodId = $reportingMethod->id;
            }

            $existingReportToObject = $this->getEmployeeService()->getReportToObject($supervisorId, $employeeId);


            if (!empty($existingReportToObject) && $this->getRequestParams()->getRequest()->isMethod('put')) {

                $existingReportToObject->setReportingMethodId($reportingMethodId);
                $existingReportToObject->save();
                $this->getEmployeeEventService()->saveEvent($employeeId,\PluginEmployeeEvent::EVENT_TYPE_SUPERVISOR,\PluginEmployeeEvent::EVENT_SAVE,'Updating Employee Supervisor','API');

                return new Response(array('success' => 'Successfully Updated'));

            } elseif(empty($existingReportToObject) && $this->getRequestParams()->getRequest()->isMethod('post')){

                $newReportToObject = new \ReportTo();
                $newReportToObject->setSupervisorId($supervisorId);
                $newReportToObject->setSubordinateId($employeeId);
                $newReportToObject->setReportingMethodId($reportingMethodId);
                $newReportToObject->save();
                $this->getEmployeeEventService()->saveEvent($employeeId,\PluginEmployeeEvent::EVENT_TYPE_SUPERVISOR,\PluginEmployeeEvent::EVENT_SAVE,'Saving Employee Supervisor','API');

                return new Response(array('success' => 'Successfully Saved'));

            } else {
                throw new BadRequestException('Supervisor Already Added');
            }
        } else {
            throw new RecordNotFoundException('Supervisor Not Found');
        }

    }

    /**
     * Update employee supervisor
     *
     * @return Response
     * @throws BadRequestException
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     */
    public function updateEmployeeSupervisor()
    {
        $employeeId = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        $supervisorId = $this->getRequestParams()->getPostParam(self::PARAMETER_SUPERVISOR_ID);
        $reportingMethodName = $this->getRequestParams()->getPostParam(self::PARAMETER_REPORTING_METHOD);

        $this->validateEmployee($employeeId);

        if (empty($supervisorId) || empty($reportingMethodName) ) {
            throw new InvalidParamException('Invalid Parameter');
        }
        if($supervisorId == $employeeId){
            throw new InvalidParamException('Cannot Add Same Employee As Supervisor');
        }
        $supervisor = $this->getEmployeeService()->getEmployee($supervisorId);
        if (!empty($supervisor)) {
            $reportingMethod = $this->getReportingMethodConfigurationService()->getReportingMethodByName($reportingMethodName);

            $reportingMethodId = null;

            if (empty($reportingMethod)) {
                $newReportingMethod = new \ReportingMethod();
                $newReportingMethod->name = $reportingMethodName;
                $savedReportingMethod = $this->getReportingMethodConfigurationService()->saveReportingMethod($newReportingMethod);
                $reportingMethodId = $savedReportingMethod->id;
            } else {
                $reportingMethodId = $reportingMethod->id;
            }

            $existingReportToObject = $this->getEmployeeService()->getReportToObject($supervisorId, $employeeId);


            if (!empty($existingReportToObject) && $this->getRequestParams()->getRequest()->isMethod('put')) {

                $existingReportToObject->setReportingMethodId($reportingMethodId);
                $existingReportToObject->save();
                $this->getEmployeeEventService()->saveEvent($employeeId,\PluginEmployeeEvent::EVENT_TYPE_SUPERVISOR,\PluginEmployeeEvent::EVENT_SAVE,'Updating Employee Supervisor','API');

                return new Response(array('success' => 'Successfully Updated'));

            } else {
                throw new BadRequestException('No Supervisors Added');
            }
        } else {
            throw new RecordNotFoundException('Supervisor Not Found');
        }

    }

    /**
     * Delete supervisor
     *
     * @return Response
     * @throws RecordNotFoundException
     */
    public function deleteEmployeeSupervisor()
    {
        $filters = $this->getFilters();
        $employeeId = $filters[self::PARAMETER_ID];
        $supervisorId = $filters[self::PARAMETER_SUPERVISOR_ID];
        $reportingMethodName = $filters[self::PARAMETER_REPORTING_METHOD];
        $existingReportToObject = $this->getEmployeeService()->getReportToObject($supervisorId, $employeeId);
        $reportingMethod = $this->getReportingMethodConfigurationService()->getReportingMethodByName($reportingMethodName);

        if (empty($supervisorId) || empty($reportingMethodName)) {
            throw new InvalidParamException('Invalid Parameter');
        }
        if (empty($existingReportToObject) || empty($reportingMethod)) {
            throw new RecordNotFoundException('Supervisor Not Found');
        } else {
            $status = $this->getEmployeeService()->removeSupervisor($supervisorId, $employeeId,
                $reportingMethod->getId());
            if ($status) {
                $this->getEmployeeEventService()->saveEvent($employeeId,\PluginEmployeeEvent::EVENT_TYPE_SUPERVISOR,\PluginEmployeeEvent::EVENT_DELETE,'Deleting Employee Supervisor','API');
                return new Response(array('success' => 'Successfully Deleted'));
            } else {
                throw new BadRequestException('Deleting Failed');
            }

        }
    }

    public function getValidationRules()
    {
        return array(
            self::PARAMETER_SUPERVISOR_ID => array('NotEmpty' => true, 'Length' => array(1, 10)),
            self::PARAMETER_REPORTING_METHOD => array('StringType' => true, 'NotEmpty' => true),
        );

    }

    public function getFilters(){

        $filters[] = array();

        $filters[self::PARAMETER_ID] = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        $filters[self::PARAMETER_SUPERVISOR_ID] = $this->getRequestParams()->getPostParam(self::PARAMETER_SUPERVISOR_ID);
        $filters[self::PARAMETER_REPORTING_METHOD] = $this->getRequestParams()->getPostParam(self::PARAMETER_REPORTING_METHOD);

        return $filters;

    }

    /**
     * Validate employee
     *
     * @param $id employee ID
     * @throws RecordNotFoundException
     */
    public function validateEmployee($id){

        $employee = $this->getEmployeeService()->getEmployee($id);

        if (empty($employee)) {
            throw new RecordNotFoundException('Employee Not Found');
        }
    }


}
