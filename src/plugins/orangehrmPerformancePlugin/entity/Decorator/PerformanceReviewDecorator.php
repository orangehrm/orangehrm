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
use OrangeHRM\Entity\Reviewer;

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
    protected function getPerformanceReview(): PerformanceReview
    {
        return $this->performanceReview;
    }

    /**
     * @return Reviewer
     */
    public function getSupervisorReviewer(): Reviewer
    {
        $reviewers = [...$this->performanceReview->getReviewers()];
        $supervisorArray = array_filter($reviewers, function ($reviewer) {
            /** @var Reviewer $reviewer */
            return $reviewer->getGroup()->getName() === 'Supervisor';
        });
        return array_values($supervisorArray)[0];
    }

    /**
     * @return string|null
     */
    public function getWorkPeriodStart(): ?string
    {
        return $this->getDateTimeHelper()->formatDateTimeToYmd(
            $this->getPerformanceReview()->getWorkPeriodStart()
        );
    }

    /**
     * @return string|null
     */
    public function getWorkPeriodEnd(): ?string
    {
        return $this->getDateTimeHelper()->formatDateTimeToYmd(
            $this->getPerformanceReview()->getWorkPeriodEnd()
        );
    }

    /**
     * @return string|null
     */
    public function getCompletedDate(): ?string
    {
        return $this->getDateTimeHelper()->formatDateTimeToYmd(
            $this->getPerformanceReview()->getCompletedDate()
        );
    }

    /**
     * @return string|null
     */
    public function getDueDate(): ?string
    {
        return $this->getDateTimeHelper()->formatDateTimeToYmd(
            $this->getPerformanceReview()->getDueDate()
        );
    }
}
