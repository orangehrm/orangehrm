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

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Kpi;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Entity\ReportTo;
use OrangeHRM\Entity\Reviewer;
use OrangeHRM\Entity\ReviewerGroup;
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
     * @param int $reviewerId
     * @return PerformanceReview
     */
    public function createReview(PerformanceReview $performanceReview, int $reviewerId): PerformanceReview
    {
        $this->persist($performanceReview);
        $this->saveReviewer($performanceReview, 'Supervisor', $reviewerId);
        $this->saveReviewer($performanceReview, 'Employee', null);
        return $performanceReview;
    }

    /**
     * @param PerformanceReview $performanceReview
     * @param string $reviewerGroupName
     * @param int|null $reviewerId
     * @return void
     */
    private function saveReviewer(PerformanceReview $performanceReview, string $reviewerGroupName, ?int $reviewerId)
    {
        $reviewer = new Reviewer();
        if (! is_null($reviewerId)) {
            $reviewer->getDecorator()->setEmployeeByEmpNumber($reviewerId);
        } else {
            $reviewer->setEmployee($performanceReview->getEmployee());
        }
        $reviewer->setStatus(1);
        $reviewerGroup = $this->getRepository(ReviewerGroup::class)->findOneBy(['name' => $reviewerGroupName]);
        $reviewer->setGroup($reviewerGroup);
        $reviewer->setReview($performanceReview);
        $this->persist($reviewer);
    }

    /**
     * @param int $id
     * @return PerformanceReview|null
     */
    public function getReviewById(int $id): ?PerformanceReview
    {
        $review = $this->getRepository(PerformanceReview::class)->findOneBy(['id'=>$id]);
        if ($review instanceof PerformanceReview) {
            return $review;
        }
        return null;
    }

    /**
     * @param PerformanceReview $performanceReview
     * @param int $reviewerId
     * @return PerformanceReview
     */
    public function updateReview(PerformanceReview $performanceReview): PerformanceReview
    {
        $this->persist($performanceReview);
        return $performanceReview;
    }

    public function getReviewKPI(PerformanceReview $performanceReview)
    {
        $q = $this->createQueryBuilder(Kpi::class, 'kpi');
        $q->andWhere('kpi.jobTitle =:jobTitle')
            ->setParameter('jobTitle', $performanceReview->getJobTitle());
        return $q->getQuery()->execute();
    }
}
