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

namespace OrangeHRM\Leave\Report;

use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
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
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\JobTitle;
use OrangeHRM\Entity\LeaveType;
use OrangeHRM\Entity\Location;
use OrangeHRM\Entity\Subunit;
use OrangeHRM\I18N\Traits\Service\I18NHelperTrait;
use OrangeHRM\Leave\Api\LeaveCommonParams;
use OrangeHRM\Leave\Dto\LeaveTypeLeaveEntitlementUsageReportSearchFilterParams;
use OrangeHRM\Leave\Traits\Service\LeavePeriodServiceTrait;

class LeaveTypeLeaveEntitlementUsageReport implements EndpointAwareReport
{
    use AuthUserTrait;
    use UserRoleManagerTrait;
    use LeavePeriodServiceTrait;
    use I18NHelperTrait;

    public const PARAMETER_EMPLOYEE_NAME = 'employeeName';
    public const PARAMETER_ENTITLEMENT_DAYS = 'entitlementDays';
    public const PARAMETER_PENDING_APPROVAL_DAYS = 'pendingApprovalDays';
    public const PARAMETER_SCHEDULED_DAYS = 'scheduledDays';
    public const PARAMETER_TAKEN_DAYS = 'takenDays';
    public const PARAMETER_BALANCE_DAYS = 'balanceDays';

    public const FILTER_PARAMETER_INCLUDE_EMPLOYEES = 'includeEmployees';
    public const FILTER_PARAMETER_JOB_TITLE_ID = 'jobTitleId';
    public const FILTER_PARAMETER_SUBUNIT_ID = 'subunitId';
    public const FILTER_PARAMETER_LOCATION_ID = 'locationId';

    public const DEFAULT_COLUMN_SIZE = 150;

    /**
     * @return Header
     */
    public function getHeaderDefinition(): Header
    {
        return new Header(
            [
                (new Column(self::PARAMETER_EMPLOYEE_NAME))
                    ->setName($this->getI18NHelper()->transBySource('Employee'))
                    ->setPin(Column::PIN_COL_START)
                    ->setSize(self::DEFAULT_COLUMN_SIZE),
                (new Column(self::PARAMETER_ENTITLEMENT_DAYS))
                    ->setName($this->getI18NHelper()->transBySource('Leave Entitlements (Days)'))
                    ->setCellProperties(['class' => ['col-alt' => true, 'cell-action' => true]])
                    ->setSize(self::DEFAULT_COLUMN_SIZE),
                (new Column(self::PARAMETER_PENDING_APPROVAL_DAYS))
                    ->setName($this->getI18NHelper()->transBySource('Leave Pending Approval (Days)'))
                    ->setCellProperties(['class' => ['cell-action' => true]])
                    ->setSize(self::DEFAULT_COLUMN_SIZE),
                (new Column(self::PARAMETER_SCHEDULED_DAYS))
                    ->setName($this->getI18NHelper()->transBySource('Leave Scheduled (Days)'))
                    ->setCellProperties(['class' => ['cell-action' => true]])
                    ->setSize(self::DEFAULT_COLUMN_SIZE),
                (new Column(self::PARAMETER_TAKEN_DAYS))
                    ->setName($this->getI18NHelper()->transBySource('Leave Taken (Days)'))
                    ->setCellProperties(['class' => ['cell-action' => true]])
                    ->setSize(self::DEFAULT_COLUMN_SIZE),
                (new Column(self::PARAMETER_BALANCE_DAYS))
                    ->setName($this->getI18NHelper()->transBySource('Leave Balance (Days)'))
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
     * @param LeaveTypeLeaveEntitlementUsageReportSearchFilterParams $filterParams
     * @return LeaveTypeLeaveEntitlementUsageReportData
     */
    public function getData(FilterParams $filterParams): LeaveTypeLeaveEntitlementUsageReportData
    {
        return new LeaveTypeLeaveEntitlementUsageReportData($filterParams);
    }

    /**
     * @inheritDoc
     */
    public function prepareFilterParams(EndpointProxy $endpoint): FilterParams
    {
        $filterParams = new LeaveTypeLeaveEntitlementUsageReportSearchFilterParams();
        $accessibleEmpNumbers = $this->getUserRoleManager()->getAccessibleEntityIds(Employee::class);
        $filterParams->setEmpNumbers($accessibleEmpNumbers);
        $filterParams->setLeaveTypeId(
            $endpoint->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_QUERY,
                LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID
            )
        );
        $filterParams->setJobTitleId(
            $endpoint->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_PARAMETER_JOB_TITLE_ID
            )
        );
        $filterParams->setSubunitId(
            $endpoint->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_PARAMETER_SUBUNIT_ID
            )
        );
        $filterParams->setLocationId(
            $endpoint->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_PARAMETER_LOCATION_ID
            )
        );
        $filterParams->setIncludeEmployees(
            $endpoint->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_PARAMETER_INCLUDE_EMPLOYEES,
                LeaveTypeLeaveEntitlementUsageReportSearchFilterParams::INCLUDE_EMPLOYEES_ONLY_CURRENT
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
            new ParamRule(
                LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID,
                new Rule(Rules::ENTITY_ID_EXISTS, [LeaveType::class])
            ),
            $endpoint->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::FILTER_PARAMETER_JOB_TITLE_ID, new Rule(Rules::ENTITY_ID_EXISTS, [JobTitle::class]))
            ),
            $endpoint->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::FILTER_PARAMETER_LOCATION_ID, new Rule(Rules::ENTITY_ID_EXISTS, [Location::class]))
            ),
            $endpoint->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::FILTER_PARAMETER_SUBUNIT_ID, new Rule(Rules::ENTITY_ID_EXISTS, [Subunit::class]))
            ),
            $endpoint->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_PARAMETER_INCLUDE_EMPLOYEES,
                    new Rule(
                        Rules::IN,
                        [LeaveTypeLeaveEntitlementUsageReportSearchFilterParams::INCLUDE_EMPLOYEES]
                    )
                )
            ),
            $endpoint->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(LeaveCommonParams::PARAMETER_FROM_DATE, new Rule(Rules::API_DATE))
            ),
            $endpoint->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(LeaveCommonParams::PARAMETER_TO_DATE, new Rule(Rules::API_DATE))
            ),
            ...
            $endpoint->getSortingAndPaginationParamsRules(
                LeaveTypeLeaveEntitlementUsageReportSearchFilterParams::ALLOWED_SORT_FIELDS
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function checkReportAccessibility(EndpointProxy $endpoint): void
    {
        if (!$this->getUserRoleManagerHelper()
            ->getEntityIndependentDataGroupPermissions('leave_report_leave_type_leave_entitlements_and_usage')
            ->canRead()) {
            throw new ForbiddenException();
        }
    }
}
