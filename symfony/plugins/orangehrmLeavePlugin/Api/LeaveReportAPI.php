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
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Report\Api\EndpointAwareReport;
use OrangeHRM\Leave\Report\EmployeeLeaveEntitlementUsageReport;

class LeaveReportAPI extends ReportAPI
{
    public const LEAVE_REPORT_MAP = [
        'leave_entitlements_and_usage' => EmployeeLeaveEntitlementUsageReport::class
    ];

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $report = $this->getReport();
        $header = $report->getHeaderDefinition();

        return new EndpointResourceResult(
            ArrayModel::class,
            $header->normalize(),
            $header->getMeta()
        );
    }

    /**
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

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection($this->getReportNameParamRule());
    }
}
