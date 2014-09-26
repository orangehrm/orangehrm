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
class ReportGeneratorService extends BaseService {
    const LIST_SEPARATOR = "|\n|";

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

        $selectedFilterFields = $this->getReportableService()->getSelectedFilterFields($reportId, true);

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

        $type = PluginSelectedFilterField::RUNTIME_FILTER_FIELD;
        $runtimeSelectedFilterFields = $this->getReportableService()->getSelectedFilterFieldsByType($reportId, $type, true);

        if (($reportGroupId != null) && ($runtimeSelectedFilterFields != null)) {

            $runtimeFilterFieldWidgetNamesAndLabels = array();

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

        $selectCondition = null;

        $displayGroups = $this->getGroupedDisplayFieldsForReport($reportId);        
        $selectCondition = $this->constructSelectStatement($displayGroups);

        return $selectCondition;
    }
    
    public function getGroupedDisplayFieldsForReport($reportId) {
        $displayFields = $this->getSelectedDisplayFields($reportId);
        $metaFields = $this->getSelectedMetaDisplayFields($reportId);
        $compositeFields = $this->getSelectedCompositeDisplayFields($reportId);        

        $selectedDisplayFields = array_merge($displayFields, $compositeFields, $metaFields);

        $displayGroups = $this->getGroupedDisplayFields($selectedDisplayFields);    
        
        return $displayGroups;
    }
    
    public function getAllSelectedFieldsGrouped($reportId) {
        $displayFields = $this->getSelectedDisplayFields($reportId);
        $metaFields = $this->getSelectedMetaDisplayFields($reportId);
        $compositeFields = $this->getSelectedCompositeDisplayFields($reportId);        

        $selectedDisplayFields = array_merge($selectedDisplayFields, $compositeFields, $displayFields, $summaryFields);
        $headerGroups = array();

        // Default Group - for headers without a display group
        $defaultGroup = new DisplayFieldGroup();
        //$defaultGroup;

        $selectedDisplayGroupIds = array();
        $selectedDisplayGroups = $this->getReportableService()->getSelectedDisplayFieldGroups($reportId);

        if (!empty($selectedDisplayGroups)) {
            foreach ($selectedDisplayGroups as $group) {
                $selectedDisplayGroupIds[] = $group['display_field_group_id'];
            }
        }


        foreach ($displayFields as $displayField) {

            if ($displayField->getIsSortable() == "false") {
                $isSortable = false;
            } else {
                $isSortable = true;
            }

            if ($displayField->getIsValueList()) {
                $isValueList = true;
            } else {
                $isValueList = false;
            }

            $properties = array(
                'name' => $displayField->getLabel(),
                'isSortable' => $isSortable,
                'sortOrder' => $displayField->getSortOrder(),
                'sortField' => $displayField->getSortField(),
                'elementType' => $displayField->getElementType(),
                'width' => $displayField->getWidth(),
                'isExportable' => $displayField->getIsExportable(),
                'textAlignmentStyle' => $displayField->getTextAlignmentStyle(),
            );
        $elementPropertyArray['default'] = $displayField->getDefaultValue();

            $properties = array_filter($properties, 'strlen');

            $elementPropertyXmlString = $this->escapeSpecialCharacters($displayField->getElementProperty());

            $xmlIterator = new SimpleXMLIterator($elementPropertyXmlString);

            $elementPropertyArray = $this->simplexmlToArray($xmlIterator);

            $elementPropertyArray['isValueList'] = $isValueList;
            $elementPropertyArray['listSeparator'] = self::LIST_SEPARATOR;

            if ($displayField->getDefaultValue() != null) {
                $elementPropertyArray['default'] = $displayField->getDefaultValue();
            }

            $properties['elementProperty'] = $elementPropertyArray;

            $header = new ListHeader;
            $header->populateFromArray($properties);

            // Set to correct display group
            $displayGroupId = $displayField->getDisplayFieldGroupId();

            if ($displayGroupId === null) {
                $defaultGroup->addHeader($header);
            } else {
                if (!isset($headerGroups[$displayGroupId])) {
                    $displayFieldGroup = $displayField->getDisplayFieldGroup();

                    if (in_array($displayGroupId, $selectedDisplayGroupIds)) {
                        $groupName = $displayFieldGroup->getName();
                    } else {
                        $groupName = null;
                    }

                    // Check if group is selected in report.

                    $headerGroup = new ListHeaderGroup(array($header), $groupName);
                    $headerGroups[$displayGroupId] = $headerGroup;
                } else {
                    $headerGroups[$displayGroupId]->addHeader($header);
                }
            }
        }

        // Add the default group if it has any headers
        if ($defaultGroup->getHeaderCount() > 0) {
            $headerGroups[] = $defaultGroup;
        }

        return $headerGroups;        
    }

    /**
     * Appends display field names to select statement.
     * @param string $selectStatement
     * @param DisplayField $displayField
     * @return string 
     */
    public function constructSelectClauseForDisplayField($selectStatement, $displayField) {
        
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

        $dataSet = $this->getReportableService()->executeSql($sql);

        $dataSet = $this->processListsInDataSet($reportId, $dataSet);
        return $dataSet;
    }

    public function processListsInDataSet($reportId, $dataSet) {
        $displayGroups = $this->getGroupedDisplayFieldsForReport($reportId);
        
        for($rowNdx = 0; $rowNdx < count($dataSet); $rowNdx++) {
            $dataRow = $dataSet[$rowNdx];

            foreach ($displayGroups as $groupDetails) {
                
                $group = $groupDetails[0];
                $displayFields = $groupDetails[1];

                if ($group->getIsList() && count($displayFields) > 0) {
                    
                    $groupAlias = 'DisplayFieldGroup' . $group->getId();
                    
                    $groupValue = $dataRow[$groupAlias];
                    
                    $fieldValues = array();
                    
                    foreach($displayFields as $displayField) {
                        $fieldValues[$displayField->getFieldAlias()] = array();
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
     * Generates all headers that are to be used in the list component for a given report.
     * @param integer $reportId
     * @return ListHeader[]
     */
    public function getHeaderGroups($reportId) {

        $selectedDisplayFields = array();

        $compositeFields = $this->getSelectedCompositeDisplayFields($reportId);
        $summaryFields = $this->getSelectedSummaryDisplayFields($reportId);
        $displayFields = $this->getSelectedDisplayFields($reportId);

        $selectedDisplayFields = array_merge($selectedDisplayFields, $compositeFields, $displayFields, $summaryFields);

        $headerGroups = $this->getHeaderGroupsForDisplayFields($reportId, $selectedDisplayFields);

        return $headerGroups;
    }

    /**
     * Get list of selected composite display fields for given report
     * @param integer $reportId
     * @return CompositeDisplayField[]
     */
    private function getSelectedCompositeDisplayFields($reportId) {

        $compositeDisplayFields = array();

        $selectedCompositeDisplayFields = $this->getReportableService()->getSelectedCompositeDisplayFields($reportId);

        if ($selectedCompositeDisplayFields != null) {
            foreach ($selectedCompositeDisplayFields as $selectedCompositeDisplayField) {

                $compositeDisplayFields[] = $selectedCompositeDisplayField->getCompositeDisplayField();
            }
        }

        return $compositeDisplayFields;
    }

    /**
     * Get list of selected summary display fields for given report
     * @param integer $reportId
     * @return SummaryDisplayField[]
     */
    private function getSelectedSummaryDisplayFields($reportId) {

        $summaryDisplayFields = array();

        $selectedGroupField = $this->getReportableService()->getSelectedGroupField($reportId);

        if ($selectedGroupField != null) {
            $summaryDisplayFields[] = $selectedGroupField->getSummaryDisplayField();
        }

        return $summaryDisplayFields;
    }

    /**
     * Get list of selected selected display fields for given report
     * @param integer $reportId
     * @return SelectedDisplayField[]
     */
    private function getSelectedDisplayFields($reportId) {

        $displayFields = array();

        $selectedDisplayFields = $this->getReportableService()->getSelectedDisplayFields($reportId);

        if ($selectedDisplayFields != null) {
            foreach ($selectedDisplayFields as $selectedDisplayField) {
                $displayFields[] = $selectedDisplayField->getDisplayField();
            }
        }
        return $displayFields;
    }

    /**
     * Get list of selected selected display fields for given report
     * @param integer $reportId
     * @return SelectedDisplayField[]
     */
    private function getSelectedMetaDisplayFields($reportId) {

        $report = $this->getReportableService()->getReport($reportId);
        $reportGroupId = $report->getReportGroupId();
        $displayFields = array();
        
        $metaFields = $this->getReportableService()->getMetaDisplayFields($reportGroupId);
        
        if (!empty($metaFields)) {
            foreach ($metaFields as $displayField) {
                $displayFields[] = $displayField;
            }
        }

        return $displayFields;
    }
    
    /**
     * Get list of header groups for the given display fields.
     * @param array $displayFields Array of DisplayFields
     * @return array ListHeaderGroup 
     */
    private function getHeaderGroupsForDisplayFields($reportId, $displayFields) {

        $headerGroups = array();

        // Default Group - for headers without a display group
        $defaultGroup = new ListHeaderGroup(array());

        $selectedDisplayGroupIds = array();
        $selectedDisplayGroups = $this->getReportableService()->getSelectedDisplayFieldGroups($reportId);

        if (!empty($selectedDisplayGroups)) {
            foreach ($selectedDisplayGroups as $group) {
                $selectedDisplayGroupIds[] = $group['display_field_group_id'];
            }
        }


        foreach ($displayFields as $displayField) {

            if ($displayField->getIsSortable() == "false") {
                $isSortable = false;
            } else {
                $isSortable = true;
            }

            if ($displayField->getIsValueList()) {
                $isValueList = true;
            } else {
                $isValueList = false;
            }

            $properties = array(
                'name' => $displayField->getLabel(),
                'isSortable' => $isSortable,
                'sortOrder' => $displayField->getSortOrder(),
                'sortField' => $displayField->getSortField(),
                'elementType' => $displayField->getElementType(),
                'width' => $displayField->getWidth(),
                'isExportable' => $displayField->getIsExportable(),
                'textAlignmentStyle' => $displayField->getTextAlignmentStyle(),
            );
        $elementPropertyArray['default'] = $displayField->getDefaultValue();

            $properties = array_filter($properties, 'strlen');

            $elementPropertyXmlString = $this->escapeSpecialCharacters($displayField->getElementProperty());

            $xmlIterator = new SimpleXMLIterator($elementPropertyXmlString);

            $elementPropertyArray = $this->simplexmlToArray($xmlIterator);

            $elementPropertyArray['isValueList'] = $isValueList;
            $elementPropertyArray['listSeparator'] = self::LIST_SEPARATOR;

            if ($displayField->getDefaultValue() != null) {
                $elementPropertyArray['default'] = $displayField->getDefaultValue();
            }

            $properties['elementProperty'] = $elementPropertyArray;

            $header = new ListHeader;
            $header->populateFromArray($properties);

            // Set to correct display group
            $displayGroupId = $displayField->getDisplayFieldGroupId();

            if ($displayGroupId === null) {
                $defaultGroup->addHeader($header);
            } else {
                if (!isset($headerGroups[$displayGroupId])) {
                    $displayFieldGroup = $displayField->getDisplayFieldGroup();

                    if (in_array($displayGroupId, $selectedDisplayGroupIds)) {
                        $groupName = $displayFieldGroup->getName();
                    } else {
                        $groupName = null;
                    }

                    // Check if group is selected in report.

                    $headerGroup = new ListHeaderGroup(array($header), $groupName);
                    $headerGroups[$displayGroupId] = $headerGroup;
                } else {
                    $headerGroups[$displayGroupId]->addHeader($header);
                }
            }
        }

        // Add the default group if it has any headers
        if ($defaultGroup->getHeaderCount() > 0) {
            $headerGroups[] = $defaultGroup;
        }

        return $headerGroups;
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
    public function generateWhereClauseConditionArray($selectedFilterFields, $formValues) {

        $conditionArray = array();

        foreach ($selectedFilterFields as $selectedFilterField) {

            $type = $selectedFilterField->getType();
            $filterFieldId = $selectedFilterField->getFilterFieldId();

            if ($type == "Predefined") {
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
            } else if ($type == "Runtime") {
                $runtimeFilterField = $selectedFilterField->getFilterField();

                $labelName = $runtimeFilterField->getName();
                $widgetName = $runtimeFilterField->getFilterFieldWidget();
                $widget = new $widgetName(array(), array('id' => $labelName));
                $value = $formValues[$runtimeFilterField->getName()];

                $conditionNo = $runtimeFilterField->getConditionNo();

                if (array_key_exists($conditionNo, $conditionArray)) {

                    if ($widget->generateWhereClausePart($runtimeFilterField->getWhereClausePart(), $value) != null) {
                        $conditionArray[$conditionNo] = $conditionArray[$conditionNo] . " AND " . $widget->generateWhereClausePart($runtimeFilterField->getWhereClausePart(), $value);
                    }
                } else {
                    if ($widget->generateWhereClausePart($runtimeFilterField->getWhereClausePart(), $value) != null) {
                        $conditionArray[$conditionNo] = $widget->generateWhereClausePart($runtimeFilterField->getWhereClausePart(), $value);
                    }
                }
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

        $reportName = NULL;

        $report = $this->getReportableService()->getReport($reportId);

        if (!empty($report)) {
            $reportName = $report->getName();
        }

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
        $whereClausePart = $selectedFilterField->getFilterField()->getWhereClausePart();
        $value1 = $selectedFilterField->getValue1();
        $whereClause = $whereClausePart . " " . $whereCondition . " '" . $value1 . "'";
        return $whereClause;
    }

    public function constructWhereStatementForBetweenOperator($selectedFilterField, $whereCondition) {
        $whereClausePart = $selectedFilterField->getFilterField()->getWhereClausePart();
        $value1 = $selectedFilterField->getValue1();
        $value2 = $selectedFilterField->getValue2();
        $whereClause = $whereClausePart . " BETWEEN '" . $value1 . "' AND '" . $value2 . "'";
        return $whereClause;
    }

    public function constructWhereStatementForInOperator($selectedFilterField, $whereCondition){
        $whereClausePart = $selectedFilterField->getFilterField()->getWhereClausePart();
        $value1 = $selectedFilterField->getValue1();
        $whereClause = $whereClausePart . " " . $whereCondition . " " . "(" . $value1 . ")";
        return $whereClause;
    }

    public function constructWhereStatementForIsNullOperator($selectedFilterField, $whereCondition){
        $whereClausePart = $selectedFilterField->getFilterField()->getWhereClausePart();
        $whereClause = $whereClausePart . " IS NULL";
        return $whereClause;
    }
    
    public function constructWhereStatementForIsNotNullOperator($selectedFilterField, $whereCondition){
        $whereClausePart = $selectedFilterField->getFilterField()->getWhereClausePart();
        $whereClause = $whereClausePart . " IS NOT NULL";
        return $whereClause;
    }
    
    public function saveSelectedFilterFields($formValues, $reportId, $type) {

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

        $reportableService = $this->getReportableService();
        $reportableService->removeSelectedDisplayFields($reportId);

        foreach ($displayFieldIds as $displayFieldId) {
            $reportableService->saveSelectedDispalyField($displayFieldId, $reportId);
        }
    }

    public function saveSelectedDisplayFieldGroups($displayFieldGroupIds, $reportId) {

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

        foreach ($customFieldList as $customField) {
            $customDisplayFieldName = "hs_hr_employee.custom" . $customField;
            $result = $this->getReportableService()->deleteCustomDisplayField($customDisplayFieldName);
        }
    }
    
    /**
     * Gets all display field groups for given report group
     */
    public function getGroupedDisplayFieldsForReportGroup($reportGroupId) {
        $displayFields = $this->getReportableService()->getDisplayFieldsForReportGroup($reportGroupId);

        $groups = $this->getGroupedDisplayFields($displayFields);
        
        return $groups;
    }
    
    public function getGroupedDisplayFields($displayFields) {
        
        // Organize by groups
        $groups = array();
        $defaultDisplayFieldGroup = new DisplayFieldGroup();
        $defaultDisplayFieldGroup->setIsList(false);
        
        $defaultGroup = array($defaultDisplayFieldGroup, array());
        
        foreach ($displayFields as $field) {
            
            $displayGroupId = $field->getDisplayFieldGroupId();
            
            if (empty($displayGroupId)) {
                $defaultGroup[1][] = $field;
            } else {

                if (!isset($groups[$displayGroupId])) {
                    $displayFieldGroup = $field->getDisplayFieldGroup();
                    $groups[$displayGroupId] = array($displayFieldGroup, array($field));
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

}
