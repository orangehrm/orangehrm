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
use Orangehrm\Rest\Api\Mobile\MyLeaveRequestAPI;
use Orangehrm\Rest\Api\Leave\SaveLeaveRequestAPI;

class MyLeaveRequestApiAction extends BaseMobileApiAction
{
    /**
     * @var null|MyLeaveRequestAPI
     */
    private $myLeaveRequestAPI = null;

    /**
     * @var null|SaveLeaveRequestAPI
     */
    private $saveLeaveRequestApi = null;

    protected function init(Request $request)
    {
        $systemUser = $this->getSystemUser();
        $this->myLeaveRequestAPI = new MyLeaveRequestAPI($request);
        $this->myLeaveRequestAPI->setRequest($request);
        $this->saveLeaveRequestApi = new SaveLeaveRequestAPI($request);
        $this->saveLeaveRequestApi->getRequestParams()->setParam(
            SaveLeaveRequestAPI::PARAMETER_ID, $systemUser->getEmpNumber());
        $this->saveLeaveRequestApi->getRequestParams()->setPostParam(
            SaveLeaveRequestAPI::PARAMETER_LEAVE_ACTION, "PENDING");
        $this->postValidationRule = $this->saveLeaveRequestApi->getValidationRules();
        $this->getValidationRule = $this->myLeaveRequestAPI->getValidationRules();
    }

    protected function handleGetRequest(Request $request)
    {
        $systemUser = $this->getSystemUser();
        return $this->myLeaveRequestAPI->getMyLeaveDetails($systemUser->getEmpNumber());
    }

    protected function handlePostRequest(Request $request)
    {
        return $this->saveLeaveRequestApi->saveLeaveRequest();
    }
}
