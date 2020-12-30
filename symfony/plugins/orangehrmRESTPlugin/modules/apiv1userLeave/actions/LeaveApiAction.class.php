<?php
/*
 *
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
 *
 */

use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Api\User\Leave\LeaveAPI;
use Orangehrm\Rest\Api\Exception\NotImplementedException;
use Orangehrm\Rest\Api\Exception\BadRequestException;

class LeaveApiAction extends BaseUserApiAction
{
    /**
     * @var null|LeaveAPI
     */
    private $leaveAPI = null;

    /**
     * @param Request $request
     */
    protected function init(Request $request)
    {
        $this->leaveAPI = new LeaveAPI($request);
        $this->leaveAPI->setRequest($request);
        $this->getValidationRule = $this->leaveAPI->getValidationRules();
    }

    /**
     * @OA\Get(
     *     path="/leave/leaves",
     *     summary="Get Leaves",
     *     tags={"Leave","User"},
     *     @OA\Parameter(
     *         name="fromDate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="From date (Default: Current leave period from date, Format: Y-m-d, e.g. 2020-01-01)",
     *     ),
     *     @OA\Parameter(
     *         name="toDate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="To date (Default: Current leave period to date, Format: Y-m-d, e.g. 2020-12-31)",
     *     ),
     *     @OA\Parameter(
     *         name="empNumber",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="number"),
     *         description="Employee number",
     *     ),
     *     @OA\Parameter(
     *         name="rejected",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean"),
     *         description="Leave status rejected",
     *     ),
     *     @OA\Parameter(
     *         name="cancelled",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean"),
     *         description="Leave status cancelled",
     *     ),
     *     @OA\Parameter(
     *         name="pendingApproval",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean"),
     *         description="Leave status pending approval",
     *     ),
     *     @OA\Parameter(
     *         name="scheduled",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean"),
     *         description="Leave status scheduled",
     *     ),
     *     @OA\Parameter(
     *         name="taken",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean"),
     *         description="Leave status taken",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Leaves"),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No Bound User",
     *         @OA\JsonContent(ref="#/components/schemas/NoBoundUserError"),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No Records Found",
     *         @OA\JsonContent(ref="#/components/schemas/RecordNotFoundException"),
     *     ),
     * )
     * @OA\Schema(
     *     schema="Leaves",
     *     type="object",
     *     example={"data":{{"id":"12","date":"2020-12-15","lengthHours":"4.00","lengthDays":"0.5000","leaveType":{"id":"1","type":"Medical"},"startTime":"09:00:00","endTime":"13:00:00","status":"CANCELLED"},{"id":"11","date":"2020-12-16","lengthHours":"8.00","lengthDays":"1.0000","leaveType":{"id":"3","type":"Anual"},"startTime":"00:00:00","endTime":"00:00:00","status":"PENDINGAPPROVAL"},{"id":"13","date":"2020-12-18","lengthHours":"4.00","lengthDays":"0.5000","leaveType":{"id":"2","type":"Casual"},"startTime":"09:00:00","endTime":"13:00:00","status":"REJECTED"}},"rels":{}}
     * )
     */
    protected function handleGetRequest(Request $request)
    {
        $this->setUserToContext();
        return $this->leaveAPI->getLeaveRecords();
    }
    
    /**
     * @param Request $request
     * @throws NotImplementedException
     */
    protected function handlePostRequest(Request $request)
    {
        throw new NotImplementedException();
    }
}
