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
use Orangehrm\Rest\Api\User\Attendance\EmployeePunchStatusAPI;
use Orangehrm\Rest\Api\Exception\NotImplementedException;

class EmployeePunchStatusApiAction extends BaseUserApiAction
{
    private $punchStatusApi = null;

    /**
     * @return EmployeePunchStatusAPI
     */
    public function getPunchStatusApi(Request $request)
    {
        if (!$this->punchStatusApi) {
            $this->punchStatusApi = new EmployeePunchStatusAPI($request);
        }
        return $this->punchStatusApi;
    }

    /**
     * @param $punchStatusApi
     */
    public function setPunchStatusApi(EmployeePunchStatusAPI $punchStatusApi)
    {
        $this->punchStatusApi = $punchStatusApi;
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
     * @OA\Get(
     *     path="/attendance/punch-status",
     *     summary="Get Employee Punch Status",
     *     tags={"Attendance","User"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/EmployeePunchStatus"),
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
     *     schema="EmployeePunchStatus",
     *     type="object",
     *     example={"data": {"punchTime": "2021-01-31 19:31:00","punchNote": "PUNCH IN NOTE","punchTimeZoneOffset": 5.5,"dateTimeEditable": true,"currentUtcDateTime": "2020-11-12 05:25","punchState": "PUNCHED IN"},"rels": {}}
     * )
     */
    protected function handleGetRequest(Request $request)
    {
        $this->setUserToContext();
        return $this->getPunchStatusApi($request)->getStatusDetailsOfLatestAttendanceRecord();
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
