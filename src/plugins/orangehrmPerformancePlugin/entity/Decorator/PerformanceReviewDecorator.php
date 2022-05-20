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

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\PerformanceReview;

class PerformanceReviewDecorator
{
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
    public function getDueDate(): ?string
    {
        return $this->getDateTimeHelper()->formatDateTimeToYmd($this->getPerformanceReview()->getDueDate());
    }

    /**
     * @return string|null
     */
    public function getReviewPeriodStart(): ?string
    {
        return $this->getDateTimeHelper()->formatDateTimeToYmd($this->getPerformanceReview()->getReviewPeriodStart());
    }

    /**
     * @return string|null
     */
    public function getReviewPeriodEnd(): ?string
    {
        return $this->getDateTimeHelper()->formatDateTimeToYmd($this->getPerformanceReview()->getReviewPeriodEnd());
    }

    /**
     * @return string
     */
    public function getStatusName(): string
    {
        $statusId = $this->getPerformanceReview()->getStatusId();
        switch ($statusId) {
            case PerformanceReview::STATUS_ACTIVATED:
                return 'Activated';
            case PerformanceReview::STATUS_IN_PROGRESS:
                return 'In progress';
            case PerformanceReview::STATUS_COMPLETED:
                return 'Completed';
            default:
                return '';
        }
    }
}
