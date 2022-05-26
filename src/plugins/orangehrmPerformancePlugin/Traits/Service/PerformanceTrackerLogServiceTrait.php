<?php

namespace OrangeHRM\Performance\Traits\Service;

use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Services;
use OrangeHRM\Performance\Service\PerformanceTrackerLogService;

trait PerformanceTrackerLogServiceTrait
{
    use ServiceContainerTrait;

    /**
     * @return PerformanceTrackerLogService
     */
    protected function getPerformanceTrackerService(): PerformanceTrackerLogService
    {
        return $this->getContainer()->get(Services::PERFORMANCE_TRACKER_LOG_SERVICE);
    }
}
