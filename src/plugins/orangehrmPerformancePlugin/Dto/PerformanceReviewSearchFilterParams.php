<?php

namespace OrangeHRM\Performance\Dto;

use OrangeHRM\Core\Dto\FilterParams;

class PerformanceReviewSearchFilterParams extends FilterParams
{
    public const ALLOWED_SORT_FIELDS = ['performanceReview.statusId','performanceReview.reviewPeriod', 'performanceReview.dueDate', ];

    /**
     * @var int|null
     */
    protected ?int $EmpNumber = null;

    /**
     * @param int|null $EmpNumber
     */
    public function __construct()
    {
        $this->setSortField('performanceReview.statusId');
    }

    /**
     * @return int|null
     */
    public function getEmpNumber(): ?int
    {
        return $this->EmpNumber;
    }

    /**
     * @param int|null $EmpNumber
     */
    public function setEmpNumber(?int $EmpNumber): void
    {
        $this->EmpNumber = $EmpNumber;
    }
}
