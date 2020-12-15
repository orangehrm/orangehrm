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

use Orangehrm\Rest\Api\Leave\SaveLeaveRequestAPI;
use Orangehrm\Rest\Api\User\Leave\ApplyLeaveRequestAPI;
use Orangehrm\Rest\Api\User\Leave\MyLeaveRequestAPI;
use Orangehrm\Rest\Http\Request;

class MyLeaveRequestApiAction extends BaseUserApiAction
{
    /**
     * @var null|MyLeaveRequestAPI
     */
    private $myLeaveRequestAPI = null;

    /**
     * @var null|ApplyLeaveRequestAPI
     */
    private $applyLeaveRequestAPI = null;

    protected function init(Request $request)
    {
        $this->myLeaveRequestAPI = new MyLeaveRequestAPI($request);
        $this->myLeaveRequestAPI->setRequest($request);
        $this->applyLeaveRequestAPI = new ApplyLeaveRequestAPI($request);
        $this->postValidationRule = $this->applyLeaveRequestAPI->getValidationRules();
        $this->getValidationRule = $this->myLeaveRequestAPI->getValidationRules();
    }

    /**
     * @OA\Get(
     *     path="/leave/my-leave-request",
     *     summary="Get My Leave Requests",
     *     tags={"Leave","User"},
     *     @OA\Parameter(
     *         name="fromDate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="From date",
     *     ),
     *     @OA\Parameter(
     *         name="toDate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="To date",
     *     ),
     *     @OA\Parameter(
     *         name="leaveTypeId",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="number"),
     *         description="Leave type id",
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
     *         @OA\JsonContent(ref="#/components/schemas/MyLeaveRequest"),
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
     *     schema="MyLeaveRequest",
     *     type="object",
     *     example={"data":{{"id":"8","fromDate":"2020-07-16","toDate":"2020-07-21","appliedDate":"2020-07-16","leaveType":{"type":"Annual","id":"2"},"leaveBalance":"10.00","numberOfDays":"3.00","comments":{"user":"EmployeeName","date":"2020-06-25","time":"17:23:03","comment":"Comment"},"days":{{"date":"2020-07-20","status":"SCHEDULED","duration":"8.00","durationString":"","comments":{}},{"date":"2020-07-19","status":"WEEKEND","duration":"0.00","durationString":"","comments":{}},{"date":"2020-07-18","status":"WEEKEND","duration":"0.00","durationString":"","comments":{}},{"date":"2020-07-17","status":"SCHEDULED","duration":"8.00","durationString":"","comments":{}}},"leaveBreakdown":"Scheduled(2.00)"},{"id":"3","fromDate":"2020-07-15","toDate":"2020-07-15","appliedDate":"2020-07-15","leaveType":{"type":"Casual","id":"1"},"leaveBalance":"3.00","numberOfDays":"0.50","comments":{},"days":{{"date":"2020-07-15","status":"PENDINGAPPROVAL","duration":"4.00","durationString":"(09:00-13:00)","comments":{}}},"leaveBreakdown":"PendingApproval(0.50)"}},"rels":{}}
     * )
     */
    protected function handleGetRequest(Request $request)
    {
        $systemUser = $this->getSystemUser();
        return $this->myLeaveRequestAPI->getMyLeaveDetails($systemUser->getEmpNumber());
    }

    /**
     * @OA\Post(
     *     path="/leave/my-leave-request",
     *     summary="Save My Leave Request (Single Day/Multiple Day)",
     *     tags={"Leave","User"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             oneOf={@OA\Schema(ref="#/components/schemas/LeaveRequestSingleDayRequestBody"),
     *                 @OA\Schema(ref="#/components/schemas/LeaveRequestMultipleDayRequestBody")}
     *         )
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
     *     schema="LeaveRequestSingleDayRequestBody",
     *     type="object",
     *     @OA\Property(
     *         property="type",
     *         description="Mandatory leave type id",
     *         type="number"
     *     ),
     *     @OA\Property(
     *         property="fromDate",
     *         description="Leave start date",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="toDate",
     *         description="Leave end date",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="comment",
     *         description="Leave comment",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="singleType",
     *         description="Single day leave applying type",
     *         type="string",
     *         enum={"half_day","full_day","specify_time"}
     *     ),
     *     @OA\Property(
     *         property="singleAMPM",
     *         description="Half day morning or evening, (required for 'half_day')",
     *         type="string",
     *         enum={"AM","PM"}
     *     ),
     *     @OA\Property(
     *         property="singleFromTime",
     *         description="Single day from time for specify time (required if specifying time)",
     *         type="string",
     *     ),
     *     @OA\Property(
     *         property="singleToTime",
     *         description="Single day to time for specify time (required if specifying time)",
     *         type="string"
     *     ),
     * )
     * @OA\Schema(
     *     schema="LeaveRequestMultipleDayRequestBody",
     *     type="object",
     *     @OA\Property(
     *         property="type",
     *         description="Mandatory leave type id",
     *         type="number"
     *     ),
     *     @OA\Property(
     *         property="fromDate",
     *         description="Leave start date",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="toDate",
     *         description="Leave end date",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="comment",
     *         description="Leave comment",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="partialOption",
     *         description="Partial day option Note : If partial option is 'all'  start day fields must be filled. If partial option is 'end'  end day fields must be filed. If partial option is 'start' start day fields must be filed. If partial option is 'start_end'  start and end day fields must be filed. If partial option is 'none'  No partial option.",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="startDayType",
     *         description="Start day leave applying type",
     *         type="string",
     *         enum={"half_day","full_day","specify_time"}
     *     ),
     *     @OA\Property(
     *         property="startDayAMPM",
     *         description="Half day morning or evening, (required for 'half_day')",
     *         type="string",
     *         enum={"AM","PM"}
     *     ),
     *     @OA\Property(
     *         property="startDayFromTime",
     *         description="Start day from time for specify time (required if specifying time)",
     *         type="string",
     *     ),
     *     @OA\Property(
     *         property="startDayToTime",
     *         description="Start day to time for specify time (required if specifying time)",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="endDayType",
     *         description="End day leave applying type",
     *         type="string",
     *         enum={"half_day","full_day","specify_time"}
     *     ),
     *     @OA\Property(
     *         property="endDayAMPM",
     *         description="Half day morning or evening, (required for 'half_day')",
     *         type="string",
     *         enum={"AM","PM"}
     *     ),
     *     @OA\Property(
     *         property="endDayFromTime",
     *         description="End day from time for specify time (required if specifying time)",
     *         type="string",
     *     ),
     *     @OA\Property(
     *         property="endDayToTime",
     *         description="End day to time for specify time (required if specifying time)",
     *         type="string"
     *     ),
     * )
     * @OA\Schema(
     *     schema="SuccessfullySaved",
     *     type="object",
     *     example={"success":"Successfully Saved"}
     * )
     */
    protected function handlePostRequest(Request $request)
    {
        $this->setUserToContext();
        $systemUser = $this->getSystemUser();
        $this->applyLeaveRequestAPI->getRequestParams()->setParam(
            SaveLeaveRequestAPI::PARAMETER_ID,
            $systemUser->getEmpNumber()
        );
        return $this->applyLeaveRequestAPI->saveLeaveRequest();
    }
}
