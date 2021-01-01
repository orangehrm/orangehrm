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

class LeaveTypesApiAction extends BaseUserApiAction
{
    /**
     * @var null|LeaveConfigAPI
     */
    protected $leaveConfigApi = null;

    protected function init(Request $request)
    {
        $this->setUserToContext();
        $this->leaveConfigApi = new LeaveConfigAPI($request);
    }

    /**
     * @OA\Get(
     *     path="/leave/leave-types",
     *     summary="Get Leave Types",
     *     tags={"Leave","User"},
     *     @OA\Parameter(
     *         name="all",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean"),
     *         description="Get all leave types. Only allowed to perform supervisors and admins",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/LeaveTypes"),
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
     *     schema="LeaveTypes",
     *     type="object",
     *     example={"data":{{"id":"1","type":"Casual","deleted":"0","situational":false},{"id":"2","type":"Medical","deleted":"0","situational":false}},"rels":{}}
     * )
     */
    protected function handleGetRequest(Request $request)
    {
        return $this->leaveConfigApi->getLeaveTypes();
    }

    /**
     * @inheritDoc
     */
    protected function handlePostRequest(Request $request)
    {
        throw new NotImplementedException();
    }
}
