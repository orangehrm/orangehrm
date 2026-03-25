<?php

namespace OrangeHRM\Time\Report;

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
use OrangeHRM\Core\Report\Filter\FilterDefinition;
use OrangeHRM\Core\Report\Header\Column;
use OrangeHRM\Core\Report\Header\Header;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\I18N\Traits\Service\I18NHelperTrait;
use OrangeHRM\Time\Dto\ClientReportSearchFilterParams;
use OrangeHRM\Entity\Customer;
use OrangeHRM\Entity\Project;

class ClientReport implements EndpointAwareReport
{
    use UserRoleManagerTrait;
    use I18NHelperTrait;

    /* Report Columns */
    public const PARAMETER_CUSTOMER = 'customer';
    public const PARAMETER_PROJECT = 'project';
    public const PARAMETER_EMPLOYEE = 'employee';
    public const PARAMETER_HOURS_WORKED = 'hoursWorked';

    /* Filters */
    public const FILTER_PARAMETER_CUSTOMER_ID = 'customerId';
    public const FILTER_PARAMETER_PROJECT_ID = 'projectId';
    public const FILTER_PARAMETER_EMP_NUMBER = 'empNumber';
    public const FILTER_PARAMETER_FROM_DATE = 'fromDate';
    public const FILTER_PARAMETER_TO_DATE = 'toDate';
    public const FILTER_PARAMETER_TIMESHEET_STATE = 'timesheetState';

    public const DEFAULT_COLUMN_SIZE = 150;

    public function prepareFilterParams(EndpointProxy $endpoint): FilterParams
    {
        $filterParams = new ClientReportSearchFilterParams();

        $endpoint->setSortingAndPaginationParams($filterParams);

        $filterParams->setCustomerId(
            $endpoint->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_PARAMETER_CUSTOMER_ID
            )
        );

        $filterParams->setProjectId(
            $endpoint->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_PARAMETER_PROJECT_ID
            )
        );

        $filterParams->setEmpNumber(
            $endpoint->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_PARAMETER_EMP_NUMBER
            )
        );

        $timesheetState = $endpoint->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_PARAMETER_TIMESHEET_STATE
        );

        $filterParams->setIncludeTimesheets($timesheetState);

        $filterParams->setFromDate(
            $endpoint->getRequestParams()->getDateTimeOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_PARAMETER_FROM_DATE
            )
        );

        $filterParams->setToDate(
            $endpoint->getRequestParams()->getDateTimeOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_PARAMETER_TO_DATE
            )
        );

        return $filterParams;
    }

    public function getValidationRule(EndpointProxy $endpoint): ParamRuleCollection
    {
        return new ParamRuleCollection(

            $endpoint->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_PARAMETER_CUSTOMER_ID,
                    new Rule(Rules::ENTITY_ID_EXISTS, [Customer::class])
                )
            ),

            $endpoint->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_PARAMETER_PROJECT_ID,
                    new Rule(Rules::ENTITY_ID_EXISTS, [Project::class])
                )
            ),

            $endpoint->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_PARAMETER_EMP_NUMBER,
                    new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
                )
            ),

            $endpoint->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_PARAMETER_FROM_DATE,
                    new Rule(Rules::API_DATE)
                )
            ),

            $endpoint->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_PARAMETER_TO_DATE,
                    new Rule(Rules::API_DATE)
                )
            ),

            $endpoint->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_PARAMETER_TIMESHEET_STATE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::IN, ['all', 'onlyApproved'])
                )
            ),

            ...$endpoint->getSortingAndPaginationParamsRules(
                ClientReportSearchFilterParams::ALLOWED_SORT_FIELDS
            )
        );
    }

    public function checkReportAccessibility(EndpointProxy $endpoint): void
    {
        if (!$this->getUserRoleManager()->getDataGroupPermissions(
            'time_project_reports'
        )->canRead()) {
            throw new ForbiddenException();
        }
    }

    public function getHeaderDefinition(): Header
    {
        return new Header(
            [
                (new Column(self::PARAMETER_CUSTOMER))
                    ->setName($this->getI18NHelper()->transBySource('Customer'))
                    ->setSize(self::DEFAULT_COLUMN_SIZE),

                (new Column(self::PARAMETER_PROJECT))
                    ->setName($this->getI18NHelper()->transBySource('Project'))
                    ->setSize(self::DEFAULT_COLUMN_SIZE),

                (new Column(self::PARAMETER_EMPLOYEE))
                    ->setName($this->getI18NHelper()->transBySource('Employee'))
                    ->setSize(self::DEFAULT_COLUMN_SIZE),

                (new Column(self::PARAMETER_HOURS_WORKED))
                    ->setName($this->getI18NHelper()->transBySource('Hours Worked'))
                    ->setSize(self::DEFAULT_COLUMN_SIZE),
            ]
        );
    }

    public function getFilterDefinition(): FilterDefinition
    {
        return new Filter();
    }

    public function getData(FilterParams $filterParams): ClientReportData
    {
        return new ClientReportData($filterParams);
    }
}