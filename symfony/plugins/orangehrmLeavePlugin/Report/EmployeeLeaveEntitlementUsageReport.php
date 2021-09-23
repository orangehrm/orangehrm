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

namespace OrangeHRM\Leave\Report;

use OrangeHRM\Core\Dto\FilterParams;
use OrangeHRM\Core\Report\Header\Column;
use OrangeHRM\Core\Report\Header\Header;
use OrangeHRM\Core\Report\Report;
use OrangeHRM\Leave\Dto\EmployeeLeaveEntitlementUsageReportSearchFilterParams;

class EmployeeLeaveEntitlementUsageReport implements Report
{
    public const PARAMETER_LEAVE_TYPE_NAME = 'leaveTypeName';
    public const PARAMETER_ENTITLEMENT_DAYS = 'entitlementDays';
    public const PARAMETER_PENDING_APPROVAL_DAYS = 'pendingApprovalDays';
    public const PARAMETER_SCHEDULED_DAYS = 'scheduledDays';
    public const PARAMETER_TAKEN_DAYS = 'takenDays';
    public const PARAMETER_BALANCE_DAYS = 'balanceDays';

    /**
     * @return Header
     */
    public function getHeaderDefinition(): Header
    {
        return new Header(
            [
                (new Column(self::PARAMETER_LEAVE_TYPE_NAME))->setName('Leave Type'),
                (new Column(self::PARAMETER_ENTITLEMENT_DAYS))->setName('Leave Entitlements (Days)'),
                (new Column(self::PARAMETER_PENDING_APPROVAL_DAYS))->setName('Leave Pending Approval (Days)'),
                (new Column(self::PARAMETER_SCHEDULED_DAYS))->setName('Leave Scheduled (Days)'),
                (new Column(self::PARAMETER_TAKEN_DAYS))->setName('Leave Taken (Days)'),
                (new Column(self::PARAMETER_BALANCE_DAYS))->setName('Leave Balance (Days)'),
            ]
        );
    }

    public function getFilterDefinition()
    {
        // TODO: Implement getFilterDefinition() method.
    }

    /**
     * @param EmployeeLeaveEntitlementUsageReportSearchFilterParams $filterParams
     */
    public function getData(FilterParams $filterParams): EmployeeLeaveEntitlementUsageReportData
    {
        return new EmployeeLeaveEntitlementUsageReportData($filterParams);
    }

    public function getValidationRule()
    {
        // TODO: Implement getValidationRule() method.
    }
}
