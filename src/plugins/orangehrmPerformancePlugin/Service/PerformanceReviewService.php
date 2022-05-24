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

namespace OrangeHRM\Performance\Service;

use OrangeHRM\Entity\JobTitle;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Entity\Subunit;
use OrangeHRM\Performance\Dao\PerformanceReviewDao;
use OrangeHRM\Performance\Exception\ReviewServiceException;

class PerformanceReviewService
{
    private ?PerformanceReviewDao $performanceReviewDao = null;

    /**
     * @return PerformanceReviewDao
     */
    public function getPerformanceReviewDao(): PerformanceReviewDao
    {
        if (! ($this->performanceReviewDao instanceof PerformanceReviewDao)) {
            $this->performanceReviewDao = new PerformanceReviewDao();
        }
        return $this->performanceReviewDao;
    }

    /**
     * @param PerformanceReview $performanceReview
     * @param int $reviewerId
     * @return PerformanceReview
     * @throws ReviewServiceException
     */
    public function activateReview(PerformanceReview $performanceReview, int $reviewerId): PerformanceReview
    {
        if ((! $performanceReview->getEmployee()->getJobTitle() instanceof JobTitle) || (! $performanceReview->getEmployee()->getSubDivision() instanceof Subunit)) {
            throw ReviewServiceException::activateWithoutJobTitle();
        }
        return $this->getPerformanceReviewDao()->createReview($performanceReview, $reviewerId);
    }

    /**
     * @param PerformanceReview $performanceReview
     * @return PerformanceReview
     * @throws ReviewServiceException
     */
    public function updateActivateReview(PerformanceReview $performanceReview, int $reviewerEmpNumber): PerformanceReview
    {
        if (! $performanceReview->getEmployee()->getJobTitle() instanceof JobTitle) {
            throw ReviewServiceException::activateWithoutJobTitle();
        }
        if ($this->getPerformanceReviewDao()->getReviewKPI($performanceReview) == null) {
            throw ReviewServiceException::activateWithoutKPI();
        }
        return $this->getPerformanceReviewDao()->updateReview($performanceReview, $reviewerEmpNumber);
    }
}
