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
use Orangehrm\Rest\Api\User\Attendance\AttendanceAPI;
use Orangehrm\Rest\Api\Exception\NotImplementedException;
use Orangehrm\Rest\Api\Exception\BadRequestException;

class AttendanceApiAction extends BaseUserApiAction
{

    /**
     * @var null|AttendanceAPI
     */
    private $attendanceAPI = null;

    /**
     * @param Request $request
     * @return AttendanceAPI
     */
    public function getAttendanceApi(Request $request)
    {
        if (is_null($this->attendanceAPI)) {
            $this->attendanceAPI = new AttendanceAPI($request);
        }
        return $this->attendanceAPI;
    }

    /**
     * @param AttendanceAPI $attendanceAPI
     */
    public function setAttendanceApi(AttendanceAPI $attendanceAPI)
    {
        $this->attendanceAPI = $attendanceAPI;
    }

    /**
     * @param Request $request
     */
    protected function init(Request $request)
    {
        $this->attendanceAPI = new AttendanceAPI($request);
        $this->attendanceAPI->setRequest($request);
        $this->getValidationRule = $this->attendanceAPI->getValidationRules();
    }

    /**
     * @OA\Get(
     *     path="/attendance/records",
     *     summary="Get Attendance Records",
     *     tags={"Attendance","User"},
     *     @OA\Parameter(
     *         name="fromDate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="From date (Default: Timesheet Period start date, Format: Y-m-d H:i:s, e.g. 2020-05-20 00:00:00)",
     *     ),
     *     @OA\Parameter(
     *         name="toDate",
     *         in="query",
     *         required=false,
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
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/AttendanceRecords"),
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
     *     schema="AttendanceRecords",
     *     type="object",
     *     example={"data":{{"id":"94","punchInUtcTime":"2020-12-1703:00:00","punchInNote":null,"punchInTimeOffset":"5.5","punchInUserTime":"2020-12-1708:30:00","punchOutUtcTime":"2020-12-1703:31:00","punchOutNote":null,"punchOutTimeOffset":"5.0","punchOutUserTime":"2020-12-1708:31:00","state":"PUNCHEDOUT"},{"id":"95","punchInUtcTime":"2020-12-1704:00:00","punchInNote":null,"punchInTimeOffset":"5.5","punchInUserTime":"2020-12-1709:30:00","punchOutUtcTime":"2020-12-1704:01:00","punchOutNote":null,"punchOutTimeOffset":"5.5","punchOutUserTime":"2020-12-1709:31:00","state":"PUNCHEDOUT"},{"id":"93","punchInUtcTime":"2020-12-1704:33:00","punchInNote":"","punchInTimeOffset":"5.5","punchInUserTime":"2020-12-1710:03:00","punchOutUtcTime":"2020-12-1705:00:00","punchOutNote":null,"punchOutTimeOffset":"5.5","punchOutUserTime":"2020-12-1710:30:00","state":"PUNCHEDOUT"}},"rels":{}}
     * )
     */
    protected function handleGetRequest(Request $request)
    {
        $this->setUserToContext();
        return $this->attendanceAPI->getAttendanceRecords();
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
