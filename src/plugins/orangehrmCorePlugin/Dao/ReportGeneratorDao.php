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

namespace OrangeHRM\Core\Dao;

use Exception;
use OrangeHRM\Entity\CompositeDisplayField;
use OrangeHRM\Entity\DisplayField;
use OrangeHRM\Entity\DisplayFieldGroup;
use OrangeHRM\Entity\FilterField;
use OrangeHRM\Entity\Report;
use OrangeHRM\Entity\ReportGroup;
use OrangeHRM\Entity\SelectedDisplayField;
use OrangeHRM\Entity\SelectedDisplayFieldGroup;
use OrangeHRM\Entity\SelectedFilterField;
use OrangeHRM\Entity\SummaryDisplayField;
use OrangeHRM\I18N\Traits\Service\I18NHelperTrait;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Pim\Dto\PimDefinedReportSearchFilterParams;

class ReportGeneratorDao extends BaseDao
{
    use I18NHelperTrait;

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
     * @param Report $report
     * @param int[] $selectedDisplayFieldGroupIds
     * @param int[] $selectedDisplayFieldIds
     * @param SelectedFilterField[] $selectedFilterFields
     * @return Report
     * @throws TransactionException
     */
    public function saveReport(
        Report $report,
        array $selectedDisplayFieldGroupIds,
        array $selectedDisplayFieldIds,
        array $selectedFilterFields
    ): Report {
        $this->beginTransaction();
        try {
            $this->persist($report);
            if (count($selectedDisplayFieldGroupIds) > 0) {
                $this->saveSelectedDisplayFieldGroup($report, $selectedDisplayFieldGroupIds);
            }
            $this->saveSelectedDisplayField($report, $selectedDisplayFieldIds);
            $this->saveSelectedFilterField($selectedFilterFields);
            $this->commitTransaction();
            return $report;
        } catch (Exception $exception) {
            $this->rollBackTransaction();
            throw new TransactionException($exception);
        }
    }

    /**
     * @param SelectedFilterField[] $selectedFilterFields
     */
    public function saveSelectedFilterField(array $selectedFilterFields): void
    {
        foreach ($selectedFilterFields as $selectedFilterField) {
            $this->getEntityManager()->persist($selectedFilterField);
        }
        $this->getEntityManager()->flush();
    }

    /**
     * @param Report $report
     * @param array $selectedDisplayFieldIds
     * @return void
     */
    public function saveSelectedDisplayField(Report $report, array $selectedDisplayFieldIds): void
    {
        foreach ($selectedDisplayFieldIds as $selectedDisplayFieldId) {
            $selectedDisplayField = new SelectedDisplayField();
            $selectedDisplayField->getDecorator()->setDisplayFieldById($selectedDisplayFieldId);
            $selectedDisplayField->setReport($report);
            $this->getEntityManager()->persist($selectedDisplayField);
        }
        $this->getEntityManager()->flush();
    }

    /**
     * @param Report $report
     * @param array $selectedDisplayFieldGroupIds
     * @return void
     */
    public function saveSelectedDisplayFieldGroup(Report $report, array $selectedDisplayFieldGroupIds): void
    {
        foreach ($selectedDisplayFieldGroupIds as $selectedDisplayFieldGroupId) {
            $selectedDisplayFieldGroup = new SelectedDisplayFieldGroup();
            $selectedDisplayFieldGroup->setReport($report);
            $selectedDisplayFieldGroup->getDecorator()->setDisplayFieldGroupById($selectedDisplayFieldGroupId);
            $this->getEntityManager()->persist($selectedDisplayFieldGroup);
        }
        $this->getEntityManager()->flush();
    }

    /**
     * @param string $name
     * @return FilterField|null
     */
    public function getFilterFieldByName(string $name): ?FilterField
    {
        $filterField = $this->getRepository(FilterField::class)->findOneBy(['name' => $name]);
        return ($filterField instanceof FilterField) ? $filterField : null;
    }

    /**
     * @param PimDefinedReportSearchFilterParams $pimDefinedReportSearchFilterParams
     * @return Report[]
     */
    public function searchPimDefinedReports(
        PimDefinedReportSearchFilterParams $pimDefinedReportSearchFilterParams
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
        if (!empty($pimDefinedReportSearchFilterParams->getReportId())) {
            $q->andWhere('report.id = :reportId');
            $q->setParameter('reportId', $pimDefinedReportSearchFilterParams->getReportId());
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
            ->andWhere($q->expr()->in('report.id', ':ids'))
            ->andWhere($q->expr()->eq('report.reportGroup', ':reportGroupId'))
            ->setParameter('ids', $deletedIds)
            ->setParameter('reportGroupId', ReportGroup::REPORT_GROUP_PIM);
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

    /**
     * @param int[] $ids
     * @return int[]
     */
    public function getExistingReportIdsForPim(array $ids): array
    {
        $qb = $this->createQueryBuilder(Report::class, 'report');

        $qb->select('report.id')
            ->andWhere($qb->expr()->in('report.id', ':ids'))
            ->andWhere($qb->expr()->eq('report.reportGroup', ':reportGroupId'))
            ->setParameter('ids', $ids)
            ->setParameter('reportGroupId', ReportGroup::REPORT_GROUP_PIM);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param string $reportGroupName
     * @return ReportGroup|null
     */
    public function getReportGroupByName(string $reportGroupName): ?ReportGroup
    {
        $reportGroup = $this->getRepository(ReportGroup::class)->findOneBy(['name' => $reportGroupName]);
        return ($reportGroup instanceof ReportGroup) ? $reportGroup : null;
    }

    /**
     * @param Report $report
     * @return void
     */
    public function deleteExistingReportRecordsByReportId(Report $report): void
    {
        $this->deleteSelectedDisplayFieldGroups($report);
        $this->deleteSelectedDisplayFields($report);
        $this->deleteSelectedFilterFields($report);
    }

    /**
     * @param Report $report
     * @return void
     */
    public function deleteSelectedDisplayFieldGroups(Report $report): void
    {
        $q = $this->createQueryBuilder(SelectedDisplayFieldGroup::class, 'sdfg');
        $q->delete()
            ->where('sdfg.report = :reportId')
            ->setParameter('reportId', $report->getId())
            ->getQuery()
            ->execute();
    }

    /**
     * @param Report $report
     * @return void
     */
    public function deleteSelectedDisplayFields(Report $report): void
    {
        $q = $this->createQueryBuilder(SelectedDisplayField::class, 'sdf');
        $q->delete()
            ->where('sdf.report = :reportId')
            ->setParameter('reportId', $report->getId())
            ->getQuery()
            ->execute();
    }

    /**
     * @param Report $report
     * @return void
     */
    public function deleteSelectedFilterFields(Report $report): void
    {
        $q = $this->createQueryBuilder(SelectedFilterField::class, 'sff');
        $q->delete()
            ->where('sff.report = :reportId')
            ->setParameter('reportId', $report->getId())
            ->getQuery()
            ->execute();
    }

    /**
     * @param int $reportId
     * @return int[]
     */
    public function getSelectedDisplayFieldIdsByReportId(int $reportId): array
    {
        $q = $this->createQueryBuilder(DisplayField::class, 'df');
        $q->leftJoin('df.selectedDisplayFields', 'sdf');
        $q->select('df.id');
        $q->andWhere('sdf.report = :reportId')
            ->setParameter('reportId', $reportId);
        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'id');
    }

    /**
     * @param int $reportId
     * @param int $reportGroupId
     * @return int[]
     */
    public function getSelectedDisplayFieldIdsByReportGroupId(int $reportId, int $reportGroupId): array
    {
        $q = $this->createQueryBuilder(SelectedDisplayField::class, 'sdf');
        $q->leftJoin('sdf.displayField', 'df');
        $q->select('df.id');
        $q->andWhere('sdf.report = :reportId')
            ->setParameter('reportId', $reportId);
        $q->andWhere('df.displayFieldGroup = :displayFieldGroup')
            ->setParameter('displayFieldGroup', $reportGroupId);
        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'id');
    }

    /**
     * @param int $reportId
     * @return SelectedFilterField|null
     */
    public function getIncludeType(int $reportId): ?SelectedFilterField
    {
        $filterFieldByInclude = $this->getFilterFieldByName('include');
        $selectedFilterField = $this->getRepository(SelectedFilterField::class)->findOneBy(
            ['report' => $reportId, 'filterField' => $filterFieldByInclude->getId()]
        );
        return ($selectedFilterField instanceof SelectedFilterField) ? $selectedFilterField : null;
    }

    /**
     * @param int $reportId
     * @param int $displayGroupId
     * @return bool
     */
    public function isIncludeHeader(int $reportId, int $displayGroupId): bool
    {
        // this method for getting the display field group ids that saved in the database as header true
        $q = $this->createQueryBuilder(SelectedDisplayFieldGroup::class, 'selectedDisplayFieldGroup');
        $q->andWhere('selectedDisplayFieldGroup.report = :reportId')
            ->setParameter('reportId', $reportId);
        $q->andWhere('selectedDisplayFieldGroup.displayFieldGroup = :displayFieldGroupId')
            ->setParameter('displayFieldGroupId', $displayGroupId);
        return $this->getPaginator($q)->count() !== 0;
    }

    /**
     * @param int $reportId
     * @return int[]
     */
    public function getDisplayFieldGroupIdList(int $reportId): array
    {
        $q = $this->createQueryBuilder(DisplayField::class, 'displayField');
        $q->select('IDENTITY(displayField.displayFieldGroup) as displayFieldGroupId');
        $q->leftJoin('displayField.displayFieldGroup', 'displayFieldGroup');
        $q->leftJoin('displayField.selectedDisplayFields', 'selectedDisplayFields');
        $q->andWhere($q->expr()->eq('selectedDisplayFields.report', ':reportId'))
            ->setParameter('reportId', $reportId);
        $q->groupBy('displayFieldGroupId');

        return array_column($q->getQuery()->execute(), 'displayFieldGroupId');
    }

    /**
     * @param int $reportId
     * @return SelectedFilterField[]
     */
    public function getSkippedSelectedFilterFieldsByReportId(int $reportId): array
    {
        $q = $this->createQueryBuilder(SelectedFilterField::class, 'sff');
        $q->andWhere('sff.report = :reportId')
            ->setParameter('reportId', $reportId);
        $q->andWhere('sff.filterFieldOrder != :order')
            ->setParameter('order', 1);
        return $q->getQuery()->execute();
    }

    /**
     * @return DisplayFieldGroup[]
     */
    public function getAllDisplayFieldGroups(): array
    {
        $q = $this->createQueryBuilder(DisplayFieldGroup::class, 'displayFieldGroup');
        return $q->getQuery()->execute();
    }

    /**
     * @return DisplayField[]
     */
    public function getAllDisplayFields(): array
    {
        $q = $this->createQueryBuilder(DisplayField::class, 'displayField');
        $q->andWhere($q->expr()->isNotNull('displayFieldGroup.id'))
            ->leftJoin('displayField.displayFieldGroup', 'displayFieldGroup');
        return $q->getQuery()->execute();
    }

    /**
     * @return FilterField[]
     */
    public function getAllFilterFields(): array
    {
        $q = $this->createQueryBuilder(FilterField::class, 'filterField');
        $q->leftJoin('filterField.reportGroup', 'reportGroup')
            ->andWhere('reportGroup.name = :pimReportGroup')
            ->setParameter('pimReportGroup', 'pim')
            ->andWhere('filterField.name != :includeFilter')
            ->setParameter('includeFilter', 'include');
        return $q->getQuery()->execute();
    }
}
