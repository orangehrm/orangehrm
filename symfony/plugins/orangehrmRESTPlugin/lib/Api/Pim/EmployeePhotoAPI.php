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
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Http\Response;

class EmployeePhotoAPI extends EndPoint
{
    const PARAMETER_ID = "id";

    protected $employeeService;

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
     * Get employee photo
     *
     * @return Response
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     */
    public function getEmployeePhoto()
    {

        $responseArray = null;
        $empId = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);

        if (!is_numeric($empId)) {
            throw new InvalidParamException("Invalid Parameter");
        }
        $employee = $this->getEmployeeService()->getEmployee($empId);

        if (empty($employee)) {
            throw new RecordNotFoundException("Employee Not Found");
        }
        if (!empty($this->getEmployeeService()->getEmployeePicture($empId))) {

            $employeePhoto['base64'] = base64_encode($this->getEmployeeService()->getEmployeePicture($empId)->getPicture());
        } else {
            throw new RecordNotFoundException('Employee Picture Not Found');
        }

        return new Response($employeePhoto, array());
    }

    /**
     * Return base64 encoded employee picture or null if picture not exists
     * @param $employeeId
     * @return string|null
     * @throws \DaoException
     */
    public function getEmployeePhotoById($employeeId)
    {
        $employeePicture = $this->getEmployeeService()->getEmployeePicture($employeeId);
        if ($employeePicture instanceof \EmpPicture) {
            return base64_encode($employeePicture->getPicture());
        }
        return null;
    }
}
