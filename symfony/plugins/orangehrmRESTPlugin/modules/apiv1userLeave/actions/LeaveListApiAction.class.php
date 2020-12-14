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
use Orangehrm\Rest\Api\Leave\LeaveRequestAPI;
use Orangehrm\Rest\Http\Request;

class LeaveListApiAction extends BaseUserApiAction
{
    /**
     * @var null|LeaveRequestAPI
     */
    private $leaveRequestApi = null;

    protected function init(Request $request)
    {
        $this->leaveRequestApi = new LeaveRequestAPI($request);
        $this->getValidationRule = $this->leaveRequestApi->getValidationRules();
    }

    /**
     * @OA\Get(
     *     path="/leave/leave-list",
     *     summary="Get Leave List",
     *     tags={"Leave","User"},
     *     @OA\Parameter(
     *         name="fromDate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="From date (default current leave period from date)",
     *     ),
     *     @OA\Parameter(
     *         name="toDate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="To date (default current leave period to date)",
     *     ),
     *     @OA\Parameter(
     *         name="employeeName",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Employee name",
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
     *     @OA\Parameter(
     *         name="pastEmployee",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean"),
     *         description="Past employee results",
     *     ),
     *     @OA\Parameter(
     *         name="subunit",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="number"),
     *         description="Employee subunit id",
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="number"),
     *         description="Page number",
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="number"),
     *         description="Leave record limit",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/LeaveRequests"),
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
     *     schema="LeaveRequests",
     *     type="object",
     *     example={"data":{{"employeeId":"4","employeeName":"KevinMathews","leaveRequestId":"8","fromDate":"2020-07-16","toDate":"2020-07-21","appliedDate":"2020-07-16","LeaveBalance":"10.00","numberOfDays":"3.00","leaveBreakdown":"Scheduled(2.00)","LeaveType":{"type":"Annual","id":"2"}},{"employeeId":"5","employeeName":"LindaJane","leaveRequestId":"3","fromDate":"2020-07-15","toDate":"2020-07-15","appliedDate":"2020-07-15","LeaveBalance":"2.00","numberOfDays":"0.50","leaveBreakdown":"PendingApproval(0.50)","LeaveType":{"type":"Casual","id":"3"}}},"rels":{}}
     * )
     */
    protected function handleGetRequest(Request $request)
    {
        $this->setUserToContext();
        return $this->leaveRequestApi->getLeaveRequests();
    }


    protected function handlePostRequest(Request $request)
    {
        throw new NotImplementedException();
    }
}
