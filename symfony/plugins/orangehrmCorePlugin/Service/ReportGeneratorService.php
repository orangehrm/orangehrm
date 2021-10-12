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
use OrangeHRM\Core\Report\DisplayField\ListableDisplayField;
use OrangeHRM\Core\Report\DisplayField\NormalizableDTO;
use OrangeHRM\Core\Report\FilterField\FilterField;
use OrangeHRM\Core\Report\Header\Column;
use OrangeHRM\Core\Report\Header\Header;
use OrangeHRM\Core\Report\Header\StackedColumn;
use OrangeHRM\Core\Report\ReportSearchFilterParams;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\AbstractDisplayField;
use OrangeHRM\Entity\CompositeDisplayField;
use OrangeHRM\Entity\DisplayField;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Report;
use OrangeHRM\Entity\SummaryDisplayField;
use OrangeHRM\ORM\QueryBuilderWrapper;

class ReportGeneratorService
{
    use EntityManagerHelperTrait;

    public const LIST_SEPARATOR = "|\n|";
    public const SELECTED_FILTER_FIELD_TYPE_RUNTIME = 'Runtime';
    public const SELECTED_FILTER_FIELD_TYPE_PREDEFINED = 'Predefined';

    protected ?ReportGeneratorDao $reportGeneratorDao = null;

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
     * Gets ids of the selectedFilterFields of a report, given the report id.
     * @param integer $reportId
     * @return array
     */
    public function getSelectedFilterFieldIdsByReportId($reportId) {
        // TODO
        $selectedFilterFields = $this->getReportableService()->getSelectedFilterFields($reportId, true);

        if ($selectedFilterFields != null) {

            $selectedFilterFieldIdArray = [];

            foreach ($selectedFilterFields as $selectedFilterField) {
                $selectedFilterFieldIdArray[] = $selectedFilterField->getFilterFieldId();
            }

            return $selectedFilterFieldIdArray;
        } else {
            return null;
        }
    }

    /**
     * Gets the id of the report group that is related to a report. ( given report id )
     * @param integer $reportId
     * @return integer
     */
    public function getReportGroupIdOfAReport($reportId) {
        // TODO
        $report = $this->getReportableService()->getReport($reportId);

        if ($report != null) {
            $reportGroup = $report->getReportGroup();
            $reportGroupId = $reportGroup->getReportGroupId();

            return $reportGroupId;
        } else {
            return null;
        }
    }

    /**
     * Reorders FilterFields according to ids, in the order given in selectedFilterFieldIds array.
     * @param integer[] $selectedFilterFieldIds
     * @param FilterField[] $runtimeFilterFields
     * @return Doctrine_Collection (FilterField)
     */
    public function orderRuntimeFilterFields($selectedFilterFieldIds, $runtimeFilterFields) {
        // TODO
        $filterFields = new Doctrine_Collection("FilterField");

        foreach ($selectedFilterFieldIds as $id) {

            foreach ($runtimeFilterFields as $runtimeFilterField) {

                if ($runtimeFilterField->getFilterFieldId() == $id) {
                    $filterFields->add($runtimeFilterField);
                    break;
                }
            }
        }

        return $filterFields;
    }

    /**
     * Gets widget names and label names of runtime filter fields for a given report (ie. given report id)
     * @param integer $reportId
     * @return array
     */
    public function getRuntimeFilterFieldWidgetNamesAndLabels($reportId) {
        // TODO
        $reportGroupId = $this->getReportGroupIdOfAReport($reportId);

        $type = PluginSelectedFilterField::RUNTIME_FILTER_FIELD;
        $runtimeSelectedFilterFields = $this->getReportGeneratorDao()->getSelectedFilterFieldsByType($reportId, $type, true);

        if (($reportGroupId != null) && ($runtimeSelectedFilterFields != null)) {

            $runtimeFilterFieldWidgetNamesAndLabels = [];

            foreach ($runtimeSelectedFilterFields as $runtimeSelectedFilterField) {
                $runtimeFilterField = $runtimeSelectedFilterField->getFilterField();
                $tempArray['widgetName'] = $runtimeFilterField->getFilterFieldWidget();
                $tempArray['labelName'] = $runtimeFilterField->getName();
                $tempArray['required'] = $runtimeFilterField->getRequired();
                $runtimeFilterFieldWidgetNamesAndLabels[] = $tempArray;
            }

            return $runtimeFilterFieldWidgetNamesAndLabels;
        } else {
            return null;
        }
    }

    /**
     * Generates runtime where clause conditions using the value
     * @param FilterField[] $selectedRuntimeFilterFields
     * @param array $values
     * @return array
     */
    public function generateRuntimeWhereClauseConditions($selectedRuntimeFilterFields, $values) {
        // TODO
        $conditionArray = [];

        foreach ($selectedRuntimeFilterFields as $runtimeFilterField) {

            $labelName = $runtimeFilterField->getName();
            $widgetName = $runtimeFilterField->getFilterFieldWidget();
            $widget = new $widgetName([], ['id' => $labelName]);

            $conditionNo = $runtimeFilterField->getConditionNo();

            if (array_key_exists($conditionNo, $conditionArray)) {

                if ($widget->generateWhereClausePart($runtimeFilterField->getWhereClausePart(), $values[$runtimeFilterField->getName()]) != null) {
                    $conditionArray[$conditionNo] = $conditionArray[$conditionNo] . " AND " . $widget->generateWhereClausePart($runtimeFilterField->getWhereClausePart(), $values[$runtimeFilterField->getName()]);
                }
            } else {
                if ($widget->generateWhereClausePart($runtimeFilterField->getWhereClausePart(), $values[$runtimeFilterField->getName()]) != null) {
                    $conditionArray[$conditionNo] = $widget->generateWhereClausePart($runtimeFilterField->getWhereClausePart(), $values[$runtimeFilterField->getName()]);
                }
            }
        }

        return $conditionArray;
    }

    /**
     * Gets filter fields that are selected for a given report and are of type Runtime.
     * @param integer $reportId
     * @return array ( array of FilterFiled )
     */
    public function getSelectedRuntimeFilterFields($reportId) {
        // TODO
        $reportGroupId = $this->getReportGroupIdOfAReport($reportId);

        $type = PluginSelectedFilterField::RUNTIME_FILTER_FIELD;

        $runtimeSelectedFilterFields = $this->getReportableService()->getSelectedFilterFieldsByType($reportId, $type, true);
        $runtimeFilterFieldList = new Doctrine_Collection("FilterField");

        foreach ($runtimeSelectedFilterFields as $runtimeSelectedFilterField) {
            $runtimeFilterFieldList->add($runtimeSelectedFilterField->getFilterField());
        }

        return $runtimeFilterFieldList;
    }

    /**
     * Generates select condition excluding summary function for a given report (ie. given report id).
     * @param integer $reportId
     * @return string
     */
    public function getSelectConditionWithoutSummaryFunction($reportId) {
        // TODO
        $selectCondition = null;

        $displayGroups = $this->getGroupedDisplayFieldsForReport($reportId);
        $selectCondition = $this->constructSelectStatement($displayGroups);

        return $selectCondition;
    }

    public function getGroupedDisplayFieldsForReport($reportId) {
        // TODO
        $displayFields = $this->getSelectedDisplayFields($reportId);
        $metaFields = $this->getSelectedMetaDisplayFields($reportId);
        $compositeFields = $this->getSelectedCompositeDisplayFields($reportId);

        $selectedDisplayFields = array_merge($displayFields, $compositeFields, $metaFields);

        $displayGroups = $this->getGroupedDisplayFields($selectedDisplayFields);

        return $displayGroups;
    }

    /**
     * Appends display field names to select statement.
     * @param string $selectStatement
     * @param DisplayField $displayField
     * @return string
     */
    public function constructSelectClauseForDisplayField($selectStatement, $displayField) {
        // TODO
        $clause = $displayField->getName();

        if (KeyHandler::keyExists() && $displayField->getIsEncrypted()) {
            $pattern = '/(\{\{)(.{0,})(\}\})/';
            if (preg_match($pattern, $clause)) {
                $clause = preg_replace($pattern, 'AES_DECRYPT(UNHEX($2),"' . KeyHandler::readKey() . '")', $clause);
            } else {
                $clause = 'AES_DECRYPT(UNHEX('. $displayField->getName() . '),"' . KeyHandler::readKey() . '")';
            }
        }
        if ($displayField->getIsValueList()) {
            $clause = "GROUP_CONCAT(DISTINCT " . $clause . " SEPARATOR '|\\n|' ) ";
        }
        $fieldAlias = $displayField->getFieldAlias();
        if (!empty($fieldAlias)) {
            $clause = $clause . " AS " . $fieldAlias;
        }

        if (empty($selectStatement)) {
            $selectStatement = $clause;
        } else {
            $selectStatement .= ',' . $clause;
        }

        return $selectStatement;
    }


    public function constructSelectClauseForListGroup($selectStatement, $displayFieldGroup, $displayFields) {
        // TODO
        $fieldList = '';

        $isEncryptEnabled = KeyHandler::keyExists();

        foreach ($displayFields as $field) {
            $fieldName = $field->getName();

            if ($isEncryptEnabled && $field->getIsEncrypted()) {
                $pattern = '/(\{\{)(.{0,})(\}\})/';
                if (preg_match($pattern, $fieldName)) {
                    $fieldName = preg_replace($pattern, 'AES_DECRYPT(UNHEX($2),"' . KeyHandler::readKey() . '")', $fieldName);
                } else {
                    $fieldName = 'AES_DECRYPT(UNHEX('. $fieldName . '),"' . KeyHandler::readKey() . '")';
                }
            }

            // If null, change to empty string since CONCAT_WS will skip nulls, causing problems with the field list order.
            $fieldName = 'IFNULL(' . $fieldName . ",'')";

            if (empty($fieldList)) {
                $fieldList = $fieldName;
            } else {
                $fieldList .= ',' . $fieldName;
            }
        }

        $alias = "DisplayFieldGroup" . $displayFieldGroup->getId();

        $clause = "CONCAT_WS('|^^|', " . $fieldList . ")";
        $clause = "GROUP_CONCAT(DISTINCT " . $clause . " SEPARATOR '|\\n|' ) AS " . $alias;

        if (empty($selectStatement)) {
            $selectStatement = $clause;
        } else {
            $selectStatement .= ',' . $clause;
        }

        return $selectStatement;

    }
    /**
     * Constructs select statement part with meta display fields.
     * @param integer $reportId
     * @return string
     */
    public function constructSelectStatement(array $displayFieldGroups) {
        // TODO
        $selectStatement = null;

        foreach ($displayFieldGroups as $groupDetails) {
            $group = $groupDetails[0];
            $displayFields = $groupDetails[1];

            if (count($displayFields) > 0) {
                if ($group->getIsList()) {
                        $selectStatement = $this->constructSelectClauseForListGroup($selectStatement, $group, $displayFields);
                } else {

                    foreach ($displayFields as $displayField) {
                        $selectStatement = $this->constructSelectClauseForDisplayField($selectStatement, $displayField);
                    }
                }
            }
        }

        return $selectStatement;
    }

    /**
     * Generates data set for a given sql.
     * @param string $sql
     * @return string[]
     */
    public function generateReportDataSet($reportId, $sql) {
        // TODO
        $dataSet = $this->getReportableService()->executeSql($sql);

        $dataSet = $this->processListsInDataSet($reportId, $dataSet);
        return $dataSet;
    }

    public function processListsInDataSet($reportId, $dataSet) {
        // TODO
        $displayGroups = $this->getGroupedDisplayFieldsForReport($reportId);

        for($rowNdx = 0; $rowNdx < count($dataSet); $rowNdx++) {
            $dataRow = $dataSet[$rowNdx];

            foreach ($displayGroups as $groupDetails) {

                $group = $groupDetails[0];
                $displayFields = $groupDetails[1];

                if ($group->getIsList() && count($displayFields) > 0) {

                    $groupAlias = 'DisplayFieldGroup' . $group->getId();

                    $groupValue = $dataRow[$groupAlias];

                    $fieldValues = [];

                    foreach($displayFields as $displayField) {
                        $fieldValues[$displayField->getFieldAlias()] = [];
                    }

                    if (!empty($groupValue)) {

                        $rows = explode(self::LIST_SEPARATOR, $groupValue);
                        foreach ($rows as $row) {
                            $fields = explode('|^^|', $row);
                            $fieldNdx = 0;

                            foreach($displayFields as $displayField) {

                                if (isset($fields[$fieldNdx])) {
                                    $fieldValue = $fields[$fieldNdx];
                                } else {
                                    $fieldValue = "";
                                }

                                $fieldValues[$displayField->getFieldAlias()][] = $fieldValue;
                                $fieldNdx++;

                            }
                        }

                    }

                    foreach($fieldValues as $key=>$value) {
                        $dataRow[$key] = $value;
                    }


                }
            }

            $dataSet[$rowNdx] = $dataRow;
        }

        return $dataSet;
    }

    /**
     * @param int $reportId
     * @return StackedColumn[]
     */
    public function getHeaderGroups(int $reportId): array
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
     * Get list of selected selected display fields for given report
     * @param integer $reportId
     * @return SelectedDisplayField[]
     */
    private function getSelectedMetaDisplayFields($reportId) {
        // TODO
        $report = $this->getReportGeneratorDao()->getReport($reportId);
        $reportGroupId = $report->getReportGroup()->getId();
        $displayFields = [];

        $metaFields = $this->getReportGeneratorDao()->getMetaDisplayFields($reportGroupId);

        if (!empty($metaFields)) {
            foreach ($metaFields as $displayField) {
                $displayFields[] = $displayField;
            }
        }

        return $displayFields;
    }

    /**
     * @param Array<DisplayField|CompositeDisplayField|SummaryDisplayField> $displayFields
     * @param int[] $selectedDisplayGroupIds
     * @return Array<StackedColumn|Column>
     */
    private function getHeaderGroupsForDisplayFields(array $displayFields, array $selectedDisplayGroupIds): array
    {
        /** @var StackedColumn[] $headerGroups */
        $headerGroups = [];

        // Default Group - for headers without a display group
        $defaultGroup = new StackedColumn([]);

        foreach ($displayFields as $displayField) {
            $column = new Column($displayField->getFieldAlias());
            $column->setName($displayField->getLabel());
            $column->setSize($displayField->getWidth());

            if ($displayField instanceof AbstractDisplayField) {
                if (is_null($displayField->getDisplayFieldGroup())) {
                    $defaultGroup->addChild($column);
                } elseif (!isset($headerGroups[$displayField->getDisplayFieldGroup()->getId()])) {
                    $displayFieldGroup = $displayField->getDisplayFieldGroup();

                    if (in_array($displayField->getDisplayFieldGroup()->getId(), $selectedDisplayGroupIds)) {
                        $groupName = $displayFieldGroup->getName();
                        $headerGroup = new StackedColumn([$column]);
                        $headerGroup->setName($groupName);
                        $headerGroups[$displayField->getDisplayFieldGroup()->getId()] = $headerGroup;
                    } else {
                        $defaultGroup->addChild($column);
                    }
                } else {
                    $headerGroups[$displayField->getDisplayFieldGroup()->getId()]->addChild($column);
                }
            }
        }

        // Add the default group if it has any headers
        if (count($defaultGroup) > 0) {
            array_push($headerGroups, ...$defaultGroup->getChildren());
        }

        return $headerGroups;
    }

    /**
     * Generates a complete sql to retrieve report data set.
     * @param integer $reportId
     * @param array $conditionArray
     * @return string
     */
    public function generateSql($reportId, $conditionArray, $staticColumns = null) {
        // TODO
        $report = $this->getReportGeneratorDao()->getReport($reportId);
        $reportGroupId = $report->getReportGroupId();

        $reportGroup = $this->getReportGeneratorDao()->getReportGroup($reportGroupId);
        $coreSql = $reportGroup->getCoreSql();

        $selectStatement = $this->getSelectConditionWithoutSummaryFunction($reportId);

        $selectedGroupField = $this->getReportableService()->getSelectedGroupField($reportId);
        $summaryDisplayField = null;
        $groupByClause = "";

        if (!is_null($selectedGroupField)) {
            $summaryDisplayField = $selectedGroupField->getSummaryDisplayField();
            $function = $summaryDisplayField->getFunction();
            $summaryFieldAlias = $summaryDisplayField->getFieldAlias();
            $summaryFunction = $function . " AS " . $summaryFieldAlias;

            $groupField = $selectedGroupField->getGroupField();
            $groupByClause = $groupField->getGroupByClause();
        }

        if (isset($summaryFunction)) {
            $selectStatement = $selectStatement . "," . $summaryFunction;
        }

        $sql = str_replace("selectCondition", $selectStatement, $coreSql);

        foreach ($conditionArray as $key => $condition) {
            $searchString = "whereCondition" . $key;

            if (empty($condition)) {
                $condition = 'TRUE';
            }
            $sql = str_replace($searchString, $condition, $sql);
        }

        $pattern = "/whereCondition\d+/";

        $sql = preg_replace($pattern, "true", $sql);

        $sql = str_replace("groupByClause", $groupByClause, $sql);

        if ($staticColumns != null) {
            $sql = $this->insertStaticColumnsInSelectStatement($sql, $staticColumns);
        }

        return $sql;
    }

    /**
     * Static columns are just texts that appear in every records. Those are same in every records.
     * This method inserts static columns in to the select statement. Its inserted just after the
     * SELECT clause.
     * @param string $statement
     * @param array $staticColumns
     * @return string
     */
    private function insertStaticColumnsInSelectStatement($statement, $staticColumns) {
        // TODO
        $staticSelectStatement = null;

        foreach ($staticColumns as $key => $value) {

            if ($staticSelectStatement == null) {
                $staticSelectStatement = "'" . $value . "' AS " . $key . " , ";
            } else {
                $staticSelectStatement .= "'" . $value . "' AS " . $key . " , ";
            }
        }

        $statement = substr_replace($statement, $staticSelectStatement, 7, 0);

        return $statement;
    }

    /**
     * Constructs filter field id, form value pair array when filter field list and form values are given.
     * It maps filter field id to form value.
     * @param FilterField $selectedRuntimeFilterFieldList
     * @param string[] $formValues
     * @return string[]
     */
    public function linkFilterFieldIdsToFormValues($selectedRuntimeFilterFieldList, $formValues) {
        // TODO
        $filterFieldIdAndValueArray = [];

        if ($selectedRuntimeFilterFieldList[0]->getFilterFieldId() != null) {

            foreach ($selectedRuntimeFilterFieldList as $runtimeFilterField) {

                $filterFieldId = $runtimeFilterField->getFilterFieldId();
                $value = $formValues[$runtimeFilterField->getName()];

                $filterFieldIdAndValueArray[] = ["filterFieldId" => $filterFieldId, "value" => $value];
            }
        }

        return $filterFieldIdAndValueArray;
    }

    /**
     * Generates where clause condition array. It takes an array of filterFieldId and value.
     * According to the condition number, it inserts the where clause part into an array.
     * Key of the condition array is condition number and the Value is where clause string,
     * that should be replaced with whereConditing in the core sql.
     * @param array $filterFieldIdsAndValues
     * @return string[]
     */
    public function generateWhereClauseConditionArray($selectedFilterFields, $formValues) {
        // TODO
        $conditionArray = [];

        foreach ($selectedFilterFields as $selectedFilterField) {

            $type = $selectedFilterField->getType();
            $filterFieldId = $selectedFilterField->getFilterFieldId();

            if ($type === self::SELECTED_FILTER_FIELD_TYPE_PREDEFINED) {
                // TODO
                $predefinedFilterField = $this->getReportableService()->getFilterFieldById($filterFieldId);

                $conditionNo = $predefinedFilterField->getConditionNo();
                $whereClause = $this->generateWhereClauseForPredefinedReport($selectedFilterField);

                if (!empty($whereClause)) {
                    if (array_key_exists($conditionNo, $conditionArray)) {
                        $conditionArray[$conditionNo] = $conditionArray[$conditionNo] . " AND " . $whereClause;
                    } else {
                        $conditionArray[$conditionNo] = $whereClause;
                    }
                }
            } elseif ($type === self::SELECTED_FILTER_FIELD_TYPE_RUNTIME) {
                throw new \Exception('Filter field type `' . self::SELECTED_FILTER_FIELD_TYPE_RUNTIME. '` not implemented');
            }
        }

        return $conditionArray;
    }

    /**
     * Gets project activity name for a given activity id.
     * @param integer $activityId
     * @return string
     */
    public function getProjectActivityNameByActivityId($activityId) {
        // TODO
        $projectActivity = $this->getReportableService()->getProjectActivityByActivityId($activityId);
        $activityName = $projectActivity->getName();

        return $activityName;
    }

    /**
     * Gets the name of the report given report id
     * @param integer $reportId
     * @return string
     */
    public function getReportName($reportId) {
        // TODO
        $reportName = NULL;

        $report = $this->getReportableService()->getReport($reportId);

        if (!empty($report)) {
            $reportName = $report->getName();
        }

        return $reportName;
    }

    public function generateSqlForNotUseFilterFieldReports($reportId, $formValues) {
        // TODO
        $report = $this->getReportableService()->getReport($reportId);
        $reportGroupId = $report->getReportGroupId();

        $reportGroup = $this->getReportableService()->getReportGroup($reportGroupId);
        $coreSql = $reportGroup->getCoreSql();

        $selectStatement = $this->getSelectConditionWithoutSummaryFunction($reportId);
        $selectedGroupField = $this->getReportableService()->getSelectedGroupField($reportId);
        $summaryDisplayField = null;
        $groupByClause = '';

        if (!is_null($selectedGroupField)) {
            $summaryDisplayField = $selectedGroupField->getSummaryDisplayField();
            $function = $summaryDisplayField->getFunction();
            $summaryFieldAlias = $summaryDisplayField->getFieldAlias();
            $summaryFunction = $function . " AS " . $summaryFieldAlias;

            $groupField = $selectedGroupField->getGroupField();
            $groupByClause = $groupField->getGroupByClause();
        }

        if (isset($summaryFunction)) {
            $selectStatement = $selectStatement . "," . $summaryFunction;
        }

        $sql = str_replace("selectCondition", $selectStatement, $coreSql);

        $sql = str_replace("groupByClause", $groupByClause, $sql);

        foreach ($formValues as $key => $value) {

            $pattern = '/#@[\"]*' . $key . '[\)\"]*@,@[a-zA-Z0-9\(\)_\.\-\ !\"\=]*@#/';


            preg_match($pattern, $sql, $matches);
            if (!empty($matches)) {
                $str = $matches[0];

                $array = explode("@", $str);
                if (($value == '-1') || ($value == '0') || ($value == '')) {
                    $sql = str_replace($str, $array[3], $sql);
                } else {
                    $value = str_replace($key, $value, $array[1]);
                    $sql = str_replace($str, $value, $sql);
                }
            }
        }

        return $sql;
    }

    public function generateWhereClause() {
        // TODO
        $value = "no";
        $jobTitle = "jobTitle";
        $sql = 'select #@jobTitle)@,@de_f-a.u_lt @# where';
        $pattern = '/#@[\"]*' . $jobTitle . "[\)]*[\"]*@,@[a-zA-Z_\.\-\ ]*@#/";

        preg_match($pattern, $sql, $matches);

        $str = $matches[0];
        $array = explode("@", $str);

        if ($value == null) {
            $sql = str_replace($str, $array[3], $sql);
        } else {
            $value = str_replace($jobTitle, $value, $array[1]);
            $sql = str_replace($str, $value, $sql);
        }

    }

    public function generateWhereClauseForPredefinedReport($selectedFilterField) {
        // TODO
        $whereCondition = $selectedFilterField->getWhereCondition();
        $whereClause = null;

        switch ($whereCondition) {
            case "=":
                $whereClause = $this->constructWhereStatementForUneryOperator($selectedFilterField, $whereCondition);
                break;
            case ">":
                $whereClause = $this->constructWhereStatementForUneryOperator($selectedFilterField, $whereCondition);
                break;
            case "<":
                $whereClause = $this->constructWhereStatementForUneryOperator($selectedFilterField, $whereCondition);
                break;
            case "<>":
                $whereClause = $this->constructWhereStatementForUneryOperator($selectedFilterField, $whereCondition);
                break;
            case "BETWEEN":
                $whereClause = $this->constructWhereStatementForBetweenOperator($selectedFilterField, $whereCondition);
                break;
            case "IN":
                $whereClause = $this->constructWhereStatementForInOperator($selectedFilterField, $whereCondition);
                break;
            case "IS NULL":
                $whereClause = $this->constructWhereStatementForIsNullOperator($selectedFilterField, $whereCondition);
                break;
            case "IS NOT NULL":
                $whereClause = $this->constructWhereStatementForIsNotNullOperator($selectedFilterField, $whereCondition);
                break;
            default:
                break;
        }

        return $whereClause;
    }

    public function constructWhereStatementForUneryOperator($selectedFilterField, $whereCondition) {
        // TODO
        $whereClausePart = $selectedFilterField->getFilterField()->getWhereClausePart();
        $value1 = $selectedFilterField->getValue1();
        $whereClause = $whereClausePart . " " . $whereCondition . " '" . $value1 . "'";
        return $whereClause;
    }

    public function constructWhereStatementForBetweenOperator($selectedFilterField, $whereCondition) {
        // TODO
        $whereClausePart = $selectedFilterField->getFilterField()->getWhereClausePart();
        $value1 = $selectedFilterField->getValue1();
        $value2 = $selectedFilterField->getValue2();
        $whereClause = $whereClausePart . " BETWEEN '" . $value1 . "' AND '" . $value2 . "'";
        return $whereClause;
    }

    public function constructWhereStatementForInOperator($selectedFilterField, $whereCondition){
        // TODO
        $whereClausePart = $selectedFilterField->getFilterField()->getWhereClausePart();
        $value1 = $selectedFilterField->getValue1();
        $whereClause = $whereClausePart . " " . $whereCondition . " " . "(" . $value1 . ")";
        return $whereClause;
    }

    public function constructWhereStatementForIsNullOperator($selectedFilterField, $whereCondition){
        // TODO
        $whereClausePart = $selectedFilterField->getFilterField()->getWhereClausePart();
        $whereClause = $whereClausePart . " IS NULL";
        return $whereClause;
    }

    public function constructWhereStatementForIsNotNullOperator($selectedFilterField, $whereCondition){
        // TODO
        $whereClausePart = $selectedFilterField->getFilterField()->getWhereClausePart();
        $whereClause = $whereClausePart . " IS NOT NULL";
        return $whereClause;
    }

    public function saveSelectedFilterFields($formValues, $reportId, $type) {
        // TODO
        $reportableService = $this->getReportableService();
        $reportableService->removeSelectedFilterFields($reportId);

        foreach ($formValues as $key => $value) {

            $filterField = $this->getReportableService()->getFilterFieldByName($key);
            $filterFieldId = $filterField->getFilterFieldId();
            $filterFieldOrder = 0;

            $widgetName = $filterField->getFilterFieldWidget();
            $widget = new $widgetName();

            if (array_key_exists("comparision", $value)) {
                $widget->setWhereClauseCondition($value['comparision']);

                if (is_array($value)) {
                    $value1 = next($value);
                    if ($value1 === false) {
                        $value1 = null;
                    }
                    $value2 = next($value);
                    if ($value2 === false) {
                        $value2 = null;
                    }
                } else {
                    $value1 = next($value);
                    $value2 = null;
                }
            } else {
                if (is_array($value)) {
                    $value1 = current($value);
                } else {
                    $value1 = $value;
                }
                $value2 = null;
            }

            $whereClausePart = $widget->generateWhereClausePart("fieldName", $value);
            if ($whereClausePart == null) {
                $whereCondition = null;
            } else {
                $whereCondition = $widget->getWhereClauseCondition();
            }

            $this->getReportableService()->saveSelectedFilterField($reportId, $filterFieldId, $filterFieldOrder, $value1, $value2, $whereCondition, $type);
        }
    }

    public function saveSelectedDisplayFields($displayFieldIds, $reportId) {
        // TODO
        $reportableService = $this->getReportableService();
        $reportableService->removeSelectedDisplayFields($reportId);

        foreach ($displayFieldIds as $displayFieldId) {
            $reportableService->saveSelectedDispalyField($displayFieldId, $reportId);
        }
    }

    public function saveSelectedDisplayFieldGroups($displayFieldGroupIds, $reportId) {
        // TODO
        $reportableService = $this->getReportableService();
        $reportableService->removeSelectedDisplayFieldGroups($reportId);

        foreach ($displayFieldGroupIds as $displayFieldGroupId) {
            $reportableService->saveSelectedDisplayFieldGroup($displayFieldGroupId, $reportId);
        }
    }

    /**
     *
     * @param <type> $customField
     * @param <type> $reportGroupId
     * @return <type>
     */
    public function saveCustomDisplayField($customField, $reportGroupId) {
        // TODO
        $reportableService = $this->getReportableService();
        $reportableService->removeSelectedDisplayFieldGroups($reportId);

        $customFieldNo = $customField->getFieldNum();
        $name = "hs_hr_employee.custom" . $customFieldNo;

        $displayField = $this->getReportableService()->getDisplayFieldByName($name);

        if ($displayField != null) {
            $columns['displayFieldId'] = $displayField[0]->getDisplayFieldId();
        }

        $columns['reportGroupId'] = $reportGroupId;
        $columns['name'] = $name;
        $columns['label'] = $customField->getName();
        $columns['fieldAlias'] = "customField" . $customFieldNo;
        $columns['isSortable'] = "false";
        $columns['sortOrder'] = null;
        $columns['sortField'] = null;
        $columns['elementType'] = "label";
        $columns['elementProperty'] = "<xml><getter>customField" . $customFieldNo . "</getter></xml>";
        $columns['width'] = "200";
        $columns['isExportable'] = "1";
        $columns['textAlignmentStyle'] = null;
        $columns['isValueList'] = "0";
        $columns['displayFieldGroupId'] = "16";
        $columns['defaultValue'] = "---";
        $columns['isEncrypted'] = false;

        return $this->getReportableService()->saveCustomDisplayField($columns);
    }

    public function deleteCustomDisplayFieldList($customFieldList) {
        // TODO
        foreach ($customFieldList as $customField) {
            $customDisplayFieldName = "hs_hr_employee.custom" . $customField;
            $result = $this->getReportableService()->deleteCustomDisplayField($customDisplayFieldName);
        }
    }

    /**
     * Gets all display field groups for given report group
     */
    public function getGroupedDisplayFieldsForReportGroup($reportGroupId) {
        // TODO
        $displayFields = $this->getReportableService()->getDisplayFieldsForReportGroup($reportGroupId);

        $groups = $this->getGroupedDisplayFields($displayFields);

        return $groups;
    }

    public function getGroupedDisplayFields($displayFields) {
        // TODO
        // Organize by groups
        $groups = [];
        $defaultDisplayFieldGroup = new DisplayFieldGroup();
        $defaultDisplayFieldGroup->setIsList(false);

        $defaultGroup = [$defaultDisplayFieldGroup, []];

        foreach ($displayFields as $field) {

            $displayGroupId = $field->getDisplayFieldGroupId();

            if (empty($displayGroupId)) {
                $defaultGroup[1][] = $field;
            } else {

                if (!isset($groups[$displayGroupId])) {
                    $displayFieldGroup = $field->getDisplayFieldGroup();
                    $groups[$displayGroupId] = [$displayFieldGroup, [$field]];
                } else {
                    $groups[$displayGroupId][1][] = $field;
                }
            }
        }

        // Add the default group if it has any fields
        if (count($defaultGroup[1]) > 0) {
            $groups[] = $defaultGroup;
        }

        return $groups;
    }

    /**
     * @param int $reportId
     * @return Header
     */
    public function getHeaderDefinitionByReportId(int $reportId): Header
    {
        $header = new Header($this->getHeaderGroups($reportId));
        $report = $this->getReportGeneratorDao()->getReport($reportId);
        $header->setMeta(new ParameterBag(['name' => $report->getName()]));
        return $header;
    }

    public function getNormalizedReportData(ReportSearchFilterParams $filterParams)
    {
        // TODO:: Support for time, attendance, currently only supports for PIM reports
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->from(Employee::class, 'employee');

        $displayFields = $this->getReportGeneratorDao()->getSelectedDisplayFieldsByReportId($filterParams->getReportId());
        $combinedDisplayFields = [];
        $listedDisplayFields = [];
        $joinAliases = [];
        $displayFieldGroups = [];

        foreach ($displayFields as $displayField) {
            $displayFieldClassName = $displayField->getClassName();
            $displayFieldClass = new $displayFieldClassName();
            if ($displayFieldClass instanceof \OrangeHRM\Core\Report\DisplayField\DisplayField) {
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
        $selectedFilterFields = $this->getReportGeneratorDao()->getSelectedFilterFieldsByReportId(
            $filterParams->getReportId()
        );
        foreach ($selectedFilterFields as $selectedFilterField) {
            $filterField = $selectedFilterField->getFilterField();
            $filterFieldClassName = $filterField->getClassName();
            $filterFieldClass = new $filterFieldClassName(
                $selectedFilterField->getOperator(),
                $selectedFilterField->getX(),
                $selectedFilterField->getY(),
                $selectedFilterField->getFilterFieldOrder()
            );
            if ($filterFieldClass instanceof FilterField) {
                $filterFieldClass->addWhereToQueryBuilder($queryBuilderWrapper);
                array_push($joinAliases, ...$filterFieldClass->getEntityAliases());
            }
        }

        $qb->groupBy('employee.empNumber');
        $this->setJoinsToQueryBuilder($queryBuilderWrapper, array_unique($joinAliases));
        $this->setSortingAndPaginationParams($queryBuilderWrapper, $filterParams);

        $results = $qb->getQuery()->execute();
        // Normalize DTO objects
        foreach ($results as $i => $result) {
            foreach ($combinedDisplayFields as $combinedDisplayField) {
                if ($result[$combinedDisplayField] instanceof \Stringable) {
                    $results[$i][$combinedDisplayField] = (string)$result[$combinedDisplayField];
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
     * @param QueryBuilderWrapper $queryBuilderWrapper
     * @param FilterParams $filterParams
     */
    protected function setSortingAndPaginationParams(QueryBuilderWrapper $queryBuilderWrapper, FilterParams $filterParams):void
    {
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
    protected function setJoinsToQueryBuilder(QueryBuilderWrapper $queryBuilderWrapper, array $joinAliases):void
    {
        foreach ($joinAliases as $joinAlias) {
            $this->setJoinToQueryBuilder($queryBuilderWrapper, $joinAlias);
        }
    }

    /**
     * @param QueryBuilderWrapper $queryBuilderWrapper
     * @param string $joinAlias
     */
    protected function setJoinToQueryBuilder(QueryBuilderWrapper $queryBuilderWrapper, string $joinAlias):void
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
}
