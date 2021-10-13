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

namespace OrangeHRM\Core\Dao;

use OrangeHRM\Entity\CompositeDisplayField;
use OrangeHRM\Entity\DisplayField;
use OrangeHRM\Entity\DisplayFieldGroup;
use OrangeHRM\Entity\Report;
use OrangeHRM\Entity\SelectedFilterField;
use OrangeHRM\Entity\SummaryDisplayField;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Pim\Dto\PimDefinedReportSearchFilterParams;

class ReportGeneratorDao extends BaseDao
{
    /**
     * @param int $reportId
     * @return DisplayField[]
     */
    public function getSelectedDisplayFieldsByReportId(int $reportId): array
    {
        $q = $this->createQueryBuilder(DisplayField::class, 'df');
        $q->leftJoin('df.selectedDisplayFields', 'sdf');
        $q->leftJoin('df.displayFieldGroup', 'dfg');
        $q->andWhere('sdf.report = :reportId')
            ->setParameter('reportId', $reportId);
        return $q->getQuery()->execute();
    }

    /**
     * @param int $reportId
     * @return int[]
     */
    public function getSelectedDisplayFieldGroupIdsByReportId(int $reportId): array
    {
        $q = $this->createQueryBuilder(DisplayFieldGroup::class, 'dfg');
        $q->leftJoin('dfg.selectedDisplayFieldGroups', 'sdfg');
        $q->select('dfg.id');
        $q->andWhere('sdfg.report = :reportId')
            ->setParameter('reportId', $reportId);
        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'id');
    }

    /**
     * @param int $reportId
     * @return CompositeDisplayField[]
     */
    public function getSelectedCompositeDisplayFieldsByReportId(int $reportId): array
    {
        $q = $this->createQueryBuilder(CompositeDisplayField::class, 'cdf');
        $q->leftJoin('cdf.selectedCompositeDisplayFields', 'scdf');
        $q->andWhere('scdf.report = :reportId')
            ->setParameter('reportId', $reportId);
        return $q->getQuery()->execute();
    }

    /**
     * @param int $reportId
     * @return SelectedFilterField[]
     */
    public function getSelectedFilterFieldsByReportId(int $reportId): array
    {
        $q = $this->createQueryBuilder(SelectedFilterField::class, 'sff');
        $q->andWhere('sff.report = :reportId')
            ->setParameter('reportId', $reportId);
        return $q->getQuery()->execute();
    }

    /**
     * @param int $reportId
     * @return Report|null
     */
    public function getReport(int $reportId): ?Report
    {
        return $this->getRepository(Report::class)->find($reportId);
    }

    /**
     * @param int $reportId
     * @return SummaryDisplayField[]
     */
    public function getSummaryDisplayFieldByReportId(int $reportId): array
    {
        $q = $this->createQueryBuilder(SummaryDisplayField::class, 'sdf');
        $q->leftJoin('sdf.selectedGroupFields', 'sgf');
        $q->andWhere('sgf.report = :reportId')
            ->setParameter('reportId', $reportId);
        return $q->getQuery()->execute();
    }

    /**
     * @param PimDefinedReportSearchFilterParams $pimDefinedReportSearchFilterParams
     * @return Report[]
     */
    public function searchPimDefinedReports(PimDefinedReportSearchFilterParams $pimDefinedReportSearchFilterParams
    ): array {
        $paginator = $this->getSearchPimDefinedReportsPaginator($pimDefinedReportSearchFilterParams);
        return $paginator->getQuery()->execute();
    }

    /**
     * @param PimDefinedReportSearchFilterParams $pimDefinedReportSearchFilterParams
     * @return Paginator
     */
    private function getSearchPimDefinedReportsPaginator(
        PimDefinedReportSearchFilterParams $pimDefinedReportSearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(Report::class, 'report');
        $q->leftJoin('report.reportGroup', 'rg');
        $this->setSortingAndPaginationParams($q, $pimDefinedReportSearchFilterParams);

        if (!empty($pimDefinedReportSearchFilterParams->getName())) {
            $q->andWhere($q->expr()->like('report.name', ':reportName'));
            $q->setParameter('reportName', '%' . $pimDefinedReportSearchFilterParams->getName() . '%');
        }
        $q->andWhere('rg.name = :reportGroupName');
        $q->setParameter('reportGroupName', 'pim');
        return $this->getPaginator($q);
    }

    /**
     * @param PimDefinedReportSearchFilterParams $pimDefinedReportSearchFilterParams
     * @return int
     */
    public function getSearchPimDefinedReportCount(
        PimDefinedReportSearchFilterParams $pimDefinedReportSearchFilterParams
    ): int {
        $paginator = $this->getSearchPimDefinedReportsPaginator($pimDefinedReportSearchFilterParams);
        return $paginator->count();
    }

    /**
     * @param int[] $deletedIds
     * @return int
     */
    public function deletePimDefinedReport(array $deletedIds): int
    {
        $q = $this->createQueryBuilder(Report::class, 'report');
        $q->delete()
            ->where($q->expr()->in('report.id', ':ids'))
            ->setParameter('ids', $deletedIds);
        return $q->getQuery()->execute();
    }

    /**
     * @param int $reportId
     * @return Report|null
     */
    public function getReportById(int $reportId): ?Report
    {
        $report = $this->getRepository(Report::class)->find($reportId);
        return ($report instanceof Report) ? $report : null;
    }
}
