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

class WorkWeekApiAction extends BaseUserApiAction
{
    /**
     * @var null|LeaveConfigAPI
     */
    protected $leaveConfigApi = null;

    protected function init(Request $request)
    {
        $this->leaveConfigApi = new LeaveConfigAPI($request);
    }

    /**
     * @OA\Get(
     *     path="/leave/work-week",
     *     summary="Get Work Week",
     *     tags={"Leave","User"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/WorkWeek"),
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
     *     schema="WorkWeek",
     *     type="object",
     *     example={"data":{"mon":"0","tue":"0","wed":"0","thu":"0","fri":"0","sat":"4","sun":"8"},"rels":{}}
     * )
     */
    protected function handleGetRequest(Request $request)
    {
        return $this->leaveConfigApi->getEmployeeWorkWeek();
    }

    /**
     * @inheritDoc
     */
    protected function handlePostRequest(Request $request)
    {
        throw new NotImplementedException();
    }
}
