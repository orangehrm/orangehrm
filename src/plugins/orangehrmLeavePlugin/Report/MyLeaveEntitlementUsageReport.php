<?php

namespace OrangeHRM\Leave\Report;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Report\Api\EndpointProxy;
use OrangeHRM\Leave\Api\LeaveCommonParams;
use OrangeHRM\Leave\Dto\EmployeeLeaveEntitlementUsageReportSearchFilterParams;

class MyLeaveEntitlementUsageReport extends EmployeeLeaveEntitlementUsageReport
{
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
