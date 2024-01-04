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

namespace OrangeHRM\Pim\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\ReportingMethod;
use OrangeHRM\Entity\ReportTo;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Pim\Dto\ReportingMethodSearchFilterParams;

class ReportingMethodConfigurationDao extends BaseDao
{
    /**
     * @param ReportingMethod $reportingMethod
     * @return ReportingMethod
     */
    public function saveReportingMethod(ReportingMethod $reportingMethod): ReportingMethod
    {
        $this->persist($reportingMethod);
        return $reportingMethod;
    }

    /**
     * @param int $id
     * @return ReportingMethod|null
     */
    public function getReportingMethodById(int $id): ?ReportingMethod
    {
        $reportingMethod = $this->getRepository(ReportingMethod::class)->find($id);
        if ($reportingMethod instanceof ReportingMethod) {
            return $reportingMethod;
        }
        return null;
    }

    /**
     * @param string $name
     * @return ReportingMethod|null
     */
    public function getReportingMethodByName(string $name): ?ReportingMethod
    {
        $query = $this->createQueryBuilder(ReportingMethod::class, 'rm');
        $trimmed = trim($name, ' ');
        $query->andWhere('rm.name = :name');
        $query->setParameter('name', $trimmed);
        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * @param ReportingMethodSearchFilterParams $reportingMethodSearchFilterParams
     * @return Paginator
     */
    public function getReportingMethodListPaginator(
        ReportingMethodSearchFilterParams $reportingMethodSearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(ReportingMethod::class, 'rm');
        $this->setSortingAndPaginationParams($q, $reportingMethodSearchFilterParams);
        return new Paginator($q);
    }

    /**
     * @param ReportingMethodSearchFilterParams $reportingMethodSearchFilterParams
     * @return int
     */
    public function getReportingMethodCount(ReportingMethodSearchFilterParams $reportingMethodSearchFilterParams): int
    {
        $paginator = $this->getReportingMethodListPaginator($reportingMethodSearchFilterParams);
        return $paginator->count();
    }

    /**
     * @param ReportingMethodSearchFilterParams $reportingMethodSearchFilterParams
     * @return int|mixed|string
     */
    public function getReportingMethodList(ReportingMethodSearchFilterParams $reportingMethodSearchFilterParams)
    {
        $paginator = $this->getReportingMethodListPaginator($reportingMethodSearchFilterParams);
        return $paginator->getQuery()->execute();
    }

    /**
     * @param $toDeleteIds
     * @return int
     */
    public function deleteReportingMethods($toDeleteIds): int
    {
        $q = $this->createQueryBuilder(ReportingMethod::class, 'rm');
        $q->delete();
        $q->where($q->expr()->in('rm.id', ':ids'))
            ->setParameter('ids', $toDeleteIds);
        return $q->getQuery()->execute();
    }

    /**
     * @param string $reportingMethodName
     * @return bool
     */
    public function isExistingReportingMethodName(string $reportingMethodName): bool
    {
        $q = $this->createQueryBuilder(ReportingMethod::class, 'rm');
        $trimmed = trim($reportingMethodName, ' ');
        $q->where('rm.name = :name');
        $q->setParameter('name', $trimmed);
        $count = $this->count($q);
        if ($count > 0) {
            return true;
        }
        return false;
    }

    /**
     * @return int[]
     */
    public function getReportingMethodIdsInUse(): array
    {
        $query = $this->createQueryBuilder(ReportTo::class, 'rt');
        $query->leftJoin('rt.reportingMethod', 'rm');
        $query->select('rm.id');
        $result = $query->getQuery()->getScalarResult();
        return array_column($result, 'id');
    }
}
