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
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Entity\Reviewer;
use OrangeHRM\Performance\Dto\PerformanceReviewSearchFilterParams;

class PerformanceReviewDao extends BaseDao
{
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
     * @return \OrangeHRM\ORM\QueryBuilderWrapper
     */
    private function getPerformanceReviewQueryBuilderWrapper(PerformanceReviewSearchFilterParams $performanceReviewSearchFilterParams)
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
     * @return Reviewer
     */
    private function getPerformanceSelfReviewer(PerformanceReview $performanceReview): Reviewer
    {
        $reviewer = $this->getRepository(Reviewer::class)->findOneBy(['review'=>$performanceReview->getId(),'employee'=>$performanceReview->getEmployee()]);
        $q = $this->createQueryBuilder(Reviewer::class, 'reviewer');
        $q->andWhere('reviewer.review = :reviewId')
            ->setParameter('reviewId', $performanceReview->getId())
            ->andWhere('reviewer.employee =:employeeId')
            ->setParameter('employeeId', $performanceReview->getEmployee()->getEmployeeId());
        return $reviewer;
    }
}
