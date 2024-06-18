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

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Kpi;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Entity\ReviewerRating;
use OrangeHRM\ORM\QueryBuilderWrapper;
use OrangeHRM\Performance\Dto\KpiSearchFilterParams;

class KpiDao extends BaseDao
{
    use DateTimeHelperTrait;

    /**
     * @param Kpi $kpi
     * @return Kpi
     */
    public function saveKpi(Kpi $kpi): Kpi
    {
        $this->persist($kpi);
        return $kpi;
    }

    /**
     * @param int $id
     * @return Kpi|null
     */
    public function getKpiById(int $id): ?Kpi
    {
        $kpi = $this->getRepository(Kpi::class)->findOneBy(['id' => $id, 'deletedAt' => null]);
        if ($kpi instanceof Kpi) {
            return $kpi;
        }
        return null;
    }

    /**
     * @param int[] $ids
     * @return int[]
     */
    public function getExistingKpiIds(array $ids): array
    {
        $qb = $this->createQueryBuilder(Kpi::class, 'kpi');

        $qb->select('kpi.id')
            ->andWhere($qb->expr()->in('kpi.id', ':ids'))
            ->andWhere($qb->expr()->isNull('kpi.deletedAt'))
            ->setParameter('ids', $ids);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param KpiSearchFilterParams $kpiSearchFilterParams
     * @return Kpi[]
     */
    public function getKpiList(KpiSearchFilterParams $kpiSearchFilterParams): array
    {
        $qb = $this->getKpiQueryBuilderWrapper($kpiSearchFilterParams)->getQueryBuilder();
        return $qb->getQuery()->execute();
    }

    /**
     * @param KpiSearchFilterParams $kpiSearchFilterParams
     * @return int
     */
    public function getKpiCount(KpiSearchFilterParams $kpiSearchFilterParams): int
    {
        $qb = $this->getKpiQueryBuilderWrapper($kpiSearchFilterParams)->getQueryBuilder();
        return $this->getPaginator($qb)->count();
    }

    /**
     * @param KpiSearchFilterParams $kpiSearchFilterParams
     * @return QueryBuilderWrapper
     */
    private function getKpiQueryBuilderWrapper(KpiSearchFilterParams $kpiSearchFilterParams): QueryBuilderWrapper
    {
        $q = $this->createQueryBuilder(Kpi::class, 'kpi');
        $q->leftJoin('kpi.jobTitle', 'jobTitle');
        $q->andWhere($q->expr()->isNull('kpi.deletedAt'));
        $this->setSortingAndPaginationParams($q, $kpiSearchFilterParams);

        if (!is_null($kpiSearchFilterParams->getJobTitleId())) {
            $q->andWhere('jobTitle.id = :jobTitleId')
                ->andWhere('jobTitle.isDeleted = :isDeleted')
                ->setParameter('jobTitleId', $kpiSearchFilterParams->getJobTitleId())
                ->setParameter('isDeleted', false);
        }

        $q->addOrderBy('kpi.title');

        return $this->getQueryBuilderWrapper($q);
    }

    /**
     * @return Kpi|null
     */
    public function getDefaultKpi(): ?Kpi
    {
        return $this->getRepository(Kpi::class)->findOneBy(['defaultKpi' => true, 'deletedAt' => null]);
    }

    /**
     * @param int[] $toBeDeletedKpiIds
     * @return int
     */
    public function deleteKpi(array $toBeDeletedKpiIds): int
    {
        $qb = $this->createQueryBuilder(ReviewerRating::class, 'rating');
        $qb->select('kpi.id')
            ->leftJoin('rating.kpi', 'kpi')
            ->leftJoin('rating.performanceReview', 'review')
            ->andWhere('review.statusId > :inactiveStatus')
            ->setParameter('inactiveStatus', PerformanceReview::STATUS_INACTIVE)
            ->distinct('kpi.id');

        $nonDeletableKpiIds = array_column($qb->getQuery()->execute(), 'id');
        $q = $this->createQueryBuilder(Kpi::class, 'kpi');
        $q->update()
            ->set('kpi.deletedAt', ':deletedAt')
            ->setParameter('deletedAt', $this->getDateTimeHelper()->getNow())
            ->where($q->expr()->in('kpi.id', ':ids'))
            ->setParameter('ids', $toBeDeletedKpiIds);
        if (! empty($nonDeletableKpiIds)) {
            $q->andWhere($q->expr()->notIn('kpi.id', ':nonDeletableKpiIds'))
                ->setParameter('nonDeletableKpiIds', $nonDeletableKpiIds);
        }
        return $q->getQuery()->execute();
    }

    /**
     * @param int|null $id
     */
    public function unsetDefaultKpi(?int $id): void
    {
        $q = $this->createQueryBuilder(Kpi::class, 'kpi');
        $q->update()
            ->set('kpi.defaultKpi', ':newDefault')
            ->setParameter('newDefault', false)
            ->where($q->expr()->eq('kpi.defaultKpi', ':oldDefault'))
            ->setParameter('oldDefault', true);

        if ($id) {
            $q->andWhere($q->expr()->neq('kpi.id', ':id'))
                ->setParameter('id', $id);
        }

        $q->getQuery()->execute();
    }

    /**
     * @param int $kpiId
     * @return bool
     */
    public function isKpiEditable(int $kpiId): bool
    {
        $q = $this->createQueryBuilder(ReviewerRating::class, 'rating');
        $q->leftJoin('rating.performanceReview', 'review')
            ->andWhere('review.statusId > :activatedStatus')
            ->setParameter('activatedStatus', PerformanceReview::STATUS_ACTIVATED)
            ->andWhere('rating.kpi = :kpiId')
            ->setParameter('kpiId', $kpiId);
        return $this->getPaginator($q)->count() == 0;
    }

    /**
     * @param int $kpiId
     * @return bool
     */
    public function isKpiDeletable(int $kpiId): bool
    {
        $q = $this->createQueryBuilder(ReviewerRating::class, 'rating');
        $q->leftJoin('rating.performanceReview', 'review')
            ->andWhere('review.statusId > :inactiveStatus')
            ->setParameter('inactiveStatus', PerformanceReview::STATUS_INACTIVE)
            ->andWhere('rating.kpi = :kpiId')
            ->setParameter('kpiId', $kpiId);
        return $this->getPaginator($q)->count() == 0;
    }
}
