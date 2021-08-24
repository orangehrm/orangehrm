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

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Leave\Api\Model\BulkLeaveEntitlementMatchingEmployeeModel;
use OrangeHRM\Leave\Api\ValidationRules\LeaveTypeIdRule;
use OrangeHRM\Leave\Dto\LeaveEntitlementSearchFilterParams;
use OrangeHRM\Leave\Traits\Service\LeaveEntitlementServiceTrait;
use OrangeHRM\Leave\Traits\Service\LeavePeriodServiceTrait;
use OrangeHRM\Pim\Dto\EmployeeSearchFilterParams;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

class BulkLeaveEntitlementMatchingEmployeeAPI extends Endpoint implements CollectionEndpoint
{
    use EmployeeServiceTrait;
    use LeaveEntitlementServiceTrait;
    use LeavePeriodServiceTrait;
    use DateTimeHelperTrait;

    public const PARAMETER_ENTITLEMENT = 'entitlement';
    public const PARAMETER_LOCATION_ID = 'locationId';
    public const PARAMETER_SUBUNIT_ID = 'subunitId';

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        list($employees, $total) = $this->getMatchingEmployeesAndCount();
        $empNumbers = $this->getEmpNumbersByEmployees($employees);

        $leaveTypeId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_QUERY,
            LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID
        );
        $currentLeavePeriod = $this->getLeavePeriodService()->getCurrentLeavePeriod();
        $fromDate = $this->getRequestParams()->getDateTime(
            RequestParams::PARAM_TYPE_QUERY,
            LeaveCommonParams::PARAMETER_FROM_DATE,
            null,
            $currentLeavePeriod->getStartDate()
        );
        $toDate = $this->getRequestParams()->getDateTime(
            RequestParams::PARAM_TYPE_QUERY,
            LeaveCommonParams::PARAMETER_TO_DATE,
            null,
            $currentLeavePeriod->getEndDate()
        );
        $entitlement = $this->getRequestParams()->getFloatOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_ENTITLEMENT,
        );

        $entitlementSearchFilterParams = new LeaveEntitlementSearchFilterParams();
        $entitlementSearchFilterParams->setEmpNumbers($empNumbers);
        $entitlementSearchFilterParams->setLeaveTypeId($leaveTypeId);
        $entitlementSearchFilterParams->setFromDate($fromDate);
        $entitlementSearchFilterParams->setToDate($toDate);
        // Since limit when fetching employee list
        $entitlementSearchFilterParams->setLimit(0);
        $this->setSortingAndPaginationParams($entitlementSearchFilterParams);

        $entitlements = $this->getLeaveEntitlementService()
            ->getLeaveEntitlementDao()
            ->getLeaveEntitlements($entitlementSearchFilterParams);

        return new EndpointCollectionResult(
            BulkLeaveEntitlementMatchingEmployeeModel::class,
            [$employees, $entitlements, $entitlement],
            new ParameterBag(
                [
                    CommonParams::PARAMETER_TOTAL => $total,
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => $leaveTypeId,
                    LeaveCommonParams::PARAMETER_DURATION_FROM_TIME => $this->getDateTimeHelper()
                        ->formatDateTimeToYmd($fromDate),
                    LeaveCommonParams::PARAMETER_DURATION_TO_TIME => $this->getDateTimeHelper()
                        ->formatDateTimeToYmd($toDate),
                ]
            )
        );
    }

    /**
     * @param Employee[] $employees
     * @return int[]
     */
    private function getEmpNumbersByEmployees(array $employees): array
    {
        return array_map(
            function (Employee $employee) {
                return $employee->getEmpNumber();
            },
            $employees
        );
    }

    /**
     * @return array array(Employee[], int)
     */
    private function getMatchingEmployeesAndCount(): array
    {
        $locationId = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_LOCATION_ID
        );
        $subunitId = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_SUBUNIT_ID
        );
        $employeeSearchFilterParams = new EmployeeSearchFilterParams();
        $employeeSearchFilterParams->setSubunitId($subunitId);
        $employeeSearchFilterParams->setLocationId($locationId);
        $this->setSortingAndPaginationParams($employeeSearchFilterParams);
        return [
            $this->getEmployeeService()->getEmployeeList($employeeSearchFilterParams),
            $this->getEmployeeService()->getEmployeeCount($employeeSearchFilterParams)
        ];
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID, new Rule(LeaveTypeIdRule::class)),
            new ParamRule(
                LeaveCommonParams::PARAMETER_FROM_DATE,
                new Rule(Rules::API_DATE),
                new Rule(Rules::LESS_THAN_OR_EQUAL, [
                    fn() => $this->getRequestParams()->getDateTime(
                        RequestParams::PARAM_TYPE_QUERY,
                        LeaveCommonParams::PARAMETER_TO_DATE
                    )
                ])
            ),
            new ParamRule(LeaveCommonParams::PARAMETER_TO_DATE, new Rule(Rules::API_DATE)),
            $this->getValidationDecorator()->notRequiredParamRule(
            // Zero not allowed, it can be achieved through passing null
                new ParamRule(self::PARAMETER_ENTITLEMENT, new Rule(Rules::POSITIVE))
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::PARAMETER_LOCATION_ID, new Rule(Rules::POSITIVE))
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::PARAMETER_SUBUNIT_ID, new Rule(Rules::POSITIVE))
            ),
            ...$this->getSortingAndPaginationParamsRules(EmployeeSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }
}
