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

namespace OrangeHRM\Pim\Report;

use OrangeHRM\Core\Api\Rest\ReportAPI;
use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Dto\FilterParams;
use OrangeHRM\Core\Report\Api\EndpointAwareReport;
use OrangeHRM\Core\Report\Api\EndpointProxy;
use OrangeHRM\Core\Report\Filter\Filter;
use OrangeHRM\Core\Report\Header\Header;
use OrangeHRM\Core\Service\ReportGeneratorService;
use OrangeHRM\Pim\Api\PimReportAPI;
use OrangeHRM\Pim\Dto\PimReportSearchFilterParams;

class PimReport implements EndpointAwareReport
{
    /**
     * @var ReportGeneratorService|null
     */
    private ?ReportGeneratorService $reportGeneratorService = null;

    /**
     * @var int
     */
    private int $reportId;

    /**
     * @param int $reportId
     */
    public function __construct(int $reportId)
    {
        $this->reportId = $reportId;
    }

    /**
     * @return ReportGeneratorService
     */
    protected function getReportGeneratorService(): ReportGeneratorService
    {
        if (!$this->reportGeneratorService instanceof ReportGeneratorService) {
            $this->reportGeneratorService = new ReportGeneratorService();
        }
        return $this->reportGeneratorService;
    }

    /**
     * @return Header
     */
    public function getHeaderDefinition(): Header
    {
        return $this->getReportGeneratorService()->getHeaderDefinitionByReportId($this->reportId);
    }

    /**
     * @return Filter
     */
    public function getFilterDefinition(): Filter
    {
        return new Filter();
    }

    /**
     * @param PimReportSearchFilterParams $filterParams
     * @return PimReportData
     */
    public function getData(FilterParams $filterParams): PimReportData
    {
        return new PimReportData($filterParams);
    }

    /**
     * @inheritDoc
     */
    public function prepareFilterParams(EndpointProxy $endpoint): PimReportSearchFilterParams
    {
        $filterParams = new PimReportSearchFilterParams();
        $endpoint->setSortingAndPaginationParams($filterParams);
        $filterParams->setReportId(
            $endpoint->getRequestParams()->getInt(RequestParams::PARAM_TYPE_QUERY, PimReportAPI::PARAMETER_REPORT_ID)
        );
        return $filterParams;
    }

    /**
     * @inheritDoc
     */
    public function getValidationRule(EndpointProxy $endpoint): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$endpoint->getSortingAndPaginationParamsRules([])
        );
    }

    /**
     * @inheritDoc
     */
    public function checkReportAccessibility(EndpointProxy $endpoint): void
    {
        $reportName = $endpoint->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            ReportAPI::PARAMETER_NAME
        );
        if ($reportName !== 'pim_defined') {
            // Should handle permissions if PIM report requirement changes
            throw new ForbiddenException();
        }
    }
}
