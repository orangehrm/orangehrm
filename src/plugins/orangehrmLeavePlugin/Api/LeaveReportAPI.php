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

namespace OrangeHRM\Leave\Api;

use OrangeHRM\Core\Api\Rest\ReportAPI;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Report\Api\EndpointAwareReport;
use OrangeHRM\Leave\Report\EmployeeLeaveEntitlementUsageReport;
use OrangeHRM\Leave\Report\LeaveTypeLeaveEntitlementUsageReport;

class LeaveReportAPI extends ReportAPI
{
    public const LEAVE_REPORT_MAP = [
        'employee_leave_entitlements_and_usage' => EmployeeLeaveEntitlementUsageReport::class,
        'my_leave_entitlements_and_usage' => EmployeeLeaveEntitlementUsageReport::class,
        'leave_type_leave_entitlements_and_usage' => LeaveTypeLeaveEntitlementUsageReport::class,
    ];

    /**
     * @OA\Get(
     *     path="/api/v2/leave/reports",
     *     tags={"Leave/Leave Report"},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", enum={OrangeHRM\Leave\Api\LeaveReportAPI::LEAVE_REPORT_MAP})
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(
     *                         property="headers",
     *                         type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="name", type="string"),
     *                             @OA\Property(property="prop", type="string"),
     *                             @OA\Property(property="size", type="integer"),
     *                             @OA\Property(property="pin", type="string", default="null"),
     *                             @OA\Property(
     *                                 property="cellProperties",
     *                                 type="object",
     *                                 @OA\Property(
     *                                     property="class",
     *                                     type="object",
     *                                     @OA\Property(property="cell-action", type="boolean")
     *                                 )
     *                             )
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="headers", type="string", default="null"),
     *                 @OA\Property(property="filters", type="string", default="null")
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
