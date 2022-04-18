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

namespace OrangeHRM\Pim\Api;

use OrangeHRM\Core\Api\Rest\ReportAPI;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Report\Api\EndpointAwareReport;
use OrangeHRM\Core\Service\ReportGeneratorService;
use OrangeHRM\Pim\Report\PimReport;

class PimReportAPI extends ReportAPI
{
    public const PARAMETER_REPORT_ID = 'reportId';

    public const PIM_REPORT_MAP = [
        'pim_defined' => PimReport::class,
    ];

    private ?ReportGeneratorService $reportGeneratorService = null;

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
     * @return EndpointAwareReport
     * @throws BadRequestException
     */
    protected function getReport(): EndpointAwareReport
    {
        $reportName = $this->getReportName();
        if (!isset(PimReportAPI::PIM_REPORT_MAP[$reportName])) {
            throw $this->getBadRequestException('Invalid report name');
        }
        $reportClass = PimReportAPI::PIM_REPORT_MAP[$reportName];
        $reportId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_REPORT_ID);
        return new $reportClass($reportId);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getReportNameParamRule(),
            new ParamRule(
                self::PARAMETER_REPORT_ID,
                new Rule(Rules::POSITIVE),
                new Rule(
                    Rules::CALLBACK,
                    [fn ($id) => $this->getReportGeneratorService()->isPimReport($id)]
                )
            )
        );
    }
}
