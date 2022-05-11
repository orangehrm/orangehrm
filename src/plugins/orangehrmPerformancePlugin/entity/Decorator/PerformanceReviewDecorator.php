<?php

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\PerformanceReview;

class PerformanceReviewDecorator
{
    use EntityManagerHelperTrait;
    use DateTimeHelperTrait;

    protected PerformanceReview $performanceReview;

    /**
     * @param PerformanceReview $performanceReview
     */
    public function __construct(PerformanceReview $performanceReview)
    {
        $this->performanceReview = $performanceReview;
    }

    /**
     * @return PerformanceReview
     */
    public function getPerformanceReview(): PerformanceReview
    {
        return $this->performanceReview;
    }

    /**
     * @return string|null
     */
    public function getDueDate()
    {
        return $this->getDateTimeHelper()->formatDateTimeToYmd($this->getPerformanceReview()->getDueDate());
    }

    /**
     * @return string|null
     */
    public function getWorkPeriodStart()
    {
        return $this->getDateTimeHelper()->formatDateTimeToYmd($this->getPerformanceReview()->getWorkPeriodStart());
    }

    /**
     * @return string|null
     */
    public function getWorkPeriodEnd()
    {
        return $this->getDateTimeHelper()->formatDateTimeToYmd($this->getPerformanceReview()->getWorkPeriodEnd());
    }

    public function getStatusName()
    {
        $statusId = $this->getPerformanceReview()->getStatusId();
        switch ($statusId) {
            case 1:
                return 'Activated';
            case 2:
                return 'In progress';
            case 4:
            case 3:
                return 'Completed';
            default:
                return '';
        }
    }
}
