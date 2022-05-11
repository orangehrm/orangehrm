<?php

namespace OrangeHRM\Performance\Traits\Service;

use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Services;
use OrangeHRM\Performance\Service\PerformanceReviewService;

trait PerformanceReviewServiceTrait
{
    use ServiceContainerTrait;

    /**
     * @return PerformanceReviewService
     * @throws Exception
     */
    protected function getPerformanceReviewService(): PerformanceReviewService
    {
        return $this->getContainer()->get(Services::PERFORMANCE_REVIEW_SERVICE);
    }
}
