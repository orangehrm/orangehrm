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

use Orangehrm\Rest\Api\User\Leave\EmployeeLeaveRequestAPI;
use Orangehrm\Rest\Api\Leave\LeaveRequestAPI;
use Orangehrm\Rest\Http\Request;

class EmployeeLeaveRequestApiAction extends BaseUserApiAction
{
    /**
     * @var null|LeaveRequestAPI
     */
    private $leaveRequestApi = null;

    /**
     * @var null|EmployeeLeaveRequestAPI
     */
    private $employeeLeaveRequestApi = null;

    protected function init(Request $request)
    {
        $this->leaveRequestApi = new LeaveRequestAPI($request);
        $this->employeeLeaveRequestApi = new EmployeeLeaveRequestAPI($request);
        $this->postValidationRule = $this->employeeLeaveRequestApi->getValidationRules();
    }

    /**
     * @OA\Get(
     *     path="/leave/leave-request/{id}",
     *     summary="Get Leave Request",
     *     tags={"Leave","User"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="number"),
     *         description="Leave request id",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/LeaveRequest"),
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
     *     schema="LeaveRequest",
     *     type="object",
     *     example={"data":{"employeeId":"4","employeeName":"KevinMathews","leaveRequestId":"8","fromDate":"2020-07-16","toDate":"2020-07-21","appliedDate":"2020-07-16","LeaveBalance":"10.00","numberOfDays":"3.00","comments":{"user":"KevinMathews","date":"2020-06-25","time":"17:23:03","comment":"Comment"},"days":{{"date":"2020-07-20","status":"SCHEDULED","duration":"8.00","durationString":"","comments":{}},{"date":"2020-07-19","status":"WEEKEND","duration":"0.00","durationString":"","comments":{}},{"date":"2020-07-18","status":"WEEKEND","duration":"0.00","durationString":"","comments":{}},{"date":"2020-07-17","status":"SCHEDULED","duration":"8.00","durationString":"","comments":{}}},"leaveBreakdown":"Scheduled(2.00)","LeaveType":{"type":"Annual","id":"2"},"allowedActions":{"Cancel"}},"rels":{}}
     * )
     */
    protected function handleGetRequest(Request $request)
    {
        $this->setUserToContext();
        return $this->leaveRequestApi->getLeaveRequestById();
    }

    /**
     * @OA\Post(
     *     path="/leave/leave-request/{id}",
     *     summary="Change Leave Request Status",
     *     tags={"Leave","User"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="number"),
     *         description="Leave request id",
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(ref="#/components/schemas/ChangeLeaveRequestStatusRequestBody"),
     *         required=true
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/SuccessfullySaved"),
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
     *     schema="ChangeLeaveRequestStatusRequestBody",
     *     type="object",
     *     @OA\Property(
     *         property="actionType",
     *         description="Action type on leave request. Note : If actionType is 'changeStatus', status fields must be filled. If actionType is 'comment', comment fields must be filled",
     *         type="string",
     *         enum={"changeStatus","comment"}
     *     ),
     *     @OA\Property(
     *         property="status",
     *         description="Status to be changed",
     *         type="string",
     *         enum={"Approve","Reject","Cancel"}
     *     ),
     *     @OA\Property(
     *         property="comment",
     *         description="Leave comment",
     *         type="string"
     *     ),
     * )
     */
    protected function handlePostRequest(Request $request)
    {
        $this->setUserToContext();
        return $this->employeeLeaveRequestApi->saveLeaveRequestAction();
    }
}
