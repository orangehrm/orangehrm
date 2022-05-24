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

namespace OrangeHRM\Performance\Dao;

use Doctrine\ORM\Query\Expr;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Kpi;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Entity\ReportTo;
use OrangeHRM\Entity\Reviewer;
use OrangeHRM\Entity\ReviewerGroup;
use OrangeHRM\ORM\QueryBuilderWrapper;
use OrangeHRM\Performance\Dto\PerformanceReviewSearchFilterParams;
use OrangeHRM\Performance\Dto\ReviewEmployeeSupervisorSearchFilterParams;

class PerformanceReviewDao extends BaseDao
{
    /**
     * @param ReviewEmployeeSupervisorSearchFilterParams $reviewEmployeeSupervisorSearchFilterParams
     * @return Employee[]
     */
    public function getEmployeeSupervisorList(ReviewEmployeeSupervisorSearchFilterParams $reviewEmployeeSupervisorSearchFilterParams): array
    {
        $q = $this->createQueryBuilder(ReportTo::class, 'rt');
        $q->leftJoin('rt.supervisor', 'employee')
            ->andWhere('rt.subordinate = :empNumber')
            ->setParameter('empNumber', $reviewEmployeeSupervisorSearchFilterParams->getEmpNumber());
        if (! is_null($reviewEmployeeSupervisorSearchFilterParams->getNameOrId())) {
            $q->andWhere(
                $q->expr()->orX(
                    $q->expr()->like('employee.firstName', ':nameOrId'),
                    $q->expr()->like('employee.lastName', ':nameOrId'),
                    $q->expr()->like('employee.middleName', ':nameOrId'),
                    $q->expr()->like('employee.employeeId', ':nameOrId'),
                )
            );
            $q->setParameter('nameOrId', '%'.$reviewEmployeeSupervisorSearchFilterParams->getNameOrId().'%');
        }
        return $q->getQuery()->execute();
    }

    /**
     * @param PerformanceReview $performanceReview
     * @param int $reviewerEmpNumber
     * @return PerformanceReview
     */
    public function createReview(PerformanceReview $performanceReview, int $reviewerEmpNumber): PerformanceReview
    {
        $this->persist($performanceReview);
        $this->saveReviewer($performanceReview, 'Supervisor', $reviewerEmpNumber);
        $this->saveReviewer($performanceReview, 'Employee', null);
        return $performanceReview;
    }

    /**
     * @param PerformanceReview $performanceReview
     * @param string $reviewerGroupName
     * @param int|null $reviewerEmpNumber
     * @return void
     */
    private function saveReviewer(PerformanceReview $performanceReview, string $reviewerGroupName, ?int $reviewerEmpNumber)
    {
        $reviewer = new Reviewer();
        if (!is_null($reviewerEmpNumber)) {
            $reviewer->getDecorator()->setEmployeeByEmpNumber($reviewerEmpNumber);
        } else {
            $reviewer->setEmployee($performanceReview->getEmployee());
        }
        $reviewer->setStatus(Reviewer::STATUS_ACTIVATED);
        $reviewerGroup = $this->getRepository(ReviewerGroup::class)->findOneBy(['name' => $reviewerGroupName]);
        $reviewer->setGroup($reviewerGroup);
        $reviewer->setReview($performanceReview);
        $this->persist($reviewer);
    }

    /**
     * @param int $id
     * @return PerformanceReview|null
     */
    public function getEditableReviewById(int $id): ?PerformanceReview
    {
        $review = $this->getRepository(PerformanceReview::class)->findOneBy(['id' => $id, 'statusId' => PerformanceReview::STATUS_INACTIVE]);
        if ($review instanceof PerformanceReview) {
            return $review;
        }
        return null;
    }

    /**
     * @param PerformanceReviewSearchFilterParams $performanceReviewSearchFilterParams
     * @return PerformanceReview[]
     */
    public function getPerformanceReviewList(PerformanceReviewSearchFilterParams $performanceReviewSearchFilterParams): array
    {
        $query = $this->getPerformanceReviewQueryBuilderWrapper($performanceReviewSearchFilterParams)->getQueryBuilder();
        return $query->getQuery()->execute();
    }

    /**
     * @param PerformanceReviewSearchFilterParams $performanceReviewSearchFilterParams
     * @return int
     */
    public function getPerformanceReviewCount(PerformanceReviewSearchFilterParams $performanceReviewSearchFilterParams): int
    {
        $query = $this->getPerformanceReviewQueryBuilderWrapper($performanceReviewSearchFilterParams)->getQueryBuilder();
        return $this->getPaginator($query)->count();
    }

    /**
     * @param PerformanceReviewSearchFilterParams $performanceReviewSearchFilterParams
     * @return QueryBuilderWrapper
     */
    private function getPerformanceReviewQueryBuilderWrapper(PerformanceReviewSearchFilterParams $performanceReviewSearchFilterParams): QueryBuilderWrapper
    {
        $q = $this->createQueryBuilder(PerformanceReview::class, 'performanceReview');
        $q->leftJoin('performanceReview.employee ', 'employee');
        $q->leftJoin('performanceReview.jobTitle', 'jobTitle');
        $q->leftJoin('performanceReview.subunit', 'subUnit');
        $q->leftJoin(Reviewer::class, 'reviewer', Expr\Join::WITH, 'performanceReview.id = reviewer.review');
        $this->setSortingAndPaginationParams($q, $performanceReviewSearchFilterParams);
        $q->andWhere('reviewer.employee = performanceReview.employee')
            ->andWhere('employee.empNumber = :employeeNumber')
            ->andWhere('performanceReview.statusId > :inactiveStatus')
            ->setParameter('employeeNumber', $performanceReviewSearchFilterParams->getEmpNumber())
            ->setParameter('inactiveStatus', PerformanceReview::STATUS_INACTIVE);
        return $this->getQueryBuilderWrapper($q);
    }

    /**
     * @param PerformanceReview $performanceReview
     * @return string
     */
    public function getPerformanceSelfReviewStatus(PerformanceReview $performanceReview): string
    {
        $selfReviewer = $this->getPerformanceSelfReviewer($performanceReview);
        switch ($selfReviewer->getStatus()) {
            case Reviewer::STATUS_ACTIVATED:
                return 'Activated';
            case Reviewer::STATUS_IN_PROGRESS:
                return 'In progress';
            case Reviewer::STATUS_COMPLETED:
                return 'Completed';
            default:
                return '';
        }
    }

    /**
     * @param PerformanceReview $performanceReview
     * @param int $reviewerEmpNumber
     * @return PerformanceReview
     */
    public function updateReview(PerformanceReview $performanceReview, int $reviewerEmpNumber): PerformanceReview
    {
        $this->deletePerformanceReviewReviewers($performanceReview);
        $this->persist($performanceReview);
        $this->saveReviewer($performanceReview, 'Supervisor', $reviewerEmpNumber);
        $this->saveReviewer($performanceReview, 'Employee', null);
        return $performanceReview;
    }

    /**
     * @param PerformanceReview $performanceReview
     * @return void
     */
    private function deletePerformanceReviewReviewers(PerformanceReview $performanceReview): void
    {
        $q = $this->createQueryBuilder(Reviewer::class, 'reviewer');
        $q->delete()
            ->where('reviewer.review = :reviewId')
            ->setParameter('reviewId', $performanceReview->getId())
            ->getQuery()->execute();
    }

    /**
     * @param PerformanceReview $performanceReview
     * @return Kpi[]
     */
    public function getReviewKPI(PerformanceReview $performanceReview): array
    {
        $q = $this->createQueryBuilder(Kpi::class, 'kpi');
        $q->andWhere('kpi.jobTitle =:jobTitle')
            ->setParameter('jobTitle', $performanceReview->getJobTitle());
        return $q->getQuery()->execute();
    }

    /**
     * @param PerformanceReview $performanceReview
     * @return Reviewer
     */
    private function getPerformanceSelfReviewer(PerformanceReview $performanceReview): Reviewer
    {
        $reviewer = $this->getRepository(Reviewer::class)->findOneBy(['review' => $performanceReview->getId(), 'employee' => $performanceReview->getEmployee()]);
        $q = $this->createQueryBuilder(Reviewer::class, 'reviewer');
        $q->andWhere('reviewer.review = :reviewId')
            ->setParameter('reviewId', $performanceReview->getId())
            ->andWhere('reviewer.employee =:employeeId')
            ->setParameter('employeeId', $performanceReview->getEmployee()->getEmployeeId());
        return $reviewer;
    }
}
