<?php

namespace OrangeHRM\Core\Traits\Service;

use OrangeHRM\Core\Service\ReportGeneratorService;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Services;

trait ReportGeneratorServiceTrait
{
    use ServiceContainerTrait;

    /**
     * @return ReportGeneratorService
     */
    public function getReportGeneratorService(): ReportGeneratorService
    {
        return $this->getContainer()->get(Services::REPORT_GENERATOR_SERVICE);
    }
}
