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

namespace OrangeHRM\Time\Report;

use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Api\V2\Validator\ValidatorException;
use OrangeHRM\Core\Dto\FilterParams;
use OrangeHRM\Core\Report\Api\EndpointProxy;
use OrangeHRM\Core\Report\Header\Column;
use OrangeHRM\Core\Report\Header\Header;
use OrangeHRM\Core\Report\Header\HeaderDefinition;
use OrangeHRM\Core\Report\ReportData;
use OrangeHRM\Entity\ProjectActivity;
use OrangeHRM\I18N\Traits\Service\I18NHelperTrait;
use OrangeHRM\Time\Dto\ProjectActivityDetailedReportSearchFilterParams;

class ProjectActivityReport extends ProjectReport
{
    use I18NHelperTrait;

    public const PARAMETER_EMPLOYEE_NAME = 'employeeName';

    public const FILTER_PARAMETER_PROJECT_ACTIVITY_ID = 'activityId';

    /**
     * @inheritDoc
     */
    public function prepareFilterParams(EndpointProxy $endpoint): FilterParams
    {
        $filterParams = new ProjectActivityDetailedReportSearchFilterParams();
        $filterParams->setProjectId(
            $endpoint->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_PARAMETER_PROJECT_ID
            )
        );

        $filterParams->setProjectActivityId(
            $endpoint->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_PARAMETER_PROJECT_ACTIVITY_ID
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
     * @param EndpointProxy $endpoint
     * @return ParamRuleCollection
     * @throws ValidatorException
     */
    public function getValidationRule(EndpointProxy $endpoint): ParamRuleCollection
    {
        $paramRuleCollection = parent::getValidationRule($endpoint);
        $paramRuleCollection->addParamValidation(
            $endpoint->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    ProjectReport::PARAMETER_ACTIVITY_ID,
                    new Rule(Rules::POSITIVE),
                    new Rule(Rules::ENTITY_ID_EXISTS, [ProjectActivity::class]),
                )
            ),
        );
        return $paramRuleCollection;
    }

    /**
     * @return Header
     */
    public function getHeaderDefinition(): HeaderDefinition
    {
        return new Header(
            [
                (new Column(self::PARAMETER_EMPLOYEE_NAME))
                    ->setName($this->getI18NHelper()->transBySource('Employee Name'))
                    ->setSize(ProjectReport::DEFAULT_COLUMN_SIZE),
                (new Column(ProjectReport::PARAMETER_TIME))
                    ->setName($this->getI18NHelper()->transBySource('Time (Hours)'))
                    ->setCellProperties(['class' => ['col-alt' => true]])
                    ->setSize(ProjectReport::DEFAULT_COLUMN_SIZE),
            ]
        );
    }

    /**
     * @param ProjectActivityDetailedReportSearchFilterParams $filterParams
     * @return ProjectActivityDetailedReportData
     */
    public function getData(FilterParams $filterParams): ReportData
    {
        return new ProjectActivityDetailedReportData($filterParams);
    }
}
