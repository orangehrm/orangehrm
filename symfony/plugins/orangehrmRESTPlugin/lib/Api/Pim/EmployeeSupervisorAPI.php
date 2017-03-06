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
use Orangehrm\Rest\Api\Pim\Entity\Supervisor;
use Orangehrm\Rest\Http\Response;

class EmployeeSupervisorAPI extends EndPoint
{


    const PARAMETER_ID = "id";
    const PARAMETER_SUPERVISOR_ID = 'supervisorId';
    const PARAMETER_REPORTING_METHOD = 'reportingMethod';

    private $reportingMethodConfigurationService;
    protected $employeeService;


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
     * Get supervisor details
     *
     * @return Response
     *
     */
    public function getEmployeeSupervisors()
    {
        $employeeId = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        $employee = $this->getEmployeeService()->getEmployee($employeeId);
        if(empty($employee)){
            throw new RecordNotFoundException('Employee Not Found');
        }
        $supervisors = $this->getEmployeeService()->getImmediateSupervisors($employeeId);

        foreach ($supervisors as $supervisorRM) {

            $empSupervisor = $supervisorRM->getSupervisor();
            $supervisor = new Supervisor($empSupervisor->getFullName(), $empSupervisor->getempNumber(),
                $empSupervisor->getEmployeeId(), $supervisorRM->getReportingMethod()->getName());
            $responseArray[] = $supervisor->toArray();
        }

        return new Response($responseArray, array());

    }

    /**
     * Add supervisor
     *
     * @return Response
     */
    public function saveEmployeeSupervisor()
    {
        $employeeId = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        $supervisorId = $this->getRequestParams()->getPostParam(self::PARAMETER_SUPERVISOR_ID);
        $reportingMethodName = $this->getRequestParams()->getPostParam(self::PARAMETER_REPORTING_METHOD);

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

        if (!empty($existingReportToObject)) {

            $existingReportToObject->setReportingMethodId($reportingMethodId);
            $existingReportToObject->save();
            return new Response(array('success' => 'Successfully saved'));

        } else {

            $newReportToObject = new \ReportTo();
            $newReportToObject->setSupervisorId($supervisorId);
            $newReportToObject->setSubordinateId($employeeId);
            $newReportToObject->setReportingMethodId($reportingMethodId);
            $newReportToObject->save();
            return new Response(array('success' => 'Successfully saved'));

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
        $employeeId = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        $supervisorId = $this->getRequestParams()->getPostParam(self::PARAMETER_SUPERVISOR_ID);
        $existingReportToObject = $this->getEmployeeService()->getReportToObject($supervisorId, $employeeId);
        $reportingMethodName = $this->getRequestParams()->getPostParam(self::PARAMETER_REPORTING_METHOD);
        $reportingMethod = $this->getReportingMethodConfigurationService()->getReportingMethodByName($reportingMethodName);

        if(empty($existingReportToObject)|| empty($reportingMethod)) {
            throw new RecordNotFoundException('Supervisor not found');
        } else {
            $this->getEmployeeService()->removeSupervisor($supervisorId,$employeeId,$reportingMethodName);
            return new Response(array('success' => 'Successfully deleted'));
        }
    }

    public function getValidationRules()
    {
        return array(
            self::PARAMETER_SUPERVISOR_ID => array('NotEmpty' => true, 'Length' => array(1, 10)),
            self::PARAMETER_REPORTING_METHOD => array('StringType' => true, 'NotEmpty' => true),
        );

    }


}
