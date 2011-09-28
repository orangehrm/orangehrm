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
class ReportGeneratorService {

    // ReportableService Data Access Object
    private $reportableService;

    /**
     * Gets the ReportableService Data Access Object
     * @return ReportableService
     */
    public function getReportableService() {

        if (is_null($this->reportableService)) {
            $this->reportableService = new ReportableService();
        }

        return $this->reportableService;
    }

    /**
     * Sets ReportableService Data Access Object
     * @param ReportableService $ReportableService
     * @return void
     */
    public function setReportableService(ReportableService $ReportableService) {

        $this->reportableService = $ReportableService;
    }

    /**
     * Gets ids of the selectedFilterFields of a report, given the report id.
     * @param integer $reportId
     * @return array
     */
    public function getSelectedFilterFieldIdsByReportId($reportId) {

        $selectedFilterFields = $this->getReportableService()->getSelectedFilterFields($reportId);

        if ($selectedFilterFields != null) {

            $selectedFilterFieldIdArray = array();

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

        $reportGroupId = $this->getReportGroupIdOfAReport($reportId);
        $selectedFilterFieldIds = $this->getSelectedFilterFieldIdsByReportId($reportId);

        if (($reportGroupId != null) && ($selectedFilterFieldIds != null)) {
            $type = PluginAvailableFilterField::RUNTIME_FILTER_FIELD;

            $runtimeFilterFields = $this->getReportableService()->getRuntimeFilterFields($reportGroupId, $type, $selectedFilterFieldIds);

            if ($runtimeFilterFields == null) {
                return null;
            }

            $orderedRuntimeFilterFields = $this->orderRuntimeFilterFields($selectedFilterFieldIds, $runtimeFilterFields);

            $runtimeFilterFieldWidgetNamesAndLabels = array();

            foreach ($orderedRuntimeFilterFields as $runtimeFilterField) {
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

        $conditionArray = array();

        foreach ($selectedRuntimeFilterFields as $runtimeFilterField) {

            $labelName = $runtimeFilterField->getName();
            $widgetName = $runtimeFilterField->getFilterFieldWidget();
            $widget = new $widgetName(array(), array('id' => $labelName));

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

        $reportGroupId = $this->getReportGroupIdOfAReport($reportId);
        $selectedFilterFieldIds = $this->getSelectedFilterFieldIdsByReportId($reportId);

        if ($selectedFilterFieldIds == null) {
            return null;
        }

        $type = PluginAvailableFilterField::RUNTIME_FILTER_FIELD;
        $selectedRuntimeFilterFieldList = $this->getReportableService()->getRuntimeFilterFields($reportGroupId, $type, $selectedFilterFieldIds);

        if ($selectedRuntimeFilterFieldList == null) {
            return null;
        }

        $runtimeFilterFields = $this->orderRuntimeFilterFields($selectedFilterFieldIds, $selectedRuntimeFilterFieldList);

        return $runtimeFilterFields;
    }

    /**
     * Generates select condition excluding summary function for a given report (ie. given report id).
     * @param integer $reportId
     * @return string 
     */
    public function getSelectConditionWithoutSummaryFunction($reportId) {

        $selectCondition = null;

        $selectStatementPartWithSelectedDisplayFields = $this->constructSelectStatementPartWithSelectedDisplayFields($reportId);
        $selectCondition = $selectStatementPartWithSelectedDisplayFields;

        $selectStatementWithPartMetaDisplayFields = $this->constructSelectStatementPartWithMetaDisplayFields($reportId);

        if ($selectStatementWithPartMetaDisplayFields != null) {
            if ($selectCondition != null) {
                $selectCondition = $selectCondition . " , " . $selectStatementWithPartMetaDisplayFields;
            } else {
                $selectCondition = $selectStatementWithPartMetaDisplayFields;
            }
        }

        $selectStatementWithPartCompositeDisplayFields = $this->constructSelectStatementPartWithCompositeDisplayField($reportId);

        if ($selectStatementWithPartCompositeDisplayFields != null) {
            if ($selectCondition != null) {
                $selectCondition = $selectStatementWithPartCompositeDisplayFields . " , " . $selectCondition;
            } else {
                $selectCondition = $selectStatementWithPartCompositeDisplayFields;
            }
        }

        return $selectCondition;
    }

    /**
     * Constructs select statement part with selected display fields.
     * @param integer $reportId
     * @return string
     */
    public function constructSelectStatementPartWithSelectedDisplayFields($reportId) {

        $selectedDisplayFields = $this->getReportableService()->getSelectedDisplayFields($reportId);

        $selectStatement = null;

        if ($selectedDisplayFields != null) {

            foreach ($selectedDisplayFields as $selectedDisplayField) {

                $displayField = $selectedDisplayField->getDisplayField();

                $selectStatement = $this->constructSelectStatementPartUsingDisplayField($selectStatement, $displayField);
            }
        }

        return $selectStatement;
    }

    /**
     * Appends display field names to select statement.
     * @param string $selectStatement
     * @param DisplayField $displayField
     * @return string 
     */
    private function constructSelectStatementPartUsingDisplayField($selectStatement, $displayField) {

        if ($selectStatement == null) {

            $selectStatement = $displayField->getName();

            if (!$displayField->getFieldAlias() == null) {
                $selectStatement = $selectStatement . " AS " . $displayField->getFieldAlias();
            }
        } else {
            $selectStatement = $selectStatement . "," . $displayField->getName();
            if (!$displayField->getFieldAlias() == null) {
                $selectStatement = $selectStatement . " AS " . $displayField->getFieldAlias();
            }
        }

        return $selectStatement;
    }

    /**
     * Constructs select statement part with composite display fields.
     * @param string $statement
     * @param integer $reportId
     * @return string 
     */
    public function constructSelectStatementPartWithCompositeDisplayField($reportId) {

        $selectedCompositeDisplayFields = $this->getReportableService()->getSelectedCompositeDisplayFields($reportId);

        $selectStatement = null;

        if ($selectedCompositeDisplayFields != null) {

            foreach ($selectedCompositeDisplayFields as $selectedCompositeDisplayField) {

                $compositeDisplayField = $selectedCompositeDisplayField->getCompositeDisplayField();

                $selectStatement = $this->constructSelectStatementPartUsingDisplayField($selectStatement, $compositeDisplayField);
            }
        }

        return $selectStatement;
    }

    /**
     * Constructs select statement part with meta display fields.
     * @param integer $reportId
     * @return string 
     */
    public function constructSelectStatementPartWithMetaDisplayFields($reportId) {

        $metaDisplayFields = $this->getReportableService()->getMetaDisplayFields($reportId);

        $selectStatement = null;

        if ($metaDisplayFields != null) {

            foreach ($metaDisplayFields as $metaDisplayField) {

                $displayField = $metaDisplayField->getDisplayField();

                $selectStatement = $this->constructSelectStatementPartUsingDisplayField($selectStatement, $displayField);
            }
        }

        return $selectStatement;
    }

    /**
     * Generates data set for a given sql.
     * @param string $sql
     * @return string[]
     */
    public function generateReportDataSet($sql) {

        $dataSet = $result = $this->getReportableService()->executeSql($sql);

        return $dataSet;
    }

    /**
     * Generates all headers that are to be used in the list component for a given report.
     * @param integer $reportId
     * @return ListHeader[]
     */
    public function getHeaders($reportId) {

        $headers = array();

        $headerNo = 1;

        $this->generateCompositeDisplayFieldHeaders($reportId, $headers, $headerNo);
        $this->generateSelectedDisplayFieldHeaders($reportId, $headers, $headerNo);
        $this->generateSummaryDisplayFieldHeaders($reportId, $headers, $headerNo);

        return $headers;
    }

    /**
     * Generates headers for composite display fields that are to be used in the list component.
     * @param integer $reportId
     * @param ListHeader[] $headers
     * @return ListHeader[]
     */
    private function generateCompositeDisplayFieldHeaders($reportId, &$headers, &$headerNo) {

        $selectedCompositeDisplayFields = $this->getReportableService()->getSelectedCompositeDisplayFields($reportId);

        if ($selectedCompositeDisplayFields != null) {
            foreach ($selectedCompositeDisplayFields as $selectedCompositeDisplayField) {

                $compositeDisplayField = $selectedCompositeDisplayField->getCompositeDisplayField();

                $this->setHeaderProperties($compositeDisplayField, $headers, $headerNo);
            }
        }
    }

    private function generateSummaryDisplayFieldHeaders($reportId, &$headers, &$headerNo) {

        $selectedGroupField = $this->getReportableService()->getSelectedGroupField($reportId);

        if ($selectedGroupField != null) {
            $summaryDisplayField = $selectedGroupField->getSummaryDisplayField();
            $this->setHeaderProperties($summaryDisplayField, $headers, $headerNo);
        }
    }

    private function generateSelectedDisplayFieldHeaders($reportId, &$headers, &$headerNo) {

        $selectedDisplayFields = $this->getReportableService()->getSelectedDisplayFields($reportId);

        if ($selectedDisplayFields != null) {
            foreach ($selectedDisplayFields as $selectedDisplayField) {

                $displayField = $selectedDisplayField->getDisplayField();

                $this->setHeaderProperties($displayField, $headers, $headerNo);
            }
        }
    }

    private function setHeaderProperties($displayField, &$headers, &$headerNo) {

        if ($displayField->getIsSortable() == "false") {
            $isSorbale = false;
        } else {
            $isSorbale = true;
        }

        $properties = array(
            'name' => $displayField->getLabel(),
            'isSortable' => $isSorbale,
            'sortOrder' => $displayField->getSortOrder(),
            'sortField' => $displayField->getSortField(),
            'elementType' => $displayField->getElementType(),
            'width' => $displayField->getWidth(),
            'isExportable' => $displayField->getIsExportable(),
            'textAlignmentStyle' => $displayField->getTextAlignmentStyle()
        );

        $properties = array_filter($properties, 'strlen');

        $elementPropertyXmlString = $this->escapeSpecialCharacters($displayField->getElementProperty());

        $xmlIterator = new SimpleXMLIterator($elementPropertyXmlString);

        $elementPropertyArray = $this->simplexmlToArray($xmlIterator);
        $elementPropertyArray['default'] = $displayField->getDefaultValue();

        $properties['elementProperty'] = $elementPropertyArray;
        $temp = "header" . $headerNo;

        ${$temp} = new ListHeader;
        ${$temp}->populateFromArray($properties);

        $headerNo++;

        $headers[] = ${$temp};
    }

    /*
     * NOTE :
     * There is a bug in the installer. If there is any semicolon in a string that we insert into the database,
     * the installer interpret it in a wrong way and bread. That occurs when the installer run the dbscript-2.sql file.
     * So this method replaces the "#" character with "&amp;" string.
     */

    private function escapeSpecialCharacters($string) {

        $string = str_replace("#", "&amp;", $string);

        return $string;
    }

    /**
     * Converts SimpleXMLIterator object into an array.
     * @param  SimpleXMLIterator $xmlIterator
     * @return string[]
     */
    public function simplexmlToArray($xmlIterator) {

        $xmlStringArray = array();

        for ($xmlIterator->rewind(); $xmlIterator->valid(); $xmlIterator->next()) {

            if ($xmlIterator->hasChildren()) {
                $object = $xmlIterator->current();
                $xmlStringArray[$object->getName()] = $this->simplexmlToArray($object);
            } else {
                $object = $xmlIterator->current();
                $xmlStringArray[$object->getName()] = (string) $xmlIterator->current();
            }
        }

        return $xmlStringArray;
    }

    /**
     * Generates a complete sql to retrieve report data set.
     * @param integer $reportId
     * @param array $conditionArray
     * @return string 
     */
    public function generateSql($reportId, $conditionArray, $staticColumns = null) {

        $report = $this->getReportableService()->getReport($reportId);
        $reportGroupId = $report->getReportGroupId();

        $reportGroup = $this->getReportableService()->getReportGroup($reportGroupId);
        $coreSql = $reportGroup->getCoreSql();

        $selectStatement = $this->getSelectConditionWithoutSummaryFunction($reportId);
        $selectedGroupField = $this->getReportableService()->getSelectedGroupField($reportId);
        $summaryDisplayField = null;

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
            $sql = str_replace($searchString, $condition, $sql);
        }

        $pattern = "/whereCondition\d+/";

        $sql = preg_replace($pattern, "true", $sql);

        if (isSet($groupByClause)) {
            $sql = $sql . " " . $groupByClause;
        }

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

        $filterFieldIdAndValueArray = array();

        if ($selectedRuntimeFilterFieldList[0]->getFilterFieldId() != null) {

            foreach ($selectedRuntimeFilterFieldList as $runtimeFilterField) {

                $filterFieldId = $runtimeFilterField->getFilterFieldId();
                $value = $formValues[$runtimeFilterField->getName()];

                $filterFieldIdAndValueArray[] = array("filterFieldId" => $filterFieldId, "value" => $value);
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
    public function generateWhereClauseConditionArray($filterFieldIdsAndValues) {

        $conditionArray = array();

        if (!empty($filterFieldIdsAndValues)) {

            foreach ($filterFieldIdsAndValues as $filterFieldIdAndValue) {

                $runtimeFilterField = $this->getReportableService()->getFilterFieldById($filterFieldIdAndValue["filterFieldId"]);

                $labelName = $runtimeFilterField->getName();
                $widgetName = $runtimeFilterField->getFilterFieldWidget();
                $widget = new $widgetName(array(), array('id' => $labelName));

                $conditionNo = $runtimeFilterField->getConditionNo();

                if (array_key_exists($conditionNo, $conditionArray)) {

                    if ($widget->generateWhereClausePart($runtimeFilterField->getWhereClausePart(), $filterFieldIdAndValue["value"]) != null) {
                        $conditionArray[$conditionNo] = $conditionArray[$conditionNo] . " AND " . $widget->generateWhereClausePart($runtimeFilterField->getWhereClausePart(), $filterFieldIdAndValue["value"]);
                    }
                } else {
                    if ($widget->generateWhereClausePart($runtimeFilterField->getWhereClausePart(), $filterFieldIdAndValue["value"]) != null) {
                        $conditionArray[$conditionNo] = $widget->generateWhereClausePart($runtimeFilterField->getWhereClausePart(), $filterFieldIdAndValue["value"]);
                    }
                }
            }

            return $conditionArray;
        } else {
            return null;
        }
    }

    /**
     * Gets project activity name for a given activity id.
     * @param integer $activityId
     * @return string
     */
    public function getProjectActivityNameByActivityId($activityId) {

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

        $report = $this->getReportableService()->getReport($reportId);
        $reportName = $report->getName();

        return $reportName;
    }

    public function generateSqlForNotUseFilterFieldReports($reportId, $formValues) {

        $report = $this->getReportableService()->getReport($reportId);
        $reportGroupId = $report->getReportGroupId();

        $reportGroup = $this->getReportableService()->getReportGroup($reportGroupId);
        $coreSql = $reportGroup->getCoreSql();

        $selectStatement = $this->getSelectConditionWithoutSummaryFunction($reportId);
        $selectedGroupField = $this->getReportableService()->getSelectedGroupField($reportId);
        $summaryDisplayField = null;

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

        if (isSet($groupByClause)) {
            $sql = $sql . " " . $groupByClause;
        }
        
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
        print_r($sql);
//        preg_match($pattern, $str, $matches);
//        print_r($matches);
//        $value = preg_replace_callback($pattern, array(&$this, 'my_name'), $str);
//        print_r($value);
    }

    public function my_name($matches) {
        $value = "no";
        $array = explode("@", $matches[0]);

        if ($value == null) {
            return $array[3];
        }
        return $array[1];
    }

}

