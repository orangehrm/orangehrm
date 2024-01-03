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

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\JobTitle;
use OrangeHRM\Entity\Reviewer;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Entity\ReviewerGroup;

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
    protected function getPerformanceReview(): PerformanceReview
    {
        return $this->performanceReview;
    }

    /**
     * @param int $empNumber
     */
    public function setEmployeeByEmpNumber(int $empNumber): void
    {
        $employee = $this->getReference(Employee::class, $empNumber);
        $this->getPerformanceReview()->setEmployee($employee);
    }

    /**
     * @param int $id
     */
    public function setJobTitleById(int $id): void
    {
        $jobTitle = $this->getReference(JobTitle::class, $id);
        $this->getPerformanceReview()->setJobTitle($jobTitle);
    }

    /**
     * @return Reviewer
     */
    public function getSupervisorReviewer(): Reviewer
    {
        $reviewers = [...$this->performanceReview->getReviewers()];
        $supervisorArray = array_filter($reviewers, function ($reviewer) {
            /** @var Reviewer $reviewer */
            return $reviewer->getGroup()->getName() === ReviewerGroup::REVIEWER_GROUP_SUPERVISOR;
        });
        return array_values($supervisorArray)[0];
    }

    /**
     * @return Reviewer
     */
    public function getEmployeeReviewer(): Reviewer
    {
        $reviewers = [...$this->performanceReview->getReviewers()];
        $employeeArray = array_filter($reviewers, function ($reviewer) {
            /** @var Reviewer $reviewer */
            return $reviewer->getGroup()->getName() === ReviewerGroup::REVIEWER_GROUP_EMPLOYEE;
        });
        return array_values($employeeArray)[0];
    }

    /**
     * @return string|null
     */
    public function getDueDate(): ?string
    {
        return $this->getDateTimeHelper()->formatDate($this->getPerformanceReview()->getDueDate());
    }

    /**
     * @return string|null
     */
    public function getReviewPeriodStart(): ?string
    {
        return $this->getDateTimeHelper()->formatDate($this->getPerformanceReview()->getReviewPeriodStart());
    }

    /**
     * @return string|null
     */
    public function getReviewPeriodEnd(): ?string
    {
        return $this->getDateTimeHelper()->formatDate($this->getPerformanceReview()->getReviewPeriodEnd());
    }

    /**
     * @return string|null
     */
    public function getCompletedDate(): ?string
    {
        return $this->getDateTimeHelper()->formatDate($this->getPerformanceReview()->getCompletedDate());
    }

    /**
     * @return string
     */
    public function getStatusName(): string
    {
        $statusId = $this->getPerformanceReview()->getStatusId();
        switch ($statusId) {
            case PerformanceReview::STATUS_INACTIVE:
                return 'Inactive';
            case PerformanceReview::STATUS_ACTIVATED:
                return 'Activated';
            case PerformanceReview::STATUS_IN_PROGRESS:
                return 'In Progress';
            case PerformanceReview::STATUS_COMPLETED:
                return 'Completed';
            default:
                return '';
        }
    }
}
