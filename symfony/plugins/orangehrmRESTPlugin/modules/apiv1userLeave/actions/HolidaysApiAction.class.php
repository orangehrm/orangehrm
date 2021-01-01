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
use Orangehrm\Rest\Api\User\Leave\LeaveConfigAPI;
use Orangehrm\Rest\Http\Request;

class HolidaysApiAction extends BaseUserApiAction
{
    /**
     * @var null|LeaveConfigAPI
     */
    protected $leaveConfigApi = null;

    protected function init(Request $request)
    {
        $this->leaveConfigApi = new LeaveConfigAPI($request);
        $this->getValidationRule = $this->leaveConfigApi->getHolidaysValidationRules();
    }

    /**
     * @OA\Get(
     *     path="/leave/holidays",
     *     summary="Get Holidays",
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
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/LeaveHolidays"),
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
     *     schema="LeaveHolidays",
     *     type="object",
     *     example={"data":{{"id":"1","recurring":"0","description":"Holiday1","date":"2020-08-05","length":"4"},{"id":"2","recurring":"1","description":"Holiday2","date":"2020-08-06","length":"8"}},"rels":{}}
     * )
     */
    protected function handleGetRequest(Request $request)
    {
        return $this->leaveConfigApi->getHolidays();
    }

    /**
     * @inheritDoc
     */
    protected function handlePostRequest(Request $request)
    {
        throw new NotImplementedException();
    }
}
