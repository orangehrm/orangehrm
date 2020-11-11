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
use Orangehrm\Rest\Api\User\EmployeePunchStatusAPI;
use Orangehrm\Rest\Api\Exception\NotImplementedException;

class EmployeePunchStatusApiAction extends \BaseUserApiAction
{
    private $punchStatusApi = null;

    /**
     * @return EmployeePunchStatusAPI
     */
    public function getPunchStatusApi($request)
    {
        if (!$this->punchStatusApi) {
            $this->punchStatusApi = new EmployeePunchStatusAPI($request);
        }
        return $this->punchStatusApi;
    }

    /**
     * @param $punchStatusApi
     * @return $this
     */
    public function setPunchStatusApi($punchStatusApi)
    {
        $this->punchStatusApi = $punchStatusApi;
        return $this;
    }

    /**
     * @param Request $request
     */
    protected function init(Request $request)
    {
        $this->punchStatusApi = new EmployeePunchStatusAPI($request);
        $this->punchStatusApi->setRequest($request);
    }

    /**
     * @param \Orangehrm\Rest\Http\Request $request
     * @return \Orangehrm\Rest\Http\Response
     */
    protected function handleGetRequest(Request $request)
    {
        $this->setUserToContext();
        return $this->getPunchStatusApi($request)->getStatusDetails();
    }

    /**
     * @param Request $request
     * @return \Orangehrm\Rest\Http\Response|void
     * @throws NotImplementedException
     */
    protected function handlePostRequest(Request $request)
    {
        throw new NotImplementedException();
    }
}
