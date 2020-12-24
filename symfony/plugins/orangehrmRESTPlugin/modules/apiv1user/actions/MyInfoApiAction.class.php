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


use Orangehrm\Rest\Api\Exception\NotImplementedException;
use Orangehrm\Rest\Api\User\MyInfoAPI;
use Orangehrm\Rest\Http\Request;

class MyInfoApiAction extends BaseUserApiAction
{
    /**
     * @var null|MyInfoAPI
     */
    private $apiMyInfo = null;

    protected function init(Request $request)
    {
        $this->apiMyInfo = new MyInfoAPI($request);
    }

    /**
     * @OA\Get(
     *     path="/myinfo",
     *     summary="Employee Info",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="withPhoto",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean"),
     *         description="Specify whether with employee photo",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/MyInfo"),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No Bound User",
     *         @OA\JsonContent(ref="#/components/schemas/NoBoundUserError"),
     *     ),
     * )
     * @OA\Schema(
     *     schema="MyInfo",
     *     type="object",
     *     description="`employee` can be null as of 1.2.0",
     *     example={"data":{"employee":{"firstName":"Nina","middleName":"Jane","lastName":"Lewis","code":"0014","id":"1","fullName":"NinaJaneLewis","status":"Active","dob":"2016-05-04","driversLicenseNumber":"444555124223","licenseExpiryDate":"2017-02-09","maritalStatus":"Married","gender":"2","otherId":"4646522","nationality":"Armenian","unit":"MarketingUnit","jobTitle":"marketing"},"employeePhoto":"iVBORw0KGgoAAAANSUhEUgAAABwAAAASCAYAAABIB77kAAAAAXNSR0IArs4c6QAAAAZiS0dEAP8A/wD/oL2nkwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAAd0SU1FB9wMCgQvOUl1YygAAAJJSURBVDjLvZUxayJRFIW","user":{"userName":"Nina","userRole":"ESS","isSupervisor":true,"isProjectAdmin":false,"isManager":false,"isDirector":false,"isAcceptor":false,"isOfferer":false,"isHiringManager":false,"isInterviewer":false}},"rels":{}}
     * )
     * @OA\Schema(
     *     schema="NoBoundUserError",
     *     type="object",
     *     example={"error":{"No Bound User"}}
     * )
     */
    protected function handleGetRequest(Request $request)
    {
        $this->setUserToContext();
        return $this->apiMyInfo->getMyInfo();
    }

    /**
     * @inheritDoc
     */
    protected function handlePostRequest(Request $request)
    {
        throw new NotImplementedException();
    }
}
