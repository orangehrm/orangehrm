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

use Orangehrm\Rest\Api\Pim\Entity\Employee;


class EmployeeService
{
    //TODO move to a utility class
    public static $EQUALS = "==";
//    pub

    protected $request;
    protected $pimEmployeeService;

    protected function getPimEmployeeService(){
        if($this->pimEmployeeService != null){
            return $this->pimEmployeeService;
        }else {
            return new \EmployeeService();
        }
    }

    public function getEmployeeList() {

        $employeeT1 = new Employee('John','','Khan',35);
        $employeeT2 = new Employee('Simon','','Leo',24);
        return array(
            $employeeT1->toArray(),
            $employeeT2->toArray(),
        );
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param mixed $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * Employee response , based on the url params
     *
     * @param $httpRequest
     */
    public function getEmployeeResponse($httpRequest){

        $params = $httpRequest->getEmployeeSearchParams();
        $parametersList = explode(";",$params['search']);
        $empNumber = explode("==", $parametersList[0]);
        $emp = $this->getPimEmployeeService()->getEmployee($empNumber[1]);
        $apiEmployee = new Employee();
        $employeeWrapper = new EmployeeWrapper($apiEmployee);
        $employeeWrapper->setEmployee($emp);

        return array(
            $employeeWrapper->getApiEmployee()->toArray(),
        );

    }

}
