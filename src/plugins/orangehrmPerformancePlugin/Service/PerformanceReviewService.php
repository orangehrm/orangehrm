<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Performance\Service;

use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\JobTitle;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Entity\ReviewerRating;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\Performance\Dao\PerformanceReviewDao;
use OrangeHRM\Performance\Exception\ReviewServiceException;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

class PerformanceReviewService
{
    use EmployeeServiceTrait;
    use AuthUserTrait;
    use UserRoleManagerTrait;

    private ?PerformanceReviewDao $performanceReviewDao = null;

    /**
     * @return PerformanceReviewDao
     */
    public function getPerformanceReviewDao(): PerformanceReviewDao
    {
        if (!($this->performanceReviewDao instanceof PerformanceReviewDao)) {
            $this->performanceReviewDao = new PerformanceReviewDao();
        }
        return $this->performanceReviewDao;
    }

    /**
     * @param PerformanceReview $performanceReview
     * @param int $reviewerEmpNumber
     * @return PerformanceReview
     * @throws ReviewServiceException
     */
    public function activateReview(PerformanceReview $performanceReview, int $reviewerEmpNumber): PerformanceReview
    {
        $this->activateReviewCommonExceptions($performanceReview);
        return $this->getPerformanceReviewDao()->createReview($performanceReview, $reviewerEmpNumber);
    }

    /**
     * @param PerformanceReview $performanceReview
     * @return PerformanceReview
     * @throws ReviewServiceException|TransactionException
     */
    public function updateActivateReview(PerformanceReview $performanceReview, int $reviewerEmpNumber): PerformanceReview
    {
        $this->activateReviewCommonExceptions($performanceReview);
        if (!($this->getEmployeeService()->getEmployeeDao()->getEmployeeByEmpNumber($reviewerEmpNumber)->getEmployeeTerminationRecord()
            == null)) {
            throw ReviewServiceException::pastEmployeeForReviewer();
        }
        if ($this->getPerformanceReviewDao()->getSupervisorRecord(
            $performanceReview->getEmployee()->getEmpNumber(),
            $reviewerEmpNumber
        ) == null) {
            throw ReviewServiceException::invalidSupervisor();
        }

        return $this->getPerformanceReviewDao()->updateReview($performanceReview, $reviewerEmpNumber);
    }

    /**
     * @param PerformanceReview $performanceReview
     * @return void
     * @throws ReviewServiceException
     */
    private function activateReviewCommonExceptions(PerformanceReview $performanceReview): void
    {
        if (!$performanceReview->getEmployee()->getJobTitle() instanceof JobTitle) {
            throw ReviewServiceException::activateWithoutJobTitle();
        }
        if ($this->getPerformanceReviewDao()->getReviewKPI($performanceReview) == null) {
            throw ReviewServiceException::activateWithoutKPI();
        }
    }

    /**
     * @param PerformanceReview $review
     * @param array $ratings
     * @param string $reviewerGroupName
     * @return void
     */
    public function saveAndUpdateReviewRatings(PerformanceReview $review, array $ratings, string $reviewerGroupName): void
    {
        $reviewerRatings = $this->createRatingsFromRows($review, $ratings, $reviewerGroupName);
        $this->getPerformanceReviewDao()->saveAndUpdateReviewerRatings($reviewerRatings);
    }

    /**
     * @param PerformanceReview $review
     * @param array $rows
     * @param string $reviewerGroupName
     * @return array
     */
    private function createRatingsFromRows(PerformanceReview $review, array $rows, string $reviewerGroupName): array
    {
        $ratings = [];
        $reviewer = $this->performanceReviewDao->getReviewerRecord($review->getId(), $reviewerGroupName);

        foreach ($rows as $row) {
            $itemKey = $this->generateReviewReviewerRatingKey(
                $reviewer->getId(),
                $review->getId(),
                $row['kpiId'],
            );
            $reviewerRating = new ReviewerRating();
            $reviewerRating->setReviewer($reviewer);
            $reviewerRating->getDecorator()->setKpiByKpiId($row['kpiId']);
            $reviewerRating->setComment($row['comment']);
            $reviewerRating->setRating($row['rating'] === '' ? null : $row['rating']);
            $reviewerRating->setPerformanceReview($review);
            $ratings[$itemKey] = $reviewerRating;
        }
        return $ratings;
    }

    /**
     * @param int $reviewerId
     * @param int $performanceReviewId
     * @param int $kpiId
     * @return string
     */
    public function generateReviewReviewerRatingKey(int $reviewerId, int $performanceReviewId, int $kpiId): string
    {
        return $reviewerId . '_' .
            $performanceReviewId . '_' .
            $kpiId . '_';
    }
}
