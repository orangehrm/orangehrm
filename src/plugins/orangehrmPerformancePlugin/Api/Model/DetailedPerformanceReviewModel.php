<?php

namespace OrangeHRM\Performance\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Performance\Traits\Service\PerformanceReviewServiceTrait;

class DetailedPerformanceReviewModel implements Normalizable
{
    use NormalizerServiceTrait;
    use DateTimeHelperTrait;
    use PerformanceReviewServiceTrait;

    private PerformanceReview $performanceReview;

    /**
     * @param PerformanceReview $performanceReview
     */
    public function __construct(PerformanceReview $performanceReview)
    {
        $this->performanceReview = $performanceReview;
    }

    public function toArray(): array
    {
        $selfReviewStatus = $this->getPerformanceReviewService()->getPerformanceReviewDao()
            ->getPerformanceSelfReviewStatus($this->performanceReview);
        return [
            'id' => $this->performanceReview->getId(),
            'jobTitle' => [
                'id' => $this->performanceReview->getJobTitle()->getId(),
                'name' => $this->performanceReview->getJobTitle()->getJobTitleName(),
            ],
            'department' => [
                'id' => $this->performanceReview->getDepartment()->getId(),
                'name' => $this->performanceReview->getDepartment()->getName(),
            ],
            'workPeriodStart' => $this->performanceReview->getDecorator()->getWorkPeriodStart(),
            'workPeriodEnd' => $this->performanceReview->getDecorator()->getWorkPeriodEnd(),
            'dueDate' => $this->performanceReview->getDecorator()->getDueDate(),
            'overallStatus' => [
                'statusId' => $this->performanceReview->getStatusId(),
                'statusName' => $this->performanceReview->getDecorator()->getStatusName(),
            ],
            'selfReviewStatus' => $selfReviewStatus,
        ];
    }
}
