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
use Orangehrm\Rest\Api\User\Attendance\AttendanceSummaryAPI;
use Orangehrm\Rest\Api\Exception\NotImplementedException;
use Orangehrm\Rest\Api\Exception\BadRequestException;

class AttendanceSummaryApiAction extends BaseUserApiAction
{
    /**
     * @var null | AttendanceSummaryAPI
     */
    private $summaryAPI = null;

    /**
     * @param Request $request
     */
    protected function init(Request $request)
    {
        $this->summaryAPI = new AttendanceSummaryAPI($request);
        $this->summaryAPI->setRequest($request);
        $this->getValidationRule = $this->summaryAPI->getValidationRules();
    }

    /**
     * @OA\Get(
     *     path="/attendance/summary",
     *     summary="Get Attendance Summary",
     *     tags={"Attendance","User"},
     *     @OA\Parameter(
     *         name="fromDate",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="From date (Default: Timesheet Period start date, Format: Y-m-d H:i:s, e.g. 2020-05-20 00:00:00)",
     *     ),
     *     @OA\Parameter(
     *         name="toDate",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="To date (Default: Timesheet Period end date, Format: Y-m-d H:i:s, e.g. 2020-05-26 23:59:59)",
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
     *         @OA\JsonContent(ref="#/components/schemas/AttendanceSummary"),
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
     *     schema="AttendanceSummary",
     *     type="object",
     *     example={"data":{"totalWorkHours":"7.98","totalLeaveHours":"16.00","totalLeaveTypeHours":{{"typeId":"1","type":"Medical","hours":"4.00"},{"typeId":"3","type":"Anual","hours":"8.00"},{"typeId":"2","type":"Casual","hours":"4.00"}},"workSummary":{"sunday":{"workHours":0,"leave":{}},"monday":{"workHours":"7.00","leave":{}},"tuesday":{"workHours":0,"leave":{{"typeId":"1","type":"Medical","hours":"4.00"}}},"wednesday":{"workHours":0,"leave":{{"typeId":"3","type":"Anual","hours":"8.00"}}},"thursday":{"workHours":"0.98","leave":{}},"friday":{"workHours":0,"leave":{{"typeId":"2","type":"Casual","hours":"4.00"}}},"saturday":{"workHours":0,"leave":{}}}},"rels":{}}
     * )
     */
    protected function handleGetRequest(Request $request)
    {
        $this->setUserToContext();
        return $this->summaryAPI->getAttendanceSummary();
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
