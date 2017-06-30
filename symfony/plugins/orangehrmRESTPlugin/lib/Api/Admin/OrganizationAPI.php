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

namespace Orangehrm\Rest\Api\Admin;
use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Http\Response;

class OrganizationAPI extends EndPoint
{
    protected $organizationService;
    protected $employeeService;

    /**
     * @return mixed
     */
    public function getEmployeeService()
    {
        if($this->employeeService == null){
            $this->employeeService = new \EmployeeService();
        }
        return $this->employeeService;
    }

    /**
     * @param mixed $employeeService
     */
    public function setEmployeeService($employeeService)
    {
        $this->employeeService = $employeeService;
    }



    /**
     * @return \OrganizationService|null
     */
    protected function getOrganizationService()
    {
        if ($this->organizationService != null) {
            return $this->organizationService;
        } else {
            return new \OrganizationService();
        }
    }

    public function setOrganizationService($employeeService)
    {
        $this->employeeService = $employeeService;
    }


    /**
     * Get Organization
     *
     * @return Response
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     */
    public function getOrganization(){
        $responseArray = null;
        $organization =$this->getOrganizationService()->getOrganizationGeneralInformation()->toArray();
        $organization['numberOfEmployees'] = $this->getEmployeeService()->getEmployeeCount();
        return new Response($organization, array());
    }


}