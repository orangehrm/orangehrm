<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Leave\Api;

use OrangeHRM\Core\Api\Rest\ReportDataAPI;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Report\Api\EndpointAwareReport;

class LeaveReportDataAPI extends ReportDataAPI
{
    /**
     * @OA\Get(
     *     path="/api/v2/leave/reports/data",
     *     tags={"Leave/Leave Report"},
     *     summary="Get Leave Report Data",
     *     operationId="get-leave-report-data",
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             enum={
     *                 "employee_leave_entitlements_and_usage",
     *                 "my_leave_entitlements_and_usage",
     *                 "leave_type_leave_entitlements_and_usage"
     *             }
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="empNumber",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="fromDate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="toDate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="includeEmployees",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=OrangeHRM\Leave\Dto\LeaveRequestSearchFilterParams::INCLUDE_EMPLOYEES)
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/sortOrder"),
     *     @OA\Parameter(ref="#/components/parameters/limit"),
     *     @OA\Parameter(ref="#/components/parameters/offset"),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="leaveTypeName", type="string"),
     *                     @OA\Property(property="entitlementDays", type="string"),
     *                     @OA\Property(property="pendingApprovalDays", type="string"),
     *                     @OA\Property(property="scheduledDays", type="string"),
     *                     @OA\Property(property="takenDays", type="string"),
     *                     @OA\Property(property="balanceDays", type="string"),
     *                     @OA\Property(property="leaveTypeDeleted", type="boolean"),
     *                     @OA\Property(property="_url", type="object",
     *                         @OA\Property(property="entitlementDays", type="string"),
     *                         @OA\Property(property="pendingApprovalDays", type="string"),
     *                         @OA\Property(property="scheduledDays", type="string"),
     *                         @OA\Property(property="takenDays", type="string")
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(
     *                     property="employee",
     *                     type="object",
     *                     @OA\Property(property="empNumber", type="integer"),
     *                     @OA\Property(property="firstName", type="string"),
     *                     @OA\Property(property="lastName", type="string"),
     *                     @OA\Property(property="middleName", type="string"),
     *                     @OA\Property(property="employeeId", type="string"),
     *                     @OA\Property(property="terminationId", type="integer"),
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad Request - Invalid report name",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="object",
     *                 @OA\Property(property="status", type="string", default="400"),
     *                 @OA\Property(property="message", type="string", default="Invalid report name")
     *             )
     *         )
     *     ),
     * )
     *
     * @return EndpointAwareReport
     * @throws BadRequestException
     */
    protected function getReport(): EndpointAwareReport
    {
        $reportName = $this->getReportName();
        if (!isset(LeaveReportAPI::LEAVE_REPORT_MAP[$reportName])) {
            throw $this->getBadRequestException('Invalid report name');
        }
        $reportClass = LeaveReportAPI::LEAVE_REPORT_MAP[$reportName];
        return new $reportClass();
    }
}
