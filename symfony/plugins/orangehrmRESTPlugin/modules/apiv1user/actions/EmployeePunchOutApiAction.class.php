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

//namespace Orangehrm\Rest\Api\User;

use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Api\User\EmployeePunchOutAPI;
use Orangehrm\Rest\Api\Exception\NotImplementedException;

class EmployeePunchOutApiAction extends \BaseUserApiAction
{

    private $punchOutApi = null;


    /**
     * @return PunchOutAPI
     */
    public function getPunchOutApi($request)
    {
        if (!$this->punchOutApi) {
            $this->punchOutApi = new EmployeePunchOutAPI($request);
        }
        return $this->punchOutApi;
    }

    /**
     * @param $punchOutApi
     * @return $this
     */
    public function setPunchOutApi($punchOutApi)
    {
        $this->punchOutApi = $punchOutApi;
        return $this;
    }

    /**
     * @param Request $request
     */
    protected function init(Request $request)
    {
        $this->punchOutApi = new EmployeePunchOutAPI($request);
        $this->postValidationRule = $this->punchOutApi->getValidationRules();
    }

    /**
     * @param \Orangehrm\Rest\Http\Request $request
     * @return \Orangehrm\Rest\Http\Response
     */
    protected function handleGetRequest(Request $request)
    {
        $this->setUserToContext();
        return $this->getPunchOutApi($request)->getDetailsForPunchOut();
    }

    /**
     * @param \Orangehrm\Rest\Http\Request $request
     * @return \Orangehrm\Rest\Http\Response
     */
    protected function handlePostRequest(Request $request)
    {
        $this->setUserToContext();
        return $this->getPunchOutApi($request)->savePunchOut();
    }
}
