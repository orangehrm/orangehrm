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

use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Api\Admin\Entity\User;
use Orangehrm\Rest\Api\Mobile\MyLeaveEntitlementAPI;
use Orangehrm\Rest\Api\Admin\UserAPI;
use Orangehrm\Rest\Api\Exception\NotImplementedException;
use Orangehrm\Rest\Api\Exception\BadRequestException;

class MyLeaveEntitlementApiAction extends baseRestAction
{
    /**
     * @var null|MyLeaveEntitlementAPI
     */
    private $myLeaveRequestAPI = null;

    /**
     * @var null|UserAPI
     */
    private $systemUserApi = null;

    protected function init(Request $request)
    {
        $this->systemUserApi = new UserAPI($request);
        $this->myLeaveRequestAPI = new MyLeaveEntitlementAPI($request);
        $this->myLeaveRequestAPI->setRequest($request);
        $this->getValidationRule = $this->myLeaveRequestAPI->getValidationRules();
    }

    protected function handleGetRequest(Request $request)
    {
        $token = $this->getAccessTokenData();
        $user = $this->systemUserApi->getSystemUserById($token['user_id']);
        if ($user instanceof User) {
            $employeeId = $user->getEmployeeId();
            return $this->myLeaveRequestAPI->getMyLeaveDetails($employeeId);
        } else {
            throw  new BadRequestException("No Bound User");
        }
    }

    protected function handlePostRequest(Request $request)
    {
        throw new NotImplementedException();
    }
}
