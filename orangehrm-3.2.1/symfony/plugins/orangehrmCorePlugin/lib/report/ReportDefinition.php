<?php

/**
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * You should have received a copy of the OrangeHRM Enterprise  proprietary license file along
 * with this program; if not, write to the OrangeHRM Inc. 538 Teal Plaza, Secaucus , NJ 0709
 * to get the file.
 *
 */

/**
 * Report Definition class 
 *
 */
class ReportDefinition {

    private $xml;
    private $subReportCount = 0;
    private $joinByCount = 0;
    private $joinCount = 0;
    private $decoratorCount = 0;
    private $displayGroupCount = 0;
    private $fieldCount = 0;
    private $inputFieldCount = 0;

    function __construct($xmlstr) {
        libxml_use_internal_errors(true);
        $this->xml = simplexml_load_string($xmlstr);
        if (empty($this->xml)) {
            $errorMessage = "";
            foreach (libxml_get_errors() as $error) {
                $errorMessage .= "\t" . $error->message;
            }
            throw new ReportException("Bad XML : " . $errorMessage);
        }
        libxml_use_internal_errors(false);
    }

    /**
     * Gets XML.
     * 
     * @return SimpleXMLElement (report xml)
     */
    public function getXml() {
        return $this->xml;
    }

    /**
     * Gets next subReport that is in the XML.
     * 
     * @return SimpleXMLElement (sub_report)
     */
    public function getNextSubReport() {
        $this->subReportCount++;
        $subReport = $this->xml->sub_report[$this->subReportCount];
        return $subReport;
    }

    /**
     * Sets the number of sub report count to zero.
     */
    public function resetSubReport() {
        $this->subReportCount = 0;
    }

    /**
     * Gets the current subReport that is pointed by sub report count.
     * 
     * @return SimpleXMLElement (sub_report)
     */
    public function getCurrentSubReport() {
        $currentReport = $this->xml->sub_report[$this->subReportCount];
        return $currentReport;
    }

    /**
     * Gets the query of a given sub report.
     * 
     * @param SimpleXMLElement $subReport 
     * @return string (query)
     */
    public function getSubReportQuery($subReport) {
        $query = (string) $subReport->query;
        return $query;
    }

    /**
     * Gets the id of a given sub report.
     * 
     * @param SimpleXMLElement $subReport
     * @return string (sub_report)
     */
    public function getSubReportIdField($subReport) {
        $idField = (string) $subReport->id_field;
        return $idField;
    }

    /**
     * Gets the type of a given sub report.
     * 
     * @param SimpleXMLElement $subReport
     * @return string (sub_report)
     */
    public function getSubReportType($subReport) {
        $subReportType = (string) $subReport->attributes()->type;
        return $subReportType;
    }

    /**
     * Gets the name of a given sub report.
     * 
     * @param SimpleXMLElement $subReport
     * @return string (sub_report)
     */
    public function getSubReportName($subReport) {
        $subReportName = (string) $subReport->attributes()->name;
        return $subReportName;
    }

    /**
     * Gets all joins in the report. 
     * 
     * @return SimpleXMLElement[] (join)
     */
    public function getJoin() {
        $join = $this->xml->join;
        return $join;
    }

    /**
     * Sets the number of join count to zero.
     */
    public function resetJoin() {
        $this->joinCount = 0;
    }

    /**
     * Gets the current join that is pointed by join count.
     * 
     * @return SimpleXMLElement (join)
     */
    public function getCurrentJoin() {
        $currentJoin = $this->xml->join[$this->joinCount];
        return $currentJoin;
    }

    /**
     * Gets the next joint that is pointed by join count.
     * 
     * @return SimpleXMLElement (join)
     */
    public function getNextJoin() {
        $this->joinCount++;
        $nextJoin = $this->xml->join[$this->joinCount];
        return $nextJoin;
    }

    /**
     * Gets total number of joins defined in report.
     * 
     * @return integer 
     */
    public function getJoinCount() {
        $joinCount = $this->xml->join->count();
        return $joinCount;
    }

    /**
     * Gets join as attribute of a given join element.
     * 
     * @param SimpleXMLElement $join 
     * @return SimpleXMLElement (sub_report)
     */
    public function getJoinAs($join) {
        $as = (string) $join->attributes()->as;
        return $as;
    }

    /**
     * Gets current join by element that is defined under a given join element.
     * @param SimpleXMLElement $join
     * @return SimpleXMLElement
     */
    public function getCurrentJoinBy($join) {
        $currentJoinBy = $join->join_by[$this->joinByCount];
        return $currentJoinBy;
    }

    /**
     * Gets next join by element that is defined under a given join element.
     * @param SimpleXMLElement $join
     * @return SimpleXMLElement
     */
    public function getNextJoinBy($join) {
        $this->joinByCount++;
        $joinBy = $join->join_by[$this->joinByCount];
        return $joinBy;
    }

   /**
    * Resets join by count.
    */
    public function resetJoinBy() {
        $this->joinByCount = 0;
    }

   /**
    * Gets the number of join by elements that are defined under a given
    * join element.
    * @param SimpleXMLElement $join
    * @return int
    */
    public function getJoinByCount($join) {
        $numOfJoinBy = $join->join_by->count();
        return $numOfJoinBy;
    }

    /**
     * Gets the value of sub report attribute of a given join by element.
     * @param SimpleXMLElement $joinBy
     * @return string
     */
    public function getJoinBySubReport($joinBy) {
        $subReport = (string) $joinBy->attributes()->sub_report;
        return $subReport;
    }

    /**
     * Gets the value of id attribute of a given join by element.
     * @param SimpleXMLElement $joinBy
     * @return string
     */
    public function getJoinById($joinBy) {
        $id = (string) $joinBy->attributes()->id;
        return $id;
    }

    /**
     * 
     * @return type
     */
    public function getDecorators() {
        $decorators = $this->xml->decorators;
        return $decorators;
    }

    /**
     * 
     * @return type
     */
    public function getNextDecorator() {
        $this->decoratorCount++;
        $decorator = $this->xml->decorators->decorator[$this->decoratorCount];
        return $decorator;
    }

    /**
     * 
     * @return type
     */
    public function getCurrentDecorator() {
        $decorator = $this->xml->decorators->decorator[$this->decoratorCount];
        return $decorator;
    }

    /**
     * Resests decorator count
     */
    public function resetDecorators() {
        $this->decoratorCount = 0;
    }

    /**
     * 
     * @param type $decorator
     * @return type
     */
    public function getDecoratorName($decorator) {
        $name = (string) $decorator->decorator_name;
        return $name;
    }

    /**
     * 
     * @param type $decorator
     * @return type
     */
    public function getDecoratorField($decorator) {
        $field = (string) $decorator->field;
        return $field;
    }

    public function getDisplayGroups($subReport) {
        $displayGroups = $subReport->display_groups;
        return $displayGroups;
    }
    
    /**
     * Get SubReport field alias  
     * @param type $subReportName
     * @return string
     */
    public function getSubReportFields( $subReportName ){
        $fieldList = array();
        $subReportFields = $this->xml->xpath('//sub_report[@name="'.$subReportName.'"]/display_groups/display_group/fields/field[@display="true"]/field_alias');
       
        foreach($subReportFields as $node) {
           $fieldList[(string)$node] = null ;
        }
        return $fieldList;
    }

    /**
     * Gets the current display group element that is defined under a given 
     * display groups element.
     * @param SimpleXMLElement $displayGroups
     * @return SimpleXMLElement
     */
    public function getCurrentDisplayGroup($displayGroups) {
        $displayGroup = $displayGroups->display_group[$this->displayGroupCount];
        return $displayGroup;
    }

    /**
     * Gets the value of name attribute of a given display group element.
     * @param SimpleXMLElement $displayGroup
     * @return string
     */
    public function getDisplayGroupName($displayGroup) {
        $displayGroupName = (string) $displayGroup->attributes()->name;
        return $displayGroupName;
    }

    /**
     * Gets the value of type attribute of a given display group element.
     * @param SimpleXMLElement $displayGroup
     * @return string
     */
    public function getDisplayGroupType($displayGroup) {
        $displayGroupType = (string) $displayGroup->attributes()->type;
        return $displayGroupType;
    }

    /**
     * Gets all attribute values of a given display group.
     * @param SimpleXMLElement $displayGroup
     * @return array
     */
    public function getDisplayGroupAttributes($displayGroup) {
        $attributes = (array) $displayGroup->attributes();
        return $attributes["@attributes"];
    }

    /**
     * Resets display group count.
     */
    public function resetDisplayGroup() {
        $this->displayGroupCount = 0;
    }

    /**
     * Gets the next display group element that is defined under a given 
     * display groups element.
     * @param SimpleXMLElement $displayGroups
     * @return SimpleXMLElement
     */
    public function getNextDisplayGroup($displayGroups) {
        $this->displayGroupCount++;
        $displayGroup = $displayGroups->display_group[$this->displayGroupCount];
        return $displayGroup;
    }

    /**
     * Gets the value of group header element that is defined under a given 
     * display groups element.
     * @param SimpleXMLElement $displayGroup
     * @return string
     */
    public function getGroupHeader($displayGroup) {
        $groupHeader = (string) $displayGroup->group_header;
        return $groupHeader;
    }

    /**
     * Gets the value of display attribute of a given display group element.
     * @param SimpleXMLElement $displayGroup
     * @return string
     */
    public function getDisplayGroupDisplayAttr($displayGroup) {
        $displayGroupDisplayAttr = (string) $displayGroup->attributes()->display;
        return $displayGroupDisplayAttr;
    }

    /**
     * Gets the value of show header attribute of a given display group element.
     * @param SimpleXMLElement $displayGroup
     * @return string
     */
    public function getDisplayGroupShowHeaderAttr($displayGroup) {
        $displayGroupShowHeaderAttr = (string) $displayGroup->attributes()->show_header;
        return $displayGroupShowHeaderAttr;
    }

    /**
     * Gets all the fields defined in a given display group element.
     * @param SimpleXMLElement $displayGroup
     * @return SimpleXMLElement[]
     */
    public function getFields($displayGroup) {
        $fields = $displayGroup->fields;
        return $fields;
    }

    /**
     * Gets the current field element that is defined in the xml for a given Fields
     * element.
     * @param SimpleXMLElement[] $fields
     * @return SimpleXMLElement
     */
    public function getCurrentField($fields) {
        $field = $fields->field[$this->fieldCount];
        return $field;
    }

    /**
     * Resets filed count.
     */
    public function resetField() {
        $this->fieldCount = 0;
    }

    /**
     * Gets the next field element that is defined in the xml for a given Fields
     * element.
     * @param SimpleXMLElement[] $fields
     * @return SimpleXMLElement
     */
    public function getNextField($fields) {
        $this->fieldCount++;
        $field = $fields->field[$this->fieldCount];
        return $field;
    }

    /**
     * Gets the value of the display attribute of a given field element.
     * @param SimpleXMLElement $field
     * @return string
     */
    public function getFieldDisplayAttr($field) {
        $fieldDisplayAttr = (string) $field->attributes()->display;
        return $fieldDisplayAttr;
    }

    /**
     * Gets the value of the field name element of a given field element.
     * @param SimpleXMLElement $field
     * @return string
     */
    public function getFieldName($field) {
        $fieldName = (string) $field->field_name;
        return $fieldName;
    }

    /**
     * Gets the value of the field alias element of a given field element.
     * @param SimpleXMLElement $field
     * @return string
     */
    public function getFieldAlias($field) {
        $fieldAlias = (string) $field->field_alias;
        return $fieldAlias;
    }

    /**
     * Gets the value of the display name element of a given field element.
     * @param SimpleXMLElement $field
     * @return string
     */
    public function getFieldDisplayName($field) {
        $displayName = (string) $field->display_name;
        return $displayName;
    }

    /**
     * Gets the value of the width element of a given field element.
     * @param SimpleXMLElement $field
     * @return int
     */
    public function getFieldWidth($field) {
        $width = (string) $field->width;
        return $width;
    }
    
    public function getFieldLink($field) {
        $link = null;
        if (isset($field->link)) {
            $link = (string) $field->link;
        }
        return $link;
    }
    public function getFieldAlign($field) {
        $align = null;
        if (isset($field->align)) {
            $align = (string) $field->align;
        }
        return $align;
    }    

    /**
     * Gets the value of page limit element defined in the xml.
     * @return int
     */
    public function getPageLimit() {
        $pageLimit = (string) $this->xml->page_limit;
        return $pageLimit;
    }

    /**
     * Gets the value of csv setting of include group header.
     * @return string
     */
    public function getCsvIncludeGroupHeaderSetting() {
        $includeGroupHeader = (string) $this->xml->settings->csv->include_group_header;
        return $includeGroupHeader;
    }

    /**
     * Gets the value of csv setting of include header.
     * @return string
     */
    public function getCsvIncludeHeaderSetting() {
        $includeHeader = (string) $this->xml->settings->csv->include_header;
        return $includeHeader;
    }

    /**
     * Gets all input filter fields defined in the xml.
     * @return SimpleXMLElement[]
     */
    public function getFilterFields() {
        $filterFields = $this->xml->filter_fields;
        return $filterFields;
    }

    /**
     * Gets the current input field defined in the xml.
     * @return SimpleXMLElement
     */
    public function getCurrentInputField() {
        $currentInputField = $this->xml->filter_fields->input_field[$this->inputFieldCount];
        return $currentInputField;
    }

    /**
     * Gets the next input field defined in the xml.
     * @return SimpleXMLElement
     */
    public function getNextInputField() {
        $this->inputFieldCount++;
        $nextInputField = $this->xml->filter_fields->input_field[$this->inputFieldCount];
        return $nextInputField;
    }

    /**
     * Gets the value of type attribute of a given input field.
     * @param SimpleXMLElement $inputField
     * @return string
     */
    public function getInputFieldType($inputField) {
        $type = (string) $inputField->attributes()->type;
        return $type;
    }

    /**
     * Gets the value of required attribute of a given input field.
     * @param SimpleXMLElement $inputField
     * @return string
     */
    public function getInputFieldRequired($inputField) {
        $required = (string) $inputField->attributes()->required;
        return $required;
    }

    /**
     * Gets the value of name attribute of a given input field.
     * @param SimpleXMLElement $inputField
     * @return string
     */
    public function getInputFieldName($inputField) {
        $name = (string) $inputField->attributes()->name;
        return $name;
    }

    /**
     * Gets the the value of label attribute of a given input field.
     * @param SimpleXMLElement $inputField
     * @return string
     */
    public function getInputFieldLabel($inputField) {
        $label = (string) $inputField->attributes()->label;
        return $label;
    }

    /**
     * Resets input field count.
     */
    public function resetInputField() {
        $this->inputFieldCount = 0;
    }

    /**
     * Gets all input field names.
     * @return string[]
     * @throws ReportException
     */
    public function getInputFieldNameList() {
        $inputFieldNameList = array();
        $inputField = $this->getCurrentInputField();
        if (is_null($inputField)) {
            throw new ReportException("Invalid XML Definition!!! ( There is no input field element defined )");
        }

        while (!is_null($inputField)) {

            $inputFieldName = $this->getInputFieldName($inputField);
            if (is_null($inputField)) {
                throw new ReportException("Invalid XML Definition!!! ( Input Field attribute 'name' should be defined )");
            }

            $inputFieldNameList[] = $inputFieldName;
            $inputField = $this->getNextInputField();
        }

        return $inputFieldNameList;
    }

}
