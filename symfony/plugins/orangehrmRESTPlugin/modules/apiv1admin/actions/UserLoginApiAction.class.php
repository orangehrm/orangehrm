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
use Orangehrm\Rest\Api\Admin\UserLoginAPI;
use Orangehrm\Rest\Api\Exception\NotImplementedException;


class UserLoginApiAction extends baseRestAction
{
    private $userLoginApi = null;

    protected function init(Request $request)
    {
        $this->userLoginApi= new UserLoginAPI($request);
        $this->postValidationRule = $this->userLoginApi->postValidationRules();
    }

    protected function handlePostRequest(Request $request)
    {
        return $this->userLoginApi->userLogin();
    }

    protected function handleGetRequest(Request $request)
    {
        throw new NotImplementedException();
    }

}
