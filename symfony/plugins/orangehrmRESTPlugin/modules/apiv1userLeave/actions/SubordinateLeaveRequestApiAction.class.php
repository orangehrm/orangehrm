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

use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Api\Exception\NotImplementedException;
use Orangehrm\Rest\Api\Leave\SaveLeaveRequestAPI;
use Orangehrm\Rest\Api\User\Leave\AssignLeaveRequestAPI;
use Orangehrm\Rest\Http\Request;

class SubordinateLeaveRequestApiAction extends BaseUserApiAction
{
    /**
     * @var null|AssignLeaveRequestAPI
     */
    private $assignLeaveRequestAPI = null;

    protected function init(Request $request)
    {
        $this->assignLeaveRequestAPI = new AssignLeaveRequestAPI($request);
        $this->postValidationRule = $this->assignLeaveRequestAPI->getValidationRules();
    }

    protected function handleGetRequest(Request $request)
    {
        throw new NotImplementedException();
    }

    /**
     * @OA\Post(
     *     path="/subordinate/{id}/leave-request",
     *     summary="Save Subordinate Leave Request (Single Day/Multiple Day)",
     *     tags={"Leave","User"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="number"),
     *         description="Subordinate employee id",
     *     ),
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
     */
    protected function handlePostRequest(Request $request)
    {
        $this->setUserToContext();
        $empNumber = $this->assignLeaveRequestAPI->getRequestParams()->getUrlParam(SaveLeaveRequestAPI::PARAMETER_ID);
        if (!in_array($empNumber, $this->getAccessibleEmpNumbers())) {
            throw new BadRequestException('Access Denied');
        }
        return $this->assignLeaveRequestAPI->saveLeaveRequest();
    }

    protected function getAccessibleEmpNumbers(): array
    {
        $properties = ["empNumber"];
        $requiredPermissions = [BasicUserRoleManager::PERMISSION_TYPE_ACTION => ['assign_leave']];
        $employeeList = UserRoleManagerFactory::getUserRoleManager()->getAccessibleEntityProperties(
            'Employee',
            $properties,
            null,
            null,
            [],
            [],
            $requiredPermissions
        );

        return array_keys($employeeList);
    }
}
