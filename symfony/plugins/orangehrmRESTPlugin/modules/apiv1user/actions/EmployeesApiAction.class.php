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
use Orangehrm\Rest\Api\User\EmployeesAPI;
use Orangehrm\Rest\Http\Request;

class EmployeesApiAction extends BaseUserApiAction
{
    /**
     * @var null|EmployeesAPI
     */
    private $employeesAPI = null;

    protected function init(Request $request)
    {
        $this->employeesAPI = new EmployeesAPI($request);
    }

    /**
     * @OA\Get(
     *     path="/employees",
     *     summary="Get Accessible Employees",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="actionName",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Action name. e.g. assign_leave, view_leave_list",
     *     ),
     *     @OA\Parameter(
     *         name="properties",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="array",@OA\Items(type="string")),
     *         description="Employee properties array. e.g. employeeId, firstName, lastName, termination_id. /api/v1/employees?properties[]=firstName&properties[]=lastName&properties[]=termination_id",
     *     ),
     *     @OA\Parameter(
     *         name="pastEmployee",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean"),
     *         description="Specify whether with past employee. Default false",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Employees"),
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
     *     schema="Employees",
     *     type="object",
     *     example={"data":{{"empNumber":"4","firstName":"Kevin","lastName":"Mathews","employeeId":"004"},{"empNumber":"5","firstName":"Linda","lastName":"Jane","employeeId":"005"}},"rels":{}}
     * )
     */
    protected function handleGetRequest(Request $request)
    {
        $this->setUserToContext();
        return $this->employeesAPI->getEmployees();
    }

    protected function handlePostRequest(Request $request)
    {
        throw new NotImplementedException();
    }
}
