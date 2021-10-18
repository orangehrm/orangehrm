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
use OrangeHRM\ORM\Exception\TransactionException;
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
     * @param Report $report
     * @param int[] $selectedDisplayFieldGroupIds
     * @param int[] $selectedDisplayFieldIds
     * @param array $criterias
     * @param string $includeType
     * @return Report
     * @throws TransactionException
     */
    public function saveReport(
        Report $report,
        array $selectedDisplayFieldGroupIds,
        array $selectedDisplayFieldIds,
        array $criterias,
        string $includeType
    ): Report {
        $this->beginTransaction();
        try {
            $this->persist($report);
            if (count($selectedDisplayFieldGroupIds) > 0) {
                $this->saveSelectedDisplayFieldGroup($report, $selectedDisplayFieldGroupIds);
            }
            $this->saveSelectedDisplayField($report, $selectedDisplayFieldIds);
            $this->saveSelectedFilterField($report, $criterias, $includeType);
            $this->commitTransaction();
            return $report;
        } catch (Exception $exception) {
            $this->rollBackTransaction();
            throw new TransactionException($exception);
        }
    }

    /**
     * @param Report $report
     * @param array $criterias
     * @param string $includeType
     * @return void
     */
    public function saveSelectedFilterField(Report $report, array $criterias, string $includeType): void
    {
        $this->saveDefaultSelectedFilterField(
            $report,
            $includeType
        ); // Always get first priority while saving `ohrm_selected_filter_field`
        $counter = 2;
        foreach ($criterias as $key => $value) {
            $filterField = $this->getRepository(FilterField::class)->find($key);
            $selectedFilterField = new SelectedFilterField();
            $selectedFilterField->setReport($report);
            $selectedFilterField->setFilterField($filterField);
            $selectedFilterField->setFilterFieldOrder($counter);
            $selectedFilterField->setX($value['x']);// "x" is the value pair inside the associative array
            $selectedFilterField->setY($value['y']);
            $selectedFilterField->setOperator($value['operator']);
            $selectedFilterField->setType('Predefined');
            $this->getEntityManager()->persist($selectedFilterField);
            $counter++;
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
            $displayField = $this->getRepository(DisplayField::class)->find($selectedDisplayFieldId);
            $selectedDisplayField->setDisplayField($displayField);
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
            $displayFieldGroup = $this->getRepository(DisplayFieldGroup::class)->find($selectedDisplayFieldGroupId);
            $selectedDisplayFieldGroup->setReport($report);
            $selectedDisplayFieldGroup->setDisplayFieldGroup($displayFieldGroup);
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
     * @param Report $report
     * @param string $includeType
     * @return void
     */
    public function saveDefaultSelectedFilterField(Report $report, string $includeType): void
    {
        // this function for saving the default initial include record
        $filterFieldByInclude = $this->getFilterFieldByName('include');
        $selectedFilterField = new SelectedFilterField();
        $selectedFilterField->setReport($report);
        $selectedFilterField->setFilterField($filterFieldByInclude);
        $selectedFilterField->setFilterFieldOrder(1);
        if ($includeType === '') {
            // currentAndPast
            $selectedFilterField->setOperator(null);
        }
        $selectedFilterField->setOperator($includeType);
        $selectedFilterField->setType('Predefined');
        $this->persist($selectedFilterField);
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
    public function getSelectedDisplayFieldListByReportId(int $reportId): array
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
    public function getSelectedDisplayFieldIdByReportGroupId(int $reportId, int $reportGroupId): array
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
        $selectedFilterField = $this->getRepository(SelectedFilterField::class)->findOneBy(['report' => $reportId,'filterField' => $filterFieldByInclude->getId()]);
        return ($selectedFilterField instanceof SelectedFilterField) ? $selectedFilterField : null;
    }

    /**
     * @param int $reportId
     * @param int $displayGroupId
     * @return SelectedDisplayFieldGroup[]
     */
    public function isIncludeHeader(int $reportId, int $displayGroupId): array
    {
        // this method for getting the display field group ids that saved in the database as header true
        $q = $this->createQueryBuilder(SelectedDisplayFieldGroup::class, 'selectedDisplayFieldGroup');
        $q->andWhere('selectedDisplayFieldGroup.report = :reportId')
            ->setParameter('reportId', $reportId);
        $q->andWhere('selectedDisplayFieldGroup.displayFieldGroup = :displayFieldGroupId')
            ->setParameter('displayFieldGroupId', $displayGroupId);
        return $q->getQuery()->execute();
    }

    /**
     * @param int $reportId
     * @return int[]
     */
    public function getDisplayFieldGroupIdList(int $reportId): array
    {
        // this method for getting all display field group ids that saved in the database
        $displayFieldGroups = $this->getDisplayFieldGroupIds($reportId);
        $displayFieldGroupIds = [];
        foreach ($displayFieldGroups as $displayFieldGroup){
            array_push($displayFieldGroupIds,$displayFieldGroup->getDisplayFieldGroup()->getId());
        }
        return $displayFieldGroupIds;
    }

    /**
     * @param int $reportId
     * @return DisplayField[]
     */
    public function getDisplayFieldGroupIds(int $reportId): array
    {
        $q = $this->createQueryBuilder(DisplayField::class, 'df');
        $q->leftJoin('df.displayFieldGroup', 'dfg');
        $q->leftJoin('df.selectedDisplayFields', 'sdf');
        $q->andWhere('sdf.report = :reportId')
            ->setParameter('reportId', $reportId);
        $q->groupBy('df.displayFieldGroup');
        return $q->getQuery()->execute();
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
}
