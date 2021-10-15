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

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Report\ReportData;
use OrangeHRM\Core\Service\ReportGeneratorService;
use OrangeHRM\Pim\Dto\PimReportSearchFilterParams;

class PimReportData implements ReportData
{
    /**
     * @var ReportGeneratorService|null
     */
    private ?ReportGeneratorService $reportGeneratorService = null;

    private PimReportSearchFilterParams $filterParams;

    public function __construct(PimReportSearchFilterParams $filterParams)
    {
        $this->filterParams = $filterParams;
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
     * @inheritDoc
     */
    public function normalize(): array
    {
        return $this->getReportGeneratorService()->getNormalizedReportData($this->filterParams);
    }

    /**
     * @inheritDoc
     */
    public function getMeta(): ?ParameterBag
    {
        $count = $this->getReportGeneratorService()->getReportDataCount($this->filterParams);
        return new ParameterBag([CommonParams::PARAMETER_TOTAL => $count]);
    }
}
