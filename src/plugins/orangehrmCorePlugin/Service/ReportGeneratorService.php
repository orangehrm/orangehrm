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

namespace OrangeHRM\Core\Service;

use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Dao\ReportGeneratorDao;
use OrangeHRM\Core\Dto\FilterParams;
use OrangeHRM\Core\Report\DisplayField\BasicDisplayField;
use OrangeHRM\Core\Report\DisplayField\CombinedDisplayField;
use OrangeHRM\Core\Report\DisplayField\EntityAliasMapping;
use OrangeHRM\Core\Report\DisplayField\GenericBasicDisplayField;
use OrangeHRM\Core\Report\DisplayField\GenericDateDisplayField;
use OrangeHRM\Core\Report\DisplayField\ListableDisplayField;
use OrangeHRM\Core\Report\DisplayField\NormalizableDTO;
use OrangeHRM\Core\Report\DisplayField\Stringable;
use OrangeHRM\Core\Report\FilterField\FilterField;
use OrangeHRM\Core\Report\FilterField\ValueXModifiable;
use OrangeHRM\Core\Report\FilterField\ValueYModifiable;
use OrangeHRM\Core\Report\Header\Column;
use OrangeHRM\Core\Report\Header\Header;
use OrangeHRM\Core\Report\Header\HeaderData;
use OrangeHRM\Core\Report\Header\StackedColumn;
use OrangeHRM\Core\Report\ReportSearchFilterParams;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\AbstractDisplayField;
use OrangeHRM\Entity\CompositeDisplayField;
use OrangeHRM\Entity\DisplayField;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Report;
use OrangeHRM\Entity\SelectedFilterField;
use OrangeHRM\Entity\SummaryDisplayField;
use OrangeHRM\I18N\Traits\Service\I18NHelperTrait;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\ORM\QueryBuilderWrapper;

class ReportGeneratorService
{
    use EntityManagerHelperTrait;
    use I18NHelperTrait;

    public const SELECTED_FILTER_FIELD_TYPE_RUNTIME = 'Runtime';
    public const SELECTED_FILTER_FIELD_TYPE_PREDEFINED = 'Predefined';

    protected ?ReportGeneratorDao $reportGeneratorDao = null;

    /**
     * @var array<string, array>
     */
    protected array $generatedReportParamPool = [];

    /**
     * @return ReportGeneratorDao
     */
    public function getReportGeneratorDao(): ReportGeneratorDao
    {
        if (!$this->reportGeneratorDao instanceof ReportGeneratorDao) {
            $this->reportGeneratorDao = new ReportGeneratorDao();
        }
        return $this->reportGeneratorDao;
    }

    /**
     * @param int $reportId
     * @return bool
     */
    public function isPimReport(int $reportId): bool
    {
        $report = $this->getReportGeneratorDao()->getReport($reportId);
        if ($report instanceof Report) {
            return $report->getType() == 'PIM_DEFINED';
        }
        return false;
    }

    /**
     * @param int $reportId
     * @return HeaderData
     */
    public function getHeaderData(int $reportId): HeaderData
    {
        $selectedDisplayFields = [];
        $compositeFields = $this->getReportGeneratorDao()->getSelectedCompositeDisplayFieldsByReportId($reportId);
        $summaryFields = $this->getReportGeneratorDao()->getSummaryDisplayFieldByReportId($reportId);
        $displayFields = $this->getReportGeneratorDao()->getSelectedDisplayFieldsByReportId($reportId);

        $selectedDisplayFields = array_merge($selectedDisplayFields, $compositeFields, $displayFields, $summaryFields);
        $selectedDisplayGroupIds = $this->getReportGeneratorDao()->getSelectedDisplayFieldGroupIdsByReportId($reportId);

        return $this->getHeaderGroupsForDisplayFields($selectedDisplayFields, $selectedDisplayGroupIds);
    }

    /**
     * @param Array<DisplayField|CompositeDisplayField|SummaryDisplayField> $displayFields
     * @param int[] $selectedDisplayGroupIds
     * @return HeaderData
     */
    private function getHeaderGroupsForDisplayFields(array $displayFields, array $selectedDisplayGroupIds): HeaderData
    {
        /** @var StackedColumn[] $headerGroups */
        $headerGroups = [];
        $headerData = new HeaderData();

        // Default Group - for headers without a display group
        $defaultGroup = new StackedColumn([]);

        foreach ($displayFields as $displayField) {
            $column = new Column($displayField->getFieldAlias());
            $column->setName($this->getI18NHelper()->transBySource($displayField->getLabel()));
            $column->setSize($displayField->getWidth());
            if ($displayField->isValueList()) {
                $column->addCellProperties(['type' => 'list']);
            }
            $headerData->incrementColumnCount();

            if ($displayField instanceof AbstractDisplayField) {
                if (is_null($displayField->getDisplayFieldGroup())) {
                    $defaultGroup->addChild($column);
                } elseif (!isset($headerGroups[$displayField->getDisplayFieldGroup()->getId()])) {
                    $displayFieldGroup = $displayField->getDisplayFieldGroup();

                    if (in_array($displayField->getDisplayFieldGroup()->getId(), $selectedDisplayGroupIds)) {
                        $groupName = $this->getI18NHelper()->transBySource($displayFieldGroup->getName());
                        $headerData->incrementGroupCount();
                        $headerData->incrementGroupedColumnCount();
                    } else {
                        $groupName = null;
                    }
                    $headerGroup = new StackedColumn([$column]);
                    $headerGroup->setName($groupName);
                    $headerGroups[$displayField->getDisplayFieldGroup()->getId()] = $headerGroup;
                } else {
                    $headerGroups[$displayField->getDisplayFieldGroup()->getId()]->addChild($column);
                    $headerData->incrementGroupedColumnCount();
                }
            }
        }

        // Add the default group if it has any headers
        if (count($defaultGroup) > 0) {
            array_push($headerGroups, ...$defaultGroup->getChildren());
        }
        $preparedHeaderGroups = [];
        foreach ($headerGroups as $headerGroup) {
            if ($headerGroup instanceof StackedColumn && is_null($headerGroup->getName())) {
                array_push($preparedHeaderGroups, ...$headerGroup->getChildren());
                continue;
            }
            $preparedHeaderGroups[] = $headerGroup;
        }
        $headerData->setColumns($preparedHeaderGroups);
        return $headerData;
    }

    /**
     * @param int $reportId
     * @return Header
     */
    public function getHeaderDefinitionByReportId(int $reportId): Header
    {
        $headerData = $this->getHeaderData($reportId);
        $header = new Header($headerData->getColumns());
        $report = $this->getReportGeneratorDao()->getReport($reportId);
        $header->setMeta(
            new ParameterBag(
                [
                    'name' => $report->getName(),
                    'columnCount' => $headerData->getColumnCount(),
                    'groupCount' => $headerData->getGroupCount(),
                    'groupedColumnCount' => $headerData->getGroupedColumnCount(),
                ]
            )
        );
        return $header;
    }

    /**
     * @param ReportSearchFilterParams $filterParams
     * @return array
     */
    public function getNormalizedReportData(ReportSearchFilterParams $filterParams): array
    {
        list(
            $queryBuilderWrapper,
            $combinedDisplayFields,
            $listedDisplayFields,
            $displayFieldGroups
            ) = $this->getReportDataQueryBuilder($filterParams);

        $results = $queryBuilderWrapper->getQueryBuilder()->getQuery()->execute();
        // Normalize DTO objects
        foreach ($results as $i => $result) {
            foreach ($combinedDisplayFields as $combinedDisplayField) {
                if ($result[$combinedDisplayField] instanceof Stringable) {
                    $results[$i][$combinedDisplayField] = $result[$combinedDisplayField]->toString();
                }
            }
            foreach ($listedDisplayFields as $listedDisplayField) {
                if ($result[$listedDisplayField] instanceof NormalizableDTO) {
                    $results[$i] = array_merge(
                        $results[$i],
                        $result[$listedDisplayField]->toArray($displayFieldGroups[$listedDisplayField])
                    );
                }
                unset($results[$i][$listedDisplayField]);
            }
        }
        return $results;
    }

    /**
     * @param ReportSearchFilterParams $filterParams
     * @return int
     */
    public function getReportDataCount(ReportSearchFilterParams $filterParams): int
    {
        list($queryBuilderWrapper) = $this->getReportDataQueryBuilder($filterParams);
        return $this->getPaginator($queryBuilderWrapper->getQueryBuilder())->count();
    }

    /**
     * @param ReportSearchFilterParams $filterParams
     * @return array
     */
    protected function getReportDataQueryBuilder(ReportSearchFilterParams $filterParams): array
    {
        $key = $this->getHashKeyForFilterParamObject($filterParams);
        if (isset($this->generatedReportParamPool[$key])) {
            return $this->generatedReportParamPool[$key];
        }

        $displayFields = $this->getReportGeneratorDao()
            ->getSelectedDisplayFieldsByReportId($filterParams->getReportId());

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->from(Employee::class, 'employee')
            ->select('employee.empNumber')
            ->andWhere('employee.purgedAt IS NULL');

        $combinedDisplayFields = [];
        $listedDisplayFields = [];
        $joinAliases = [];
        $displayFieldGroups = [];

        foreach ($displayFields as $displayField) {
            $displayFieldClassName = $displayField->getClassName();
            $displayFieldClass = new $displayFieldClassName();
            if ($displayFieldClass instanceof \OrangeHRM\Core\Report\DisplayField\DisplayField) {
                if ($displayFieldClass instanceof GenericBasicDisplayField ||
                    $displayFieldClass instanceof GenericDateDisplayField) {
                    $displayFieldClass->setDisplayField($displayField);
                }

                if ($displayField->isValueList()) {
                    $displayFieldGroupId = $displayField->getDisplayFieldGroup()->getId();
                    $fieldAlias = 'displayFieldGroup' . $displayFieldGroupId;
                    if (!isset($displayFieldGroups[$fieldAlias])) {
                        $displayFieldGroups[$fieldAlias] = [$displayField->getFieldAlias()];
                    } else {
                        $displayFieldGroups[$fieldAlias][] = $displayField->getFieldAlias();
                        // if this field is a list field and already added to field groups
                        continue;
                    }
                } else {
                    $fieldAlias = $displayField->getFieldAlias();
                }

                // Don't use user input here
                $qb->addSelect($displayFieldClass->getSelectPart() . ' AS ' . $fieldAlias);

                // Track alias and DTO result alias
                if ($displayFieldClass instanceof ListableDisplayField) {
                    $listedDisplayFields[] = $fieldAlias;
                    array_push($joinAliases, ...$displayFieldClass->getEntityAliases());
                } elseif ($displayFieldClass instanceof CombinedDisplayField) {
                    $combinedDisplayFields[] = $fieldAlias;
                    array_push($joinAliases, ...$displayFieldClass->getEntityAliases());
                } elseif ($displayFieldClass instanceof BasicDisplayField) {
                    $joinAliases[] = $displayFieldClass->getEntityAlias();
                }
            }
        }

        $queryBuilderWrapper = $this->getQueryBuilderWrapper($qb);
        $selectedFilterFields = $this->getReportGeneratorDao()
            ->getSelectedFilterFieldsByReportId($filterParams->getReportId());
        foreach ($selectedFilterFields as $selectedFilterField) {
            $filterField = $selectedFilterField->getFilterField();
            $filterFieldClassName = $filterField->getClassName();
            $filterFieldClass = $this->getInitializedFilterFieldInstance($filterFieldClassName, $selectedFilterField);
            if ($filterFieldClass instanceof FilterField) {
                $filterFieldClass->addWhereToQueryBuilder($queryBuilderWrapper);
                array_push($joinAliases, ...$filterFieldClass->getEntityAliases());
            }
        }

        $qb->groupBy('employee.empNumber');
        $this->setJoinsToQueryBuilder($queryBuilderWrapper, array_unique($joinAliases));
        $this->setSortingAndPaginationParams($queryBuilderWrapper, $filterParams);

        $this->generatedReportParamPool[$key] = [
            $queryBuilderWrapper,
            $combinedDisplayFields,
            $listedDisplayFields,
            $displayFieldGroups,
            $joinAliases,
        ];
        return $this->generatedReportParamPool[$key];
    }

    /**
     * @param ReportSearchFilterParams $filterParams
     * @return string
     */
    protected function getHashKeyForFilterParamObject(ReportSearchFilterParams $filterParams): string
    {
        return md5(serialize($filterParams));
    }

    /**
     * @param QueryBuilderWrapper $queryBuilderWrapper
     * @param FilterParams $filterParams
     */
    protected function setSortingAndPaginationParams(
        QueryBuilderWrapper $queryBuilderWrapper,
        FilterParams $filterParams
    ): void {
        $qb = $queryBuilderWrapper->getQueryBuilder();
        if (!is_null($filterParams->getSortField())) {
            $qb->addOrderBy(
                $filterParams->getSortField(),
                $filterParams->getSortOrder()
            );
        }
        // If limit = 0, will not paginate
        if (!empty($filterParams->getLimit())) {
            $qb->setFirstResult($filterParams->getOffset())
                ->setMaxResults($filterParams->getLimit());
        }
    }

    /**
     * @param QueryBuilderWrapper $queryBuilderWrapper
     * @param string[] $joinAliases
     */
    protected function setJoinsToQueryBuilder(QueryBuilderWrapper $queryBuilderWrapper, array $joinAliases): void
    {
        foreach ($joinAliases as $joinAlias) {
            $this->setJoinToQueryBuilder($queryBuilderWrapper, $joinAlias);
        }
    }

    /**
     * @param QueryBuilderWrapper $queryBuilderWrapper
     * @param string $joinAlias
     */
    protected function setJoinToQueryBuilder(QueryBuilderWrapper $queryBuilderWrapper, string $joinAlias): void
    {
        $qb = $queryBuilderWrapper->getQueryBuilder();
        if (isset(EntityAliasMapping::ALIAS_DEPENDENCIES[$joinAlias])) {
            // alias have dependencies
            if (!in_array($joinAlias, $qb->getAllAliases())) {
                $this->setJoinToQueryBuilder($queryBuilderWrapper, EntityAliasMapping::ALIAS_DEPENDENCIES[$joinAlias]);
                $qb->leftJoin(EntityAliasMapping::ALIAS_MAPPING[$joinAlias], $joinAlias);
            } // else: alias already added
        } elseif (isset(EntityAliasMapping::ALIAS_MAPPING[$joinAlias])) {
            $qb->leftJoin(EntityAliasMapping::ALIAS_MAPPING[$joinAlias], $joinAlias);
        } // else: no need to left join since alias in root alias
    }

    /**
     * @param Report $report
     * @param array $fieldGroup
     * @param array $criterias
     * @param string $includeType
     * @return Report
     * @throws TransactionException
     */
    public function savePimDefinedReport(
        Report $report,
        array $fieldGroup,
        array $criterias,
        string $includeType
    ): Report {
        $selectedDisplayFieldGroupIds = [];
        $selectedDisplayFieldIds = [];
        foreach ($fieldGroup as $key => $value) {
            // creating an array that contains the display field group id which selected as header by user(`ohrm_display_field_group` table)
            if ($value['includeHeader'] ?? false) {
                array_push($selectedDisplayFieldGroupIds, $key);
            }

            foreach ($value['fields'] as $field) {
                /** creating an array that contains the display field id (`ohrm_display_field` table)
                 * Here fields is the value pair of $fieldGroup associative array which  contains the id of `ohrm_display_field` table
                 **/
                array_push($selectedDisplayFieldIds, $field);
            }
        }
        $selectedFilterFields = $this->generateSelectedFilterFieldsByCriteria($report, $criterias);
        $selectedFilterFields[] = $this->generateSelectedFilterFieldForIncludeEmployee($report, $includeType);
        $this->modifySelectedFilterFieldValues($selectedFilterFields);

        return $this->getReportGeneratorDao()
            ->saveReport($report, $selectedDisplayFieldGroupIds, $selectedDisplayFieldIds, $selectedFilterFields);
    }

    /**
     * @param string $filterFieldClassName
     * @param SelectedFilterField $selectedFilterField
     * @return FilterField
     */
    public function getInitializedFilterFieldInstance(
        string $filterFieldClassName,
        SelectedFilterField $selectedFilterField
    ): FilterField {
        return new $filterFieldClassName(
            $selectedFilterField->getOperator(),
            $selectedFilterField->getX(),
            $selectedFilterField->getY(),
            $selectedFilterField->getFilterFieldOrder()
        );
    }

    /**
     * @param Report $report
     * @param array $criteria
     * @return SelectedFilterField[]
     */
    protected function generateSelectedFilterFieldsByCriteria(Report $report, array $criteria): array
    {
        $selectedFilterFields = [];
        $counter = 2;
        foreach ($criteria as $key => $value) {
            $selectedFilterField = new SelectedFilterField();
            $selectedFilterField->setReport($report);
            $selectedFilterField->getDecorator()->setFilterFieldById($key);
            $selectedFilterField->setFilterFieldOrder($counter);
            $selectedFilterField->setX($value['x'] ?? null);
            $selectedFilterField->setY($value['y'] ?? null);
            $selectedFilterField->setOperator($value['operator'] ?? null);
            $selectedFilterField->setType('Predefined');
            $selectedFilterFields[] = $selectedFilterField;
            $counter++;
        }
        return $selectedFilterFields;
    }

    /**
     * @param Report $report
     * @param string $includeType
     * @return SelectedFilterField
     */
    protected function generateSelectedFilterFieldForIncludeEmployee(
        Report $report,
        string $includeType
    ): SelectedFilterField {
        $includeType = ($includeType === 'onlyCurrent') ? 'isNull' : (($includeType === 'onlyPast') ? 'isNotNull' : null);
        $filterFieldByInclude = $this->getReportGeneratorDao()->getFilterFieldByName('include');
        $selectedFilterField = new SelectedFilterField();
        $selectedFilterField->setReport($report);
        $selectedFilterField->setFilterField($filterFieldByInclude);
        $selectedFilterField->setFilterFieldOrder(1);
        $selectedFilterField->setOperator($includeType);
        $selectedFilterField->setType('Predefined');
        return $selectedFilterField;
    }

    /**
     * @param SelectedFilterField[] $selectedFilterFields
     */
    protected function modifySelectedFilterFieldValues(array $selectedFilterFields)
    {
        foreach ($selectedFilterFields as $selectedFilterField) {
            $filterField = $selectedFilterField->getFilterField();
            $filterFieldClassName = $filterField->getClassName();
            $filterFieldClass = $this->getInitializedFilterFieldInstance($filterFieldClassName, $selectedFilterField);
            if ($filterFieldClass instanceof FilterField) {
                if ($filterFieldClass instanceof ValueXModifiable) {
                    $filterFieldClass->modifyX([$filterFieldClass, 'xValueModifier']);
                    $selectedFilterField->setX($filterFieldClass->getX());
                }
                if ($filterFieldClass instanceof ValueYModifiable) {
                    $filterFieldClass->modifyY([$filterFieldClass, 'yValueModifier']);
                    $selectedFilterField->setY($filterFieldClass->getY());
                }
            }
        }
    }
}
