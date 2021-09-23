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

use OrangeHRM\Core\Api\Rest\ReportDataAPI;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Report\Api\EndpointAwareReport;

class LeaveReportDataAPI extends ReportDataAPI
{
    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $report = $this->getReport();
        $filterParams = $report->prepareFilterParams($this);
        $data = $report->getData($filterParams);

        return new EndpointCollectionResult(
            ArrayModel::class,
            $data->normalize(),
            $data->getMeta()
        );
    }

    /**
     * @return EndpointAwareReport
     * @throws BadRequestException
     */
    protected function getReport(): EndpointAwareReport
    {
        $reportName = $this->getReportName();
        if (!isset(LeaveReportAPI::LEAVE_REPORT_MAP[$reportName])) {
            throw $this->getBadRequestException('Invalid report name');
        }
        $reportClass = LeaveReportAPI::LEAVE_REPORT_MAP[$reportName];
        return new $reportClass();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        $paramRules = new ParamRuleCollection($this->getReportNameParamRule());
        // TODO:: should handle using report filter param validation
        $paramRules->setStrict(false);
        return $paramRules;
    }
}
