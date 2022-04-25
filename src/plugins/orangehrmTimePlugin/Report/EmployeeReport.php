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

namespace OrangeHRM\Time\Report;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Dto\FilterParams;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Report\Api\EndpointAwareReport;
use OrangeHRM\Core\Report\Api\EndpointProxy;
use OrangeHRM\Core\Report\Filter\Filter;
use OrangeHRM\Core\Report\Filter\FilterDefinition;
use OrangeHRM\Core\Report\Header\Column;
use OrangeHRM\Core\Report\Header\Header;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Project;
use OrangeHRM\Entity\ProjectActivity;
use OrangeHRM\I18N\Traits\Service\I18NHelperTrait;
use OrangeHRM\Time\Dto\EmployeeReportsSearchFilterParams;

class EmployeeReport implements EndpointAwareReport
{
    use UserRoleManagerTrait;
    use I18NHelperTrait;

    public const PARAMETER_PROJECT_NAME = 'projectName';
    public const PARAMETER_ACTIVITY_NAME = 'activityName';
    public const PARAMETER_DURATION = 'duration';

    public const FILTER_PARAMETER_PROJECT_ID = 'projectId';
    public const FILTER_PARAMETER_ACTIVITY_ID = 'activityId';
    public const FILTER_PARAMETER_FROM_DATE = 'fromDate';
    public const FILTER_PARAMETER_TO_DATE = 'toDate';
    public const FILTER_PARAMETER_TIMESHEET_STATE = 'timesheetState';

    public const DEFAULT_COLUMN_SIZE = 150;

    /**
     * @inheritDoc
     */
    public function prepareFilterParams(EndpointProxy $endpoint): FilterParams
    {
        $filterParams = new EmployeeReportsSearchFilterParams();
        $filterParams->setEmpNumber(
            $endpoint->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_QUERY,
                CommonParams::PARAMETER_EMP_NUMBER
            )
        );
        $endpoint->setSortingAndPaginationParams($filterParams);

        $filterParams->setProjectId(
            $endpoint->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_PARAMETER_PROJECT_ID
            )
        );
        $filterParams->setActivityId(
            $endpoint->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_PARAMETER_ACTIVITY_ID
            )
        );
        $filterParams->setProjectId(
            $endpoint->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_PARAMETER_PROJECT_ID
            )
        );
        $filterParams->setIncludeTimesheets(
            $endpoint->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_PARAMETER_TIMESHEET_STATE
            )
        );
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

    /**
     * @inheritDoc
     */
    public function getValidationRule(EndpointProxy $endpoint): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            $endpoint->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_PARAMETER_PROJECT_ID,
                    new Rule(Rules::ENTITY_ID_EXISTS, [Project::class])
                )
            ),
            $endpoint->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_PARAMETER_ACTIVITY_ID,
                    new Rule(Rules::ENTITY_ID_EXISTS, [ProjectActivity::class])
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
                    new Rule(Rules::API_DATE),
                    new Rule(Rules::CALLBACK, [
                        function () use ($endpoint) {
                            $fromDate = $endpoint->getRequestParams()->getDateTimeOrNull(
                                RequestParams::PARAM_TYPE_QUERY,
                                self::FILTER_PARAMETER_FROM_DATE
                            );

                            $toDate = $endpoint->getRequestParams()->getDateTimeOrNull(
                                RequestParams::PARAM_TYPE_QUERY,
                                self::FILTER_PARAMETER_TO_DATE
                            );

                            if (!(is_null($fromDate) || is_null($toDate)) && $fromDate > $toDate) {
                                return false;
                            }
                            return true;
                        }
                    ])
                )
            ),
            $endpoint->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_PARAMETER_TIMESHEET_STATE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(
                        Rules::IN,
                        [EmployeeReportsSearchFilterParams::INCLUDE_TIMESHEETS]
                    )
                ),
            ),
            ...$endpoint->getSortingAndPaginationParamsRules(EmployeeReportsSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
     * @throws DaoException
     */
    public function checkReportAccessibility(EndpointProxy $endpoint): void
    {
        $employeeNumber = $endpoint->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        if (!$this->getUserRoleManager()->getDataGroupPermissions(
            'time_employee_reports',
            [],
            [],
            $this->getUserRoleManagerHelper()->isSelfByEmpNumber($employeeNumber)
        )->canRead()) {
            throw new ForbiddenException();
        }
    }

    /**
     * @inheritDoc
     */
    public function getHeaderDefinition(): Header
    {
        return new Header(
            [
                (new Column(self::PARAMETER_PROJECT_NAME))->setName($this->getI18NHelper()->transBySource('Project Name'))
                    ->setSize(self::DEFAULT_COLUMN_SIZE),
                (new Column(self::PARAMETER_ACTIVITY_NAME))->setName($this->getI18NHelper()->transBySource('Activity Name'))
                    ->setCellProperties(['class' => ['col-alt' => true]])
                    ->setSize(self::DEFAULT_COLUMN_SIZE),
                (new Column(self::PARAMETER_DURATION))->setName($this->getI18NHelper()->transBySource('Time (Hours)'))
                    ->setSize(self::DEFAULT_COLUMN_SIZE)
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getFilterDefinition(): FilterDefinition
    {
        return new Filter();
    }

    /**
     * @param  EmployeeReportsSearchFilterParams  $filterParams
     * @return EmployeeReportData
     */
    public function getData(FilterParams $filterParams): EmployeeReportData
    {
        return new EmployeeReportData($filterParams);
    }
}
