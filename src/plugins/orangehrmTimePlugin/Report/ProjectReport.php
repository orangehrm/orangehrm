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
use OrangeHRM\Core\Report\Header\HeaderDefinition;
use OrangeHRM\Core\Report\ReportData;
use OrangeHRM\Core\Traits\Service\TextHelperTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Project;
use OrangeHRM\I18N\Traits\Service\I18NHelperTrait;
use OrangeHRM\Time\Dto\ProjectReportSearchFilterParams;

class ProjectReport implements EndpointAwareReport
{
    use UserRoleManagerTrait;
    use TextHelperTrait;
    use I18NHelperTrait;

    public const PARAMETER_ACTIVITY_NAME = 'activityName';
    public const PARAMETER_ACTIVITY_ID = 'activityId';
    public const PARAMETER_TIME = 'time';
    public const PARAMETER_ACTIVITY_DELETED = 'deleted';

    public const FILTER_PARAMETER_PROJECT_ID = 'projectId';
    public const FILTER_PARAMETER_DATE_FROM = 'fromDate';
    public const FILTER_PARAMETER_DATE_TO = 'toDate';
    public const FILTER_PARAMETER_PARAMETER_INCLUDE_TIMESHEET = 'includeTimesheet';

    public const DEFAULT_COLUMN_SIZE = 150;

    /**
     * @inheritDoc
     */
    public function prepareFilterParams(EndpointProxy $endpoint): FilterParams
    {
        $filterParams = new ProjectReportSearchFilterParams();
        $filterParams->setProjectId(
            $endpoint->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_PARAMETER_PROJECT_ID
            )
        );
        $endpoint->setSortingAndPaginationParams($filterParams);

        $filterParams->setFromDate(
            $endpoint->getRequestParams()->getDateTimeOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_PARAMETER_DATE_FROM
            )
        );
        $filterParams->setToDate(
            $endpoint->getRequestParams()->getDateTimeOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_PARAMETER_DATE_TO
            )
        );
        $filterParams->setIncludeApproveTimesheet(
            $endpoint->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_PARAMETER_PARAMETER_INCLUDE_TIMESHEET
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
            $endpoint->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::FILTER_PARAMETER_PROJECT_ID,
                    new Rule(Rules::POSITIVE),
                    new Rule(Rules::ENTITY_ID_EXISTS, [Project::class]),
                    new Rule(Rules::IN_ACCESSIBLE_ENTITY_ID, [Project::class])
                )
            ),
            $endpoint->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::FILTER_PARAMETER_DATE_FROM, new Rule(Rules::API_DATE))
            ),
            $endpoint->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_PARAMETER_DATE_TO,
                    new Rule(Rules::API_DATE),
                    new Rule(Rules::CALLBACK, [
                        function () use ($endpoint) {
                            $fromDate = $endpoint->getRequestParams()->getDateTimeOrNull(
                                RequestParams::PARAM_TYPE_QUERY,
                                self::FILTER_PARAMETER_DATE_FROM
                            );

                            $toDate = $endpoint->getRequestParams()->getDateTimeOrNull(
                                RequestParams::PARAM_TYPE_QUERY,
                                self::FILTER_PARAMETER_DATE_TO
                            );

                            if (!is_null($fromDate) && !is_null($toDate)) {
                                if ($fromDate > $toDate) {
                                    return false;
                                }
                            }
                            return true;
                        }
                    ])
                )
            ),
            $endpoint->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_PARAMETER_PARAMETER_INCLUDE_TIMESHEET,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(
                        Rules::IN,
                        [ProjectReportSearchFilterParams::INCLUDE_TIMESHEET]
                    )
                )
            ),
            ...$endpoint->getSortingAndPaginationParamsRules(
                ProjectReportSearchFilterParams::ALLOWED_SORT_FIELDS
            )
        );
    }

    /**
     * @return Header
     */
    public function getHeaderDefinition(): HeaderDefinition
    {
        return new Header(
            [
                (new Column(self::PARAMETER_ACTIVITY_NAME))
                    ->setName($this->getI18NHelper()->transBySource('Activity Name'))
                    ->setCellProperties(['class' => ['cell-action' => true]])
                    ->setSize(self::DEFAULT_COLUMN_SIZE),
                (new Column(self::PARAMETER_TIME))
                    ->setName($this->getI18NHelper()->transBySource('Time (Hours)'))
                    ->setCellProperties(['class' => ['col-alt' => true]])
                    ->setSize(self::DEFAULT_COLUMN_SIZE),
            ]
        );
    }

    /**
     * @return Filter
     */
    public function getFilterDefinition(): FilterDefinition
    {
        return new Filter();
    }

    /**
     * @param ProjectReportSearchFilterParams $filterParams
     * @return ProjectReportData
     */
    public function getData(FilterParams $filterParams): ReportData
    {
        return new ProjectReportData($filterParams);
    }

    /**
     * @inheritDoc
     */
    public function checkReportAccessibility(EndpointProxy $endpoint): void
    {
        if (!$this->getUserRoleManagerHelper()
            ->getEntityIndependentDataGroupPermissions('time_project_reports')->canRead()) {
            throw new ForbiddenException();
        }
    }
}
