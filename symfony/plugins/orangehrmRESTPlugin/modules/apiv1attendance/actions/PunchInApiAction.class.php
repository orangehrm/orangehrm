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
use Orangehrm\Rest\Api\Attendance\PunchInAPI;
use Orangehrm\Rest\Api\Exception\NotImplementedException;

class PunchInApiAction extends baseRestAction
{

    private $punchInApi = null;


    /**
     * @return PunchInAPI
     */
    public function getPunchInApi($request){
        if(!$this->punchInApi){
            $this->punchInApi = new PunchInAPI($request);
        }
        return $this->punchInApi;
    }

    /**
     * @param $punchInApi
     * @return $this
     */
    public function setPunchInApi($punchInApi){
        $this->punchInApi = $punchInApi;
        return $this;
    }

    /**
     * @param Request $request
     */
    protected function init(Request $request){
        $this->punchInApi = new PunchInAPI($request);
        $this->postValidationRule = $this->punchInApi->getValidationRules();
    }

    /**
     * @param \Orangehrm\Rest\Http\Request $request
     * @return \Orangehrm\Rest\Http\Response
     */
    protected function handleGetRequest(Request $request)
    {
       throw new NotImplementedException();
    }

    /**
     * @param \Orangehrm\Rest\Http\Request $request
     * @return \Orangehrm\Rest\Http\Response
     */
    protected function handlePostRequest(Request $request)
    {
        return $this->getPunchInApi($request)->savePunchIn();
    }
}