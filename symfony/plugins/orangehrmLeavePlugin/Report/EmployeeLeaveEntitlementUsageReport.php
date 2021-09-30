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

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Dto\FilterParams;
use OrangeHRM\Core\Report\Api\EndpointAwareReport;
use OrangeHRM\Core\Report\Api\EndpointProxy;
use OrangeHRM\Core\Report\Filter\Filter;
use OrangeHRM\Core\Report\Header\Column;
use OrangeHRM\Core\Report\Header\Header;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Leave\Api\LeaveCommonParams;
use OrangeHRM\Leave\Dto\EmployeeLeaveEntitlementUsageReportSearchFilterParams;
use OrangeHRM\Leave\Traits\Service\LeavePeriodServiceTrait;

class EmployeeLeaveEntitlementUsageReport implements EndpointAwareReport
{
    use AuthUserTrait;
    use LeavePeriodServiceTrait;

    public const PARAMETER_LEAVE_TYPE_NAME = 'leaveTypeName';
    public const PARAMETER_ENTITLEMENT_DAYS = 'entitlementDays';
    public const PARAMETER_PENDING_APPROVAL_DAYS = 'pendingApprovalDays';
    public const PARAMETER_SCHEDULED_DAYS = 'scheduledDays';
    public const PARAMETER_TAKEN_DAYS = 'takenDays';
    public const PARAMETER_BALANCE_DAYS = 'balanceDays';

    public const DEFAULT_COLUMN_SIZE = 150;

    /**
     * @return Header
     */
    public function getHeaderDefinition(): Header
    {
        return new Header(
            [
                (new Column(self::PARAMETER_LEAVE_TYPE_NAME))->setName('Leave Type')
                    ->setPin(Column::PIN_COL_START)
                    ->setSize(self::DEFAULT_COLUMN_SIZE),
                (new Column(self::PARAMETER_ENTITLEMENT_DAYS))->setName('Leave Entitlements (Days)')
                    ->setCellProperties(['class' => ['col-alt' => true]])
                    ->setSize(self::DEFAULT_COLUMN_SIZE),
                (new Column(self::PARAMETER_PENDING_APPROVAL_DAYS))->setName('Leave Pending Approval (Days)')
                    ->setSize(self::DEFAULT_COLUMN_SIZE),
                (new Column(self::PARAMETER_SCHEDULED_DAYS))->setName('Leave Scheduled (Days)')
                    ->setSize(self::DEFAULT_COLUMN_SIZE),
                (new Column(self::PARAMETER_TAKEN_DAYS))->setName('Leave Taken (Days)')
                    ->setSize(self::DEFAULT_COLUMN_SIZE),
                (new Column(self::PARAMETER_BALANCE_DAYS))->setName('Leave Balance (Days)')
                    ->setCellProperties(['class' => ['col-alt' => true]])
                    ->setSize(self::DEFAULT_COLUMN_SIZE),
            ]
        );
    }

    /**
     * @return Filter
     */
    public function getFilterDefinition(): Filter
    {
        return new Filter();
    }

    /**
     * @param EmployeeLeaveEntitlementUsageReportSearchFilterParams $filterParams
     * @return EmployeeLeaveEntitlementUsageReportData
     */
    public function getData(FilterParams $filterParams): EmployeeLeaveEntitlementUsageReportData
    {
        return new EmployeeLeaveEntitlementUsageReportData($filterParams);
    }

    /**
     * @inheritDoc
     */
    public function prepareFilterParams(EndpointProxy $endpoint): FilterParams
    {
        $filterParams = new EmployeeLeaveEntitlementUsageReportSearchFilterParams();
        $filterParams->setEmpNumber(
            $endpoint->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_QUERY,
                CommonParams::PARAMETER_EMP_NUMBER,
                $this->getAuthUser()->getEmpNumber()
            )
        );
        $endpoint->setSortingAndPaginationParams($filterParams);
        $leavePeriod = $this->getLeavePeriodService()->getCurrentLeavePeriod();
        $filterParams->setFromDate(
            $endpoint->getRequestParams()->getDateTime(
                RequestParams::PARAM_TYPE_QUERY,
                LeaveCommonParams::PARAMETER_FROM_DATE,
                null,
                $leavePeriod->getStartDate()
            )
        );
        $filterParams->setToDate(
            $endpoint->getRequestParams()->getDateTime(
                RequestParams::PARAM_TYPE_QUERY,
                LeaveCommonParams::PARAMETER_TO_DATE,
                null,
                $leavePeriod->getEndDate()
            )
        );
        return $filterParams;
    }

    /**
     * @inheritDoc
     */
    public function getValidationRule(EndpointProxy $endpoint): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $endpoint->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(CommonParams::PARAMETER_EMP_NUMBER, new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS))
            ),
            $endpoint->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(LeaveCommonParams::PARAMETER_FROM_DATE, new Rule(Rules::API_DATE))
            ),
            $endpoint->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(LeaveCommonParams::PARAMETER_TO_DATE, new Rule(Rules::API_DATE))
            ),
            ...
            $endpoint->getSortingAndPaginationParamsRules(
                EmployeeLeaveEntitlementUsageReportSearchFilterParams::ALLOWED_SORT_FIELDS
            )
        );
    }
}
