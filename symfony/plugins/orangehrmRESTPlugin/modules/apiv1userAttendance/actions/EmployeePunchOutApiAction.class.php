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
use Orangehrm\Rest\Api\User\Attendance\EmployeePunchOutAPI;
use Orangehrm\Rest\Api\Exception\NotImplementedException;

class EmployeePunchOutApiAction extends \BaseUserApiAction
{

    private $punchOutApi = null;


    /**
     * @param Request $request
     * @return EmployeePunchOutAPI
     */
    public function getPunchOutApi(Request $request)
    {
        if (!$this->punchOutApi) {
            $this->punchOutApi = new EmployeePunchOutAPI($request);
        }
        return $this->punchOutApi;
    }

    /**
     * @param EmployeePunchOutAPI $punchOutApi
     */
    public function setPunchOutApi(EmployeePunchOutAPI $punchOutApi)
    {
        $this->punchOutApi = $punchOutApi;
    }

    /**
     * @param Request $request
     */
    protected function init(Request $request)
    {
        $this->punchOutApi = new EmployeePunchOutAPI($request);
        $this->postValidationRule = $this->punchOutApi->getValidationRules();
    }

    /**
     * @param Request $request
     * @return \Orangehrm\Rest\Http\Response|void
     * @throws NotImplementedException
     */
    protected function handleGetRequest(Request $request)
    {
        throw new NotImplementedException();
    }

    /**
     * @OA\Post(
     *     path="/attendance/punch-out",
     *     summary="Save Employee Punch Out",
     *     tags={"Attendance","User"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(ref="#/components/schemas/EmployeePunchOutRequestBody")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/EmployeePunchOut"),
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
     *     schema="EmployeePunchOutRequestBody",
     *     type="object",
     *     @OA\Property(
     *         property="timezoneOffset",
     *         description="Time Zone Offset (ex: 5.5)",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="datetime",
     *         description="Date and Time Required If Current Time Editable (ex: '2020-12-28 08:30')",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="note",
     *         description="Punch Out Note",
     *         type="string"
     *     ),
     * )
     * @OA\Schema(
     *     schema="EmployeePunchOut",
     *     type="object",
     *     example={"data": {"id": "1","punchInDateTime":"2020-12-28 08:30","punchInTimeZone":5.5,"punchInNote":"PUNCH IN NOTE","punchOutDateTime":"2020-12-28 18:30","punchOutTimeZone":5.5,"punchOutNote":"PUNCH OUT NOTE"},"rels": {}}
     * )
     */
    protected function handlePostRequest(Request $request)
    {
        $this->setUserToContext();
        return $this->getPunchOutApi($request)->savePunchOut();
    }
}
