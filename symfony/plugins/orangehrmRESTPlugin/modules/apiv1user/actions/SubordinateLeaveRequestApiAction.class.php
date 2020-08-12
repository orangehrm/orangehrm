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

use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Api\Exception\NotImplementedException;
use Orangehrm\Rest\Api\Leave\SaveLeaveRequestAPI;
use Orangehrm\Rest\Api\User\AssignLeaveRequestAPI;
use Orangehrm\Rest\Http\Request;

class SubordinateLeaveRequestApiAction extends BaseUserApiAction
{
    /**
     * @var null|AssignLeaveRequestAPI
     */
    private $assignLeaveRequestAPI = null;

    protected function init(Request $request)
    {
        $this->assignLeaveRequestAPI = new AssignLeaveRequestAPI($request);
        $this->postValidationRule = $this->assignLeaveRequestAPI->getValidationRules();
    }

    protected function handleGetRequest(Request $request)
    {
        throw new NotImplementedException();
    }

    protected function handlePostRequest(Request $request)
    {
        $this->setUserToContext();
        $empNumber = $this->assignLeaveRequestAPI->getRequestParams()->getUrlParam(SaveLeaveRequestAPI::PARAMETER_ID);
        if (!in_array($empNumber, $this->getAccessibleEmpNumbers())) {
            throw new BadRequestException('Access Denied');
        }
        return $this->assignLeaveRequestAPI->saveLeaveRequest();
    }

    protected function getAccessibleEmpNumbers(): array
    {
        $properties = ["empNumber"];
        $requiredPermissions = [BasicUserRoleManager::PERMISSION_TYPE_ACTION => ['assign_leave']];
        $employeeList = UserRoleManagerFactory::getUserRoleManager()->getAccessibleEntityProperties(
            'Employee',
            $properties,
            null,
            null,
            [],
            [],
            $requiredPermissions
        );

        return array_keys($employeeList);
    }
}
