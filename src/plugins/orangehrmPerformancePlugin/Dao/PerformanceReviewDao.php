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

namespace OrangeHRM\Performance\Dao;

use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Kpi;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Entity\ReportTo;
use OrangeHRM\Entity\Reviewer;
use OrangeHRM\Entity\ReviewerGroup;
use OrangeHRM\Entity\ReviewerRating;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\ORM\QueryBuilderWrapper;
use OrangeHRM\Performance\Dto\PerformanceReviewSearchFilterParams;
use OrangeHRM\Performance\Dto\ReviewEmployeeSupervisorSearchFilterParams;
use OrangeHRM\Performance\Dto\ReviewKpiSearchFilterParams;
use OrangeHRM\Performance\Dto\SupervisorEvaluationSearchFilterParams;
use OrangeHRM\Performance\Traits\Service\PerformanceReviewServiceTrait;

class PerformanceReviewDao extends BaseDao
{
    use PerformanceReviewServiceTrait;
    /**
     * @param ReviewEmployeeSupervisorSearchFilterParams $reviewEmployeeSupervisorSearchFilterParams
     * @return Employee[]
     */
    public function getEmployeeSupervisorList(ReviewEmployeeSupervisorSearchFilterParams $reviewEmployeeSupervisorSearchFilterParams): array
    {
        $query = $this->getEmployeeSupervisorQueryBuilderWrapper($reviewEmployeeSupervisorSearchFilterParams)->getQueryBuilder();
        return $query->getQuery()->execute();
    }

    /**
     * @param ReviewEmployeeSupervisorSearchFilterParams $reviewEmployeeSupervisorSearchFilterParams
     * @return int
     */
    public function getEmployeeSupervisorCount(ReviewEmployeeSupervisorSearchFilterParams $reviewEmployeeSupervisorSearchFilterParams): int
    {
        $query = $this->getEmployeeSupervisorQueryBuilderWrapper($reviewEmployeeSupervisorSearchFilterParams)->getQueryBuilder();
        return $this->getPaginator($query)->count();
    }

    /**
     * @param ReviewEmployeeSupervisorSearchFilterParams $reviewEmployeeSupervisorSearchFilterParams
     * @return QueryBuilderWrapper
     */
    private function getEmployeeSupervisorQueryBuilderWrapper(ReviewEmployeeSupervisorSearchFilterParams $reviewEmployeeSupervisorSearchFilterParams): QueryBuilderWrapper
    {
        $qb = $this->createQueryBuilder(ReportTo::class, 'rt');
        $qb->leftJoin('rt.supervisor', 'employee')
            ->andWhere('rt.subordinate = :empNumber')
            ->setParameter('empNumber', $reviewEmployeeSupervisorSearchFilterParams->getEmpNumber())
            ->andWhere($qb->expr()->isNull('employee.employeeTerminationRecord'));
        if (! is_null($reviewEmployeeSupervisorSearchFilterParams->getNameOrId())) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('employee.firstName', ':nameOrId'),
                    $qb->expr()->like('employee.lastName', ':nameOrId'),
                    $qb->expr()->like('employee.middleName', ':nameOrId'),
                    $qb->expr()->like('employee.employeeId', ':nameOrId'),
                )
            );
            $qb->setParameter('nameOrId', '%'.$reviewEmployeeSupervisorSearchFilterParams->getNameOrId().'%');
        }
        $this->setSortingAndPaginationParams($qb, $reviewEmployeeSupervisorSearchFilterParams);
        return $this->getQueryBuilderWrapper($qb);
    }

    /**
     * @param PerformanceReview $performanceReview
     * @param int $reviewerEmpNumber
     * @return PerformanceReview
     */
    public function createReview(PerformanceReview $performanceReview, int $reviewerEmpNumber): PerformanceReview
    {
        $this->beginTransaction();
        try {
            $this->persist($performanceReview);
            $this->saveReviewer($performanceReview, ReviewerGroup::REVIEWER_GROUP_SUPERVISOR, $reviewerEmpNumber);
            $this->saveReviewer($performanceReview, ReviewerGroup::REVIEWER_GROUP_EMPLOYEE, null);
            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }
        return $performanceReview;
    }

    /**
     * @param PerformanceReview $performanceReview
     * @param string $reviewerGroupName
     * @param int|null $reviewerEmpNumber
     */
    private function saveReviewer(PerformanceReview $performanceReview, string $reviewerGroupName, ?int $reviewerEmpNumber)
    {
        $reviewer = new Reviewer();
        if (!is_null($reviewerEmpNumber)) {
            $reviewer->getDecorator()->setEmployeeByEmpNumber($reviewerEmpNumber);
        } else {
            $reviewer->setEmployee($performanceReview->getEmployee());
        }
        $reviewerGroup = $this->getRepository(ReviewerGroup::class)->findOneBy(['name' => $reviewerGroupName]);
        $reviewer->setStatus(Reviewer::STATUS_ACTIVATED);
        $reviewer->setGroup($reviewerGroup);
        $reviewer->setReview($performanceReview);
        $this->persist($reviewer);

        if ($performanceReview->getStatusId() == PerformanceReview::STATUS_ACTIVATED) {
            $this->saveReviewerRating($performanceReview, $reviewerGroup);
        }
    }

    /**
     * @param PerformanceReview $performanceReview
     * @param ReviewerGroup $reviewerGroup
     */
    private function saveReviewerRating(PerformanceReview $performanceReview, ReviewerGroup $reviewerGroup): void
    {
        $reviewer = $this->getReviewerRecord($performanceReview->getId(), $reviewerGroup->getName());
        $jobTitleId = $this->getReviewById($performanceReview->getId())->getJobTitle()->getId();
        $kpiIdArrayForJobTitle = $this->getKpiIdsForJobTitleId($jobTitleId);

        foreach ($kpiIdArrayForJobTitle as $kpiId) {
            $reviewerRating = new ReviewerRating();
            $reviewerRating->setPerformanceReview($performanceReview);
            $reviewerRating->getDecorator()->setKpiByKpiId($kpiId);
            $reviewerRating->setReviewer($reviewer);
            $this->persist($reviewerRating);
        }
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
     * @param int $id
     * @return PerformanceReview|null
     */
    public function getPerformanceReviewById(int $id): ?PerformanceReview
    {
        return $this->getRepository(PerformanceReview::class)->findOneBy(['id' => $id]);
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
        $qb = $this->createQueryBuilder(PerformanceReview::class, 'performanceReview');
        $qb->leftJoin('performanceReview.employee', 'employee');
        $qb->leftJoin('performanceReview.reviewers', 'reviewer');
        $qb->leftJoin('reviewer.employee', 'reviewerEmployee');
        $qb->leftJoin('reviewer.group', 'reviewGroup');
        $qb->leftJoin('performanceReview.jobTitle', 'jobTitle');
        $qb->leftJoin('performanceReview.subunit', 'subunit');

        $qb->andWhere($qb->expr()->eq('reviewGroup.name', ':reviewGroupName'))
            ->setParameter('reviewGroupName', ReviewerGroup::REVIEWER_GROUP_SUPERVISOR);

        if (!is_null($performanceReviewSearchFilterParams->getReviewerEmpNumber())) {
            $qb->andWhere($qb->expr()->eq('reviewerEmployee.empNumber', ':supervisorEmpNumber'))
                ->setParameter('supervisorEmpNumber', $performanceReviewSearchFilterParams->getReviewerEmpNumber());
        }

        if (!is_null($performanceReviewSearchFilterParams->getEmpNumber())) {
            $qb->andWhere($qb->expr()->eq('performanceReview.employee', ':empNumber'))
                ->setParameter('empNumber', $performanceReviewSearchFilterParams->getEmpNumber());
        }

        if (!is_null($performanceReviewSearchFilterParams->getStatusId())) {
            $qb->andWhere($qb->expr()->eq('performanceReview.statusId', ':statusId'))
                ->setParameter('statusId', $performanceReviewSearchFilterParams->getStatusId());
        } elseif ($performanceReviewSearchFilterParams->isExcludeInactiveReviews()) {
            $qb->andWhere($qb->expr()->neq('performanceReview.statusId', ':statusId'))
                ->setParameter('statusId', PerformanceReview::STATUS_INACTIVE);
        }

        if (!is_null($performanceReviewSearchFilterParams->getFromDate())) {
            $qb->andWhere($qb->expr()->gte('performanceReview.dueDate', ':fromDate'))
                ->setParameter('fromDate', $performanceReviewSearchFilterParams->getFromDate());
        }

        if (!is_null($performanceReviewSearchFilterParams->getToDate())) {
            $qb->andWhere($qb->expr()->lte('performanceReview.dueDate', ':toDate'))
                ->setParameter('toDate', $performanceReviewSearchFilterParams->getToDate());
        }

        if (!is_null($performanceReviewSearchFilterParams->getJobTitleId())) {
            $qb->andWhere($qb->expr()->eq('jobTitle.id', ':jobTitleId'))
                ->setParameter('jobTitleId', $performanceReviewSearchFilterParams->getJobTitleId());
        }

        if (!is_null($performanceReviewSearchFilterParams->getSubunitId())) {
            $qb->andWhere($qb->expr()->in('subunit.id', ':subunitId'))
                ->setParameter('subunitId', $performanceReviewSearchFilterParams->getSubunitIdChain());
        }

        if (is_null($performanceReviewSearchFilterParams->getIncludeEmployees()) ||
            $performanceReviewSearchFilterParams->getIncludeEmployees() ===
            PerformanceReviewSearchFilterParams::INCLUDE_EMPLOYEES_ONLY_CURRENT
        ) {
            $qb->andWhere($qb->expr()->isNull('employee.employeeTerminationRecord'));
        } elseif ($performanceReviewSearchFilterParams->getIncludeEmployees() ===
            PerformanceReviewSearchFilterParams::INCLUDE_EMPLOYEES_ONLY_PAST) {
            $qb->andWhere($qb->expr()->isNotNull('employee.employeeTerminationRecord'));
        }

        $qb->andWhere($qb->expr()->isNull('employee.purgedAt'));

        $this->setSortingAndPaginationParams($qb, $performanceReviewSearchFilterParams);
        $qb->addOrderBy('performanceReview.dueDate', ListSorter::DESCENDING);
        $qb->addOrderBy('employee.lastName');
        $qb->addOrderBy('performanceReview.id');
        return $this->getQueryBuilderWrapper($qb);
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
                return 'In Progress';
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
     * @throws TransactionException
     */
    public function updateReview(PerformanceReview $performanceReview, int $reviewerEmpNumber): PerformanceReview
    {
        $this->beginTransaction();
        try {
            $this->deletePerformanceReviewReviewers($performanceReview);
            $this->persist($performanceReview);
            $this->saveReviewer($performanceReview, ReviewerGroup::REVIEWER_GROUP_SUPERVISOR, $reviewerEmpNumber);
            $this->saveReviewer($performanceReview, ReviewerGroup::REVIEWER_GROUP_EMPLOYEE, null);
            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }
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
     * @param int $subordinateId
     * @param int $supervisorId
     * @return ReportTo[]
     */
    public function getSupervisorRecord(int $subordinateId, int $supervisorId): array
    {
        $qb = $this->createQueryBuilder(ReportTo::class, 'rt');
        $qb->leftJoin('rt.supervisor', 'employee')
            ->andWhere('rt.supervisor = :supervisorId')
            ->setParameter('supervisorId', $supervisorId)
            ->andWhere('rt.subordinate = :subordinateId')
            ->setParameter('subordinateId', $subordinateId)
            ->andWhere($qb->expr()->isNull('employee.employeeTerminationRecord'));
        return $qb->getQuery()->execute();
    }

    /**
     * @param PerformanceReview $performanceReview
     * @return Kpi[]
     */
    public function getReviewKPI(PerformanceReview $performanceReview): array
    {
        $q = $this->createQueryBuilder(Kpi::class, 'kpi');
        $q->andWhere('kpi.jobTitle = :jobTitle')
            ->setParameter('jobTitle', $performanceReview->getJobTitle());
        return $q->getQuery()->execute();
    }

    /**
     * @param PerformanceReview $performanceReview
     * @return Reviewer
     */
    public function getPerformanceSelfReviewer(PerformanceReview $performanceReview): Reviewer
    {
        $reviewer = $this->getRepository(Reviewer::class)->findOneBy(['review' => $performanceReview->getId(), 'employee' => $performanceReview->getEmployee()]);
        $q = $this->createQueryBuilder(Reviewer::class, 'reviewer');
        $q->andWhere('reviewer.review = :reviewId')
            ->setParameter('reviewId', $performanceReview->getId())
            ->andWhere('reviewer.employee = :employeeId')
            ->setParameter('employeeId', $performanceReview->getEmployee()->getEmployeeId());
        return $reviewer;
    }

    /**
     * @param array $performanceReviewIds
     * @return int
     */
    public function deletePerformanceReviews(array $performanceReviewIds): int
    {
        $qb = $this->createQueryBuilder(PerformanceReview::class, 'performanceReview');
        $qb->delete()
            ->andWhere($qb->expr()->in('performanceReview.id', ':performanceReviewIds'))
            ->setParameter('performanceReviewIds', $performanceReviewIds);
        return $qb->getQuery()->execute();
    }

    /**
     * @param int $id
     * @return PerformanceReview|null
     */
    public function getReviewById(int $id): ?PerformanceReview
    {
        return $this->getRepository(PerformanceReview::class)->findOneBy(['id' => $id]);
    }

    /**
     * @param int[] $ids
     * @return int[]
     */
    public function getExistingPerformanceReviewIds(array $ids): array
    {
        $qb = $this->createQueryBuilder(PerformanceReview::class, 'performanceReview');

        $qb->select('performanceReview.id')
            ->andWhere($qb->expr()->in('performanceReview.id', ':ids'))
            ->setParameter('ids', $ids);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param ReviewKpiSearchFilterParams $reviewKpiSearchFilterParams
     * @return Kpi[]
     */
    public function getKpisForReview(ReviewKpiSearchFilterParams $reviewKpiSearchFilterParams): array
    {
        $qb = $this->getKpisForReviewQueryBuilderWrapper($reviewKpiSearchFilterParams)->getQueryBuilder();
        return $qb->getQuery()->execute();
    }

    /**
     * @param ReviewKpiSearchFilterParams $reviewKpiSearchFilterParams
     * @return int
     */
    public function getKpisForReviewCount(ReviewKpiSearchFilterParams $reviewKpiSearchFilterParams): int
    {
        $qb = $this->getKpisForReviewQueryBuilderWrapper($reviewKpiSearchFilterParams)->getQueryBuilder();
        return $this->getPaginator($qb)->count();
    }

    /**
     * @param ReviewKpiSearchFilterParams $reviewKpiSearchFilterParams
     * @return QueryBuilderWrapper
     */
    private function getKpisForReviewQueryBuilderWrapper(
        ReviewKpiSearchFilterParams $reviewKpiSearchFilterParams
    ): QueryBuilderWrapper {
        $qb = $this->createQueryBuilder(ReviewerRating::class, 'reviewerRating');
        $qb->leftJoin('reviewerRating.performanceReview', 'performanceReview')
            ->leftJoin('reviewerRating.reviewer', 'reviewer')
            ->leftJoin('reviewerRating.kpi', 'kpi')
            ->leftJoin('reviewer.group', 'reviewerGroup')
            ->andWhere('performanceReview.id = :reviewId')
            ->setParameter('reviewId', $reviewKpiSearchFilterParams->getReviewId())
            ->andWhere('reviewerGroup.name = :groupName')
            ->setParameter('groupName', $reviewKpiSearchFilterParams->getReviewerGroupName());
        $this->setSortingAndPaginationParams($qb, $reviewKpiSearchFilterParams);
        return $this->getQueryBuilderWrapper($qb);
    }

    /**
     * @param PerformanceReview $performanceReview
     * @return PerformanceReview
     */
    public function savePerformanceReview(PerformanceReview $performanceReview): PerformanceReview
    {
        $this->persist($performanceReview);
        return $performanceReview;
    }

    /**
     * @param int $reviewId
     */
    public function setReviewerStatusToCompleted(int $reviewId): void
    {
        $qb = $this->createQueryBuilder(Reviewer::class, 'reviewer');
        $qb->update()
            ->set('reviewer.status', ':status')
            ->setParameter('status', Reviewer::STATUS_COMPLETED)
            ->andWhere($qb->expr()->eq('reviewer.review', ':reviewId'))
            ->setParameter('reviewId', $reviewId);

        $qb->getQuery()->execute();
    }

    /**
     * @param int $supervisorEmpNumber
     * @return int[]
     */
    public function getReviewIdsForSupervisorReviewer(int $supervisorEmpNumber): array
    {
        $q = $this->createQueryBuilder(Reviewer::class, 'reviewer');
        $q->leftJoin('reviewer.review', 'performanceReview');
        $q->leftJoin('reviewer.group', 'reviewGroup');
        $q->andWhere('reviewer.employee = :supervisor')
            ->setParameter('supervisor', $supervisorEmpNumber);
        $q->andWhere($q->expr()->neq('performanceReview.statusId', ':statusId'))
            ->setParameter('statusId', PerformanceReview::STATUS_INACTIVE);
        $q->andWhere($q->expr()->eq('reviewGroup.name', ':groupName'))
            ->setParameter('groupName', ReviewerGroup::REVIEWER_GROUP_SUPERVISOR);
        $purgedEmployeeReviewIds = $this->getPurgeEmployeeReviewIds();
        if (! empty($purgedEmployeeReviewIds)) {
            $q->andWhere($q->expr()->notIn('performanceReview.id', ':purgedRecords'))
                ->setParameter('purgedRecords', $purgedEmployeeReviewIds);
        }
        /** @var Reviewer[] $reviewers */
        $reviewers = $q->getQuery()->execute();

        $reviewIds = [];

        foreach ($reviewers as $reviewer) {
            $reviewIds[] = $reviewer->getReview()->getId();
        }
        return $reviewIds;
    }

    /**
     * @return array
     */
    private function getPurgeEmployeeReviewIds(): array
    {
        $q = $this->createQueryBuilder(PerformanceReview::class, 'review');
        $q->select('review.id')
            ->leftJoin('review.employee', 'employee')
            ->andWhere($q->expr()->isNotNull('employee.purgedAt'));
        return array_column($q->getQuery()->getArrayResult(), 'id');
    }

    /**
     * @return int[]
     */
    public function getReviewIdList(): array
    {
        $qb = $this->createQueryBuilder(PerformanceReview::class, 'performanceReview');
        $qb->select('performanceReview.id')
            ->leftJoin('performanceReview.employee', 'employee')
            ->andWhere($qb->expr()->isNull('employee.purgedAt'));
        return array_column($qb->getQuery()->getArrayResult(), 'id');
    }

    /**
     * @param int $employeeNumber
     * @param array|null $allowedStatuses
     * @return int[]
     */
    public function getSelfReviewIds(int $employeeNumber, ?array $allowedStatuses = []): array
    {
        $q = $this->createQueryBuilder(PerformanceReview::class, 'performanceReview');
        $q->select('performanceReview.id');
        $q->andWhere($q->expr()->eq('performanceReview.employee', ':empNumber'))
            ->setParameter('empNumber', $employeeNumber);
        if (empty($allowedStatuses)) {
            $q->andWhere($q->expr()->neq('performanceReview.statusId', ':statusId'))
                ->setParameter('statusId', PerformanceReview::STATUS_INACTIVE);
        } else {
            $q->andWhere($q->expr()->in('performanceReview.statusId', ':statuses'))
                ->setParameter('statuses', $allowedStatuses);
        }

        return array_column($q->getQuery()->getArrayResult(), 'id');
    }

    /**
     * @param SupervisorEvaluationSearchFilterParams $supervisorEvaluationSearchFilterParams
     * @return ReviewerRating[]
     */
    public function getReviewerRating(
        SupervisorEvaluationSearchFilterParams $supervisorEvaluationSearchFilterParams,
        string $reviewerGroupName
    ): array {
        $reviewerGroup = $this->getRepository(ReviewerGroup::class)->findOneBy(['name' => $reviewerGroupName]);
        $qb = $this->getEvaluationRatingQueryBuilderWrapper(
            $supervisorEvaluationSearchFilterParams,
            $reviewerGroup
        )->getQueryBuilder();
        return $qb->getQuery()->execute();
    }

    /**
     * @param SupervisorEvaluationSearchFilterParams $supervisorEvaluationSearchFilterParams
     * @param string $reviewerGroupName
     * @return int
     */
    public function getReviewerRatingCount(
        SupervisorEvaluationSearchFilterParams $supervisorEvaluationSearchFilterParams,
        string $reviewerGroupName
    ): int {
        $reviewerGroup = $this->getRepository(ReviewerGroup::class)->findOneBy(['name' => $reviewerGroupName]);
        $qb = $this->getEvaluationRatingQueryBuilderWrapper(
            $supervisorEvaluationSearchFilterParams,
            $reviewerGroup
        )->getQueryBuilder();
        return $this->getPaginator($qb)->count();
    }

    /**
     * @param SupervisorEvaluationSearchFilterParams $supervisorEvaluationSearchFilterParams
     * @param ReviewerGroup $reviewerGroup
     * @return QueryBuilderWrapper
     */
    private function getEvaluationRatingQueryBuilderWrapper(
        SupervisorEvaluationSearchFilterParams $supervisorEvaluationSearchFilterParams,
        ReviewerGroup                          $reviewerGroup
    ): QueryBuilderWrapper {
        $qb = $this->createQueryBuilder(ReviewerRating::class, 'reviewerRating');
        $qb->leftJoin('reviewerRating.performanceReview', 'performanceReview')
            ->leftJoin('reviewerRating.reviewer', 'reviewer')
            ->andWhere('performanceReview.id = :reviewId')
            ->setParameter('reviewId', $supervisorEvaluationSearchFilterParams->getReviewId())
            ->andWhere('reviewer.group = :group')
            ->setParameter('group', $reviewerGroup);
        $this->setSortingAndPaginationParams($qb, $supervisorEvaluationSearchFilterParams);
        return $this->getQueryBuilderWrapper($qb);
    }

    /**
     * @param int $reviewId
     * @return array
     */
    public function getKpiIdsForReviewId(int $reviewId): array
    {
        $qb = $this->createQueryBuilder(ReviewerRating::class, 'reviewerRating');
        $qb->select('kpi.id')
            ->leftJoin('reviewerRating.performanceReview', 'performanceReview')
            ->leftJoin('reviewerRating.reviewer', 'reviewer')
            ->leftJoin('reviewerRating.kpi', 'kpi')
            ->leftJoin('reviewer.group', 'reviewerGroup')
            ->andWhere('performanceReview.id = :reviewId')
            ->setParameter('reviewId', $reviewId)
            ->andWhere('reviewerGroup.name = :groupName')
            ->setParameter('groupName', ReviewerGroup::REVIEWER_GROUP_SUPERVISOR);

        return array_column($qb->getQuery()->execute(), 'id');
    }

    /**
     * @param int $jobTitleId
     * @return array
     */
    public function getKpiIdsForJobTitleId(int $jobTitleId): array
    {
        $q = $this->createQueryBuilder(Kpi::class, 'kpi');
        $q->select('kpi.id')
            ->andWhere('kpi.jobTitle =:jobTitle')
            ->setParameter('jobTitle', $jobTitleId);
        $q->andWhere($q->expr()->isNull('kpi.deletedAt'));
        return array_column($q->getQuery()->execute(), 'id');
    }

    /**
     * @param array $reviewerRatings
     * @return void
     */
    public function saveAndUpdateReviewerRatings(array $reviewerRatings): void
    {
        $q = $this->createQueryBuilder(ReviewerRating::class, 'reviewerRating');

        foreach (array_values($reviewerRatings) as $i => $reviewerRating) {
            $reviewerIdParamKey = 'reviewerId_' . $i;
            $performanceReviewIdParamKey = 'performanceReviewId_' . $i;
            $kpiIdParamKey = 'kpiId_' . $i;

            /** @var ReviewerRating $reviewerRating */

            $reviewerId = $reviewerRating->getReviewer()->getId();
            $performanceReviewId = $reviewerRating->getPerformanceReview()->getId();
            $kpiId = $reviewerRating->getKpi()->getId();

            $q->orWhere(
                $q->expr()->andX(
                    $q->expr()->eq('reviewerRating.reviewer', ':' . $reviewerIdParamKey),
                    $q->expr()->eq('reviewerRating.performanceReview', ':' . $performanceReviewIdParamKey),
                    $q->expr()->eq('reviewerRating.kpi', ':' . $kpiIdParamKey),
                )
            );
            $q->setParameter($reviewerIdParamKey, $reviewerId)
                ->setParameter($performanceReviewIdParamKey, $performanceReviewId)
                ->setParameter($kpiIdParamKey, $kpiId);
        }

        /** @var array<string, ReviewerRating> $updatableReviewerRatings */
        $updatableReviewerRatings = [];
        foreach ($q->getQuery()->execute() as $updatableReviewerRating) {
            /** @var ReviewerRating $updatableReviewerRating */
            $itemKey = $this->getPerformanceReviewService()->generateReviewReviewerRatingKey(
                $updatableReviewerRating->getReviewer()->getId(),
                $updatableReviewerRating->getPerformanceReview()->getId(),
                $updatableReviewerRating->getKpi()->getId()
            );
            $updatableReviewerRatings[$itemKey] = $updatableReviewerRating;
        }

        foreach ($reviewerRatings as $key => $reviewerRating) {
            if (isset($updatableReviewerRatings[$key])) {
                $updatableReviewerRatings[$key]->setRating($reviewerRating->getRating());
                $updatableReviewerRatings[$key]->setComment($reviewerRating->getComment());

                $this->getEntityManager()->persist($updatableReviewerRatings[$key]);
                continue;
            } else {
                $this->getEntityManager()->persist($reviewerRating);
            }
        }
        $this->getEntityManager()->flush();
    }

    /**
     * @param int $performanceReviewId
     * @param string $reviewerGroupName
     * @return Reviewer
     */
    public function getReviewerRecord(int $performanceReviewId, string $reviewerGroupName): Reviewer
    {
        $reviewerGroup = $this->getRepository(ReviewerGroup::class)->findOneBy(
            ['name' => $reviewerGroupName]
        );

        return $this->getRepository(Reviewer::class)->findOneBy(
            ['review' => $performanceReviewId, 'group' => $reviewerGroup]
        );
    }

    /**
     * @param PerformanceReview $performanceReview
     * @param string $reviewerGroupName
     * @param int $status
     */
    public function updateReviewerStatus(
        PerformanceReview $performanceReview,
        string $reviewerGroupName,
        int $status
    ): void {
        /** @var ReviewerGroup $reviewerGroup */
        $reviewerGroup = $this->getRepository(ReviewerGroup::class)->findOneBy(['name' => $reviewerGroupName]);
        $qb = $this->createQueryBuilder(Reviewer::class, 'reviewer');

        $qb->update()
            ->set('reviewer.status', ':status')
            ->setParameter('status', $status)
            ->andWhere($qb->expr()->eq('reviewer.review', ':reviewId'))
            ->setParameter('reviewId', $performanceReview->getId())
            ->andWhere('reviewer.group = :reviewerGroup')
            ->setParameter('reviewerGroup', $reviewerGroup->getId());

        $qb->getQuery()->execute();
    }

    /**
     * @param PerformanceReview $performanceReview
     * @param string $reviewerGroupName
     * @param string $comment
     */
    public function updateReviewerComment(
        PerformanceReview $performanceReview,
        string $reviewerGroupName,
        string $comment
    ): void {
        /** @var ReviewerGroup $reviewerGroup */
        $reviewerGroup = $this->getRepository(ReviewerGroup::class)->findOneBy(['name' => $reviewerGroupName]);
        $qb = $this->createQueryBuilder(Reviewer::class, 'reviewer');

        $qb->update()
            ->set('reviewer.comment', ':comment')
            ->setParameter('comment', $comment)
            ->andWhere($qb->expr()->eq('reviewer.review', ':reviewId'))
            ->setParameter('reviewId', $performanceReview->getId())
            ->andWhere('reviewer.group = :reviewerGroup')
            ->setParameter('reviewerGroup', $reviewerGroup->getId());

        $qb->getQuery()->execute();
    }
}
