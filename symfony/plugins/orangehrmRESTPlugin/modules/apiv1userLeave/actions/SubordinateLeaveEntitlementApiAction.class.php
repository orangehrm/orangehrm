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
use Orangehrm\Rest\Api\User\Leave\SubordinateLeaveEntitlementAPI;
use Orangehrm\Rest\Api\Exception\NotImplementedException;

class SubordinateLeaveEntitlementApiAction extends BaseUserApiAction
{
    /**
     * @var null|SubordinateLeaveEntitlementAPI
     */
    private $subordinateLeaveEntitlementAPI = null;

    protected function init(Request $request)
    {
        $this->subordinateLeaveEntitlementAPI = new SubordinateLeaveEntitlementAPI($request);
        $this->subordinateLeaveEntitlementAPI->setRequest($request);
        $this->getValidationRule = $this->subordinateLeaveEntitlementAPI->getValidationRules();
    }

    /**
     * @OA\Get(
     *     path="/subordinate/{id}/leave-entitlement",
     *     summary="Get Subordinate Leave Entitlements",
     *     tags={"Leave","User"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="number"),
     *         description="Subordinate employee id",
     *     ),
     *     @OA\Parameter(
     *         name="fromDate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Valid leave period from date",
     *     ),
     *     @OA\Parameter(
     *         name="toDate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Valid leave period to date",
     *     ),
     *     @OA\Parameter(
     *         name="deletedLeaveTypes",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean"),
     *         description="With deleted leave types",
     *     ),
     *     @OA\Parameter(
     *         name="combineLeaveTypes",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean"),
     *         description="Whether combine, not entitled leave types with leave balance.",
     *     ),
     *     @OA\Parameter(
     *         name="balanceAsAtDate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Start date for calculate balance. Default: current date.",
     *     ),
     *     @OA\Parameter(
     *         name="balanceEndDate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="End date for calculate balance. Default: end date of current leave period",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/LeaveEntitlements"),
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
    protected function handleGetRequest(Request $request)
    {
        $this->setUserToContext();
        return $this->subordinateLeaveEntitlementAPI->getSubordinateLeaveEntitlement();
    }

    protected function handlePostRequest(Request $request)
    {
        throw new NotImplementedException();
    }
}
