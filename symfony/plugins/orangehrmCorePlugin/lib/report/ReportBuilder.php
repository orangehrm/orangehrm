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
 * Report Builder class 
 *
 */
class ReportBuilder {

    const LIST_SEPARATOR = "|\n|";

    private $reportId;
    private $offset;
    private $pageLimit;

    /**
     * build Report according report Definition
     * 
     * @param type $reportId
     * @return ResultSet
     */
    public function buildReport($reportId, $offset = NULL, $pageLimit = NULL, $values = array()) {

        $reportDefinitionObject = $this->getReportDefinition($reportId);
        $resultSetArray = array();

        $subReport = $reportDefinitionObject->getCurrentSubReport();
        if (is_null($subReport)) {
            throw new ReportException("Invalid XML Definition!!! ( At least one sub report should be defined )");
        }

        $firstSubQuery = TRUE;
        $firstSubQueryName = NULL;
        $firstSubQueryFieldValues = array();

        while (!is_null($subReport)) {
            $type = $reportDefinitionObject->getSubReportType($subReport);
            if (is_null($type) || empty($type)) {
                throw new ReportException("Invalid XML Definition!!! ( Sub report type should be defined )");
            }

            $subReportName = $reportDefinitionObject->getSubReportName($subReport);
            if (is_null($subReportName) || empty($subReportName)) {
                throw new ReportException("Invalid XML Definition!!! ( Sub report name should be defined )");
            }

            if ($type == "sql") {
                
                $filterValues = $values;
                
                if (!$firstSubQuery) {
                    // Get ID field values from first sub query.
                    $idField = $reportDefinitionObject->getSubReportIdField($subReport);
                    
                    if (!isset($firstSubQueryFieldValues[$idField])) {
                        
                        // Get the id field values from each subarray of the first sub query results:
                        $idFieldValues = array_map(function($item) use ($idField) {
                                return $item[$idField];
                            }, $resultSetArray[$firstSubQueryName]);
                            
                        $firstSubQueryFieldValues[$idField] = $idFieldValues;
                    }
                    $filterValues[$idField] = $firstSubQueryFieldValues[$idField];
                    
                    // Turn off offset, page limit for all queries other than the main query, since values of current
                    // page are already filtered.
                    $offset = NULL;
                    $pageLimit = NULL;
                        
                }
                $sqlDataGenerator = new SqlDataGenerator($reportDefinitionObject);
                $resultSetArray[$subReportName] = $sqlDataGenerator->getResultSet($offset, $pageLimit, $filterValues);


                // First subQuery is considered the main query
                if ($firstSubQuery) {
                    
                    // If first subquery is empty, no need to run other queries
                    if (empty($resultSetArray[$subReportName])) {
                        return $resultSetArray[$subReportName];
                    }
                    
                    $firstSubQuery = FALSE;
                    $firstSubQueryName = $subReportName;
                }

            }
            $subReport = $reportDefinitionObject->getNextSubReport();
        }

   
        if (count($resultSetArray) == 1) {
            $resultsSet = array_shift($resultSetArray);
        } else {
            $resultsSet = $this->mergeWithMultipleJoins($resultSetArray, $reportDefinitionObject);
        }

        $resultsSet = $this->buildResultsArray($resultsSet, $reportId);

        return $resultsSet;
    }

    /**
     * Sets report id
     * @param integer $reportId
     */
    public function setReportId($reportId) {
        $this->reportId = $reportId;
    }

    /**
     * Sets offset
     * @param integer $offset
     */
    public function setOffset($offset) {
        $this->offset = $offset;
    }

    /**
     * Sets page limit
     * @param integer $pageLimit
     */
    public function setPageLimit($pageLimit) {
        $this->pageLimit = $pageLimit;
    }

    /**
     * Gets report id
     * @return integer
     */
    public function getReportId() {
        return $this->reportId;
    }

    /**
     * Gets offset
     * @return integer
     */
    public function getOffset() {
        return $this->offset;
    }

    /**
     * Gets page limit
     * @return integer
     */
    public function getPageLimit() {
        return $this->pageLimit;
    }

    /**
     * Modifies the results in displayable format. For example : results of a 
     * group is a string, that string is built as an array. 
     * @param string[][] $resultsSet
     * @param integer $reportId
     * @return string[]
     */
    private function buildResultsArray($resultsSet, $reportId) {
        $allHeaders = $this->getHeaders($reportId);
        $groupHeaders = $this->getGroupMany($allHeaders);

        for ($dataRowNdx = 0; $dataRowNdx < count($resultsSet); $dataRowNdx++) {
            $dataRow = $resultsSet[$dataRowNdx];

            foreach ($groupHeaders as $headerName => $value) {
                $groupValue = $dataRow[$headerName];

                $rows = explode(self::LIST_SEPARATOR, $groupValue);
                $fieldValues = array();

                foreach ($rows as $row) {
                    $fieldValueNdx = 0;

                    $fields = explode('|^^|', $row);
                    foreach ($fields as $field) {
                        if (count($rows) != 1) {
                            $field = '• ' . $field;
                        }
                        $fieldValues[$fieldValueNdx][] = $field;
                        $fieldValueNdx++;
                    }
                }

                $resultsSet[$dataRowNdx][$headerName] = $fieldValues;
            }
        }

        return $resultsSet;
    }

    /**
     * Gets all headers 
     * 
     * @param integer $reportId
     * @return headers[](string array)
     * @throws ReportException
     */
    private function getHeaders($reportId) {
        $headers = array();
        $reportDefinition = $this->getReportDefinition($reportId);
        $reportDefinition->resetSubReport();

        $subReport = $reportDefinition->getCurrentSubReport();
        if (is_null($subReport)) {
            throw new ReportException("Invalid XML Definition!!! ( At least one sub report should be defined )");
        }

        while (!is_null($subReport)) {

            $displayGroups = $reportDefinition->getDisplayGroups($subReport);
            if (is_null($displayGroups)) {
                throw new ReportException("Invalid XML Definition!!! ( Display Groups should be defined )");
            }

            $reportDefinition->resetDisplayGroup();
            $displayGroup = $reportDefinition->getCurrentDisplayGroup($displayGroups);
            if (is_null($displayGroup)) {
                throw new ReportException("Invalid XML Definition!!! ( At least one display group should be defined )");
            }

            while (!is_null($displayGroup)) {

                $attributes = $reportDefinition->getDisplayGroupAttributes($displayGroup);
                $groupHeader = $reportDefinition->getGroupHeader($displayGroup);

                $headers[$attributes["name"]]["attr"] = array("type" => $attributes["type"],
                    "display" => $attributes["display"],
                    "header" => $groupHeader,
                    "showHeader" => isset($attributes["show_header"]) ? $attributes["show_header"] : null);

                $fields = array();

                $reportDefinition->resetField();
                $displayFields = $reportDefinition->getFields($displayGroup);
                if (is_null($displayFields)) {
                    throw new ReportException("Invalid XML Definition!!! ( Display Fields should be defined )");
                }

                $displayField = $reportDefinition->getCurrentField($displayFields);
                if (is_null($displayField)) {
                    throw new ReportException("Invalid XML Definition!!! ( At least one display field should be defined )");
                }

                while (!is_null($displayField)) {
                    $display = $reportDefinition->getFieldDisplayAttr($displayField);
                    if (is_null($display) || empty($display)) {
                        throw new ReportException('Invalid XML Definition!!! ( Field attribute "display" should be defined )');
                    }

                    $fieldAlias = $reportDefinition->getFieldAlias($displayField);
                    $displayFieldName = $reportDefinition->getFieldDisplayName($displayField);
                    if (is_null($displayFieldName) || empty($displayFieldName)) {
                        throw new ReportException("Invalid XML Definition!!! ( Field Display Name should be defined )");
                    }


                    $fieldWidth = $reportDefinition->getFieldWidth($displayField);
                    if (is_null($fieldWidth) || empty($fieldWidth)) {
                        throw new ReportException("Invalid XML Definition!!! ( Field Width should be defined )");
                    }

                    if ($fieldWidth == "") {
                        $fieldWidth = 150;
                    }

                    $link = $reportDefinition->getFieldLink($displayField);
                    $fieldData = array("display" => $display, "name" => $displayFieldName, "width" => $fieldWidth);
                    
                    if (isset($link)) {
                        $fieldData['link'] = $link;
                    }
                    $align = $reportDefinition->getFieldAlign($displayField);
                    if (isset($align)) {
                        $fieldData['align'] = $align;
                    }                    
                    
                    if ($fieldAlias != "") {
                        $fields[$fieldAlias] = $fieldData;
                    } else {
                        $fields[] = $fieldData;
                    }
                    $displayField = $reportDefinition->getNextField($displayFields);
                }

                $headers[$attributes["name"]]["fields"] = $fields;
                $displayGroup = $reportDefinition->getNextDisplayGroup($displayGroups);
            }

            $subReport = $reportDefinition->getNextSubReport();
        }

//        echo "<pre>"; print_r($headers); echo "</pre>";die;
        return $headers;
    }

    /**
     * Gets headers that are to be displayed in the report results table.
     * 
     * @param integer $reportId
     * @return headers[] (string[])
     */
    public function getDisplayHeaders($reportId) {
        $allHeaders = $this->getHeaders($reportId);
        $displayHeaders = array();

        foreach ($allHeaders as $groupHeader => $values) {
            if ($values["attr"]["display"] == "true") {
                
                if (is_null($values["attr"]["showHeader"]) || ($values["attr"]["showHeader"] == "false")) {
                    $displayHeaders[$groupHeader]["groupHeader"] = " ";
                } else {
                    $displayHeaders[$groupHeader]["groupHeader"] = $values["attr"]["header"];
                }
                
                foreach ($values["fields"] as $fieldKey => $fieldAttr) {
                    if ($fieldAttr["display"] == "true") {
                        $displayHeaders[$groupHeader][$fieldKey] = $fieldAttr["name"];
                    }
                }
            }
        }
//                echo "<pre>"; print_r($displayHeaders); echo "</pre>";die;
        return $displayHeaders;
    }

    /**
     * Gets meta information which is used when displaying headers,
     * about headers.
     * @param integer $reportId
     * @return string[]
     * @throws ReportException
     */
    public function getHeaderInfo($reportId) {
        $allHeaders = $this->getHeaders($reportId);
        $headerInfo = array();

        foreach ($allHeaders as $groupHeader => $values) {
            foreach ($values["fields"] as $fieldKey => $fieldAttr) {
                if (!array_key_exists($fieldKey, $headerInfo)) {

                    if ($values["attr"]["type"] == "one") {
                        $data = array("type" => $values["attr"]["type"],
                            "display" => $fieldAttr["display"],
                            "group" => $groupHeader,
                            "groupDisp" => $values["attr"]["display"],
                            "width" => $fieldAttr["width"],
                            "align" => isset($fieldAttr['align']) ? $fieldAttr['align'] : '');
                        if (isset($fieldAttr['link'])) {
                            $data['link'] = $fieldAttr['link'];
                        }
                        if (!empty($fieldAttr['align'])) {
                            $data['align'] = $fieldAttr['align'];
                        }                          
                        $headerInfo[$fieldKey] = $data;
                    } else if ($values["attr"]["type"] == "many") {
                        $data = array("type" => $values["attr"]["type"],
                            "display" => $fieldAttr["display"],
                            "group" => $groupHeader,
                            "groupDisp" => $values["attr"]["display"],
                            "width" => $fieldAttr["width"]);
                        if (isset($fieldAttr['link'])) {
                            $data['link'] = $fieldAttr['link'];
                        }            
                        if (!empty($fieldAttr['align'])) {
                            $data['align'] = $fieldAttr['align'];
                        }            
                        $headerInfo[$groupHeader][$fieldKey] = $data;
                    } else {
                        throw new ReportException("Invalid XML Definition!!! ( Invalid group type '" . $values["attr"]["type"] . "' )");
                    }
                }
            }
        }
//                        echo "<pre>"; print_r($headerInfo); echo "</pre>";die;
        return $headerInfo;
    }

    /**
     * Gets the total width of the report results table by summing the width of 
     * all the columns(display fields) and also gets the width of all the 
     * columns.
     * 
     * @param integer $reportId
     * @return array[][]
     */
    public function getTableWidth($reportId) {
        $allHeaders = $this->getHeaders($reportId);
        $tableWidthInfo = array();
        $tableWidth = 0;
        $columnWidth = array();

        foreach ($allHeaders as $groupHeader => $values) {
            foreach ($values["fields"] as $fieldKey => $fieldAttr) {
//                if (!array_key_exists($fieldKey, $tableWidth)) {
                if (($values["attr"]["display"] == "true") && ($fieldAttr["display"] == "true")) {
                    $tableWidth += $fieldAttr["width"] + 15;
                    $columnWidth[] = $fieldAttr["width"];
                }
//                }
            }
        }
        $tableWidthInfo["tableWidth"] = $tableWidth + 13;
        $tableWidthInfo["columnWidth"] = $columnWidth;
        return $tableWidthInfo;
    }

    /**
     * Extracts the groups that is of type many from the given array of groups.
     * 
     * @param array $groups
     * @return array[] (group)
     */
    private function getGroupMany($groups) {
        $groupMany = array();
        foreach ($groups as $group => $value) {
            $type = $value["attr"]["type"];
            if ($type == "many") {
                $groupMany[$group] = $value;
            }
        }

        return $groupMany;
    }

    /**
     * Gets report defintion for a given report Id.
     * 
     * @param integer $reportId
     * @return \ReportDefinition
     * @throws ReportException
     */
    public function getReportDefinition($reportId) {
        $report = $this->getReportById($reportId);
        if (!($report instanceof AdvancedReport)) {
            throw new ReportException("Report does not exist for report id '{$reportId}'");
        }

        $reportDefinitionXmlString = $report->getDefinition();
        $reportDefinitionObject = new ReportDefinition($reportDefinitionXmlString);
        return $reportDefinitionObject;
    }

    /**
     * Gets the number of Records for a given id and the results are filtered 
     * by the given values.
     * @param integer $reportId
     * @param string[]
     * @return integer
     */
    public function getNumOfRecords($reportId, $values = array()) {
        $reportDefinitionObject = $this->getReportDefinition($reportId);
        $sqlDataGenerator = new SqlDataGenerator($reportDefinitionObject);
        $numOfRecords = $sqlDataGenerator->getNumOfRecords($values);
        return $numOfRecords;
    }

    /**
     * Gets maximum page limit for a given report.
     * @param integer $reportId
     * @return integer
     * @throws ReportException
     */
    public function getMaxPageLimit($reportId) {
        $reportDefinitionObject = $this->getReportDefinition($reportId);
        $maxPageLimit = $reportDefinitionObject->getPageLimit();
        if (is_null($maxPageLimit) || empty($maxPageLimit)) {
            throw new ReportException("Invalid XML Definition!!! ( Page limit should be defined )");
        }
        return $maxPageLimit;
    }

    /**
     * Get Report Definition By Id
     * 
     * @param integer $reportId
     * @return AdvancedReport
     * 
     */
    private function getReportById($reportId) {
        $dao = new ReportDefinitionDao();
        $report = $dao->getReport($reportId);
        return $report;
    }

    /**
     * Merge sub report results that are defined in a single join element.
     * @param string[][] $resultsSetArray
     * @param ReportDefinition $reportDefinitionObject
     * @param SimpleXMLElement $join
     * @return string[]
     * @throws ReportException
     */
    private function mergeResultsUsingJoinBy($resultsSetArray, $reportDefinitionObject, $join) {
        $mergedArray = array();

        $reportDefinitionObject->resetJoinBy();

        $this->validateJoinById($resultsSetArray, $reportDefinitionObject, $join);

        $joinByCount = $reportDefinitionObject->getJoinByCount($join);

        if ($joinByCount != 1) {
            $joinBy = $reportDefinitionObject->getCurrentJoinBy($join);
            $subReportName = $reportDefinitionObject->getJoinBySubReport($joinBy);
            if (is_null($subReportName) || empty($subReportName)) {
                throw new ReportException("Invalid XML Definition!!! ( Join By Attribute 'sub_report' should be defined )");
            }

            $id1 = $reportDefinitionObject->getJoinById($joinBy);
            if (is_null($id1) || empty($id1)) {
                throw new ReportException("Invalid XML Definition!!! ( Join By Attribute 'id' should be defined )");
            }

            $mergedArray = $resultsSetArray[$subReportName];

            for ($i = 0; $i < ($joinByCount - 1); $i++) {
                $midResult = array();
                $joinBy = $reportDefinitionObject->getNextJoinBy($join);
                $subReportName = $reportDefinitionObject->getJoinBySubReport($joinBy);
                if (is_null($subReportName) || empty($subReportName)){
                    throw new ReportException("Invalid XML Definition!!! ( Join By Attribute 'sub_report' should be defined )");
                }

                $id2 = $reportDefinitionObject->getJoinById($joinBy);
                if (is_null($id2) || empty($id2)) {
                    throw new ReportException("Invalid XML Definition!!! ( Join By Attribute 'id' should be defined )");
                }

                $result = $resultsSetArray[$subReportName];
                
                foreach ($mergedArray as $row1) {
                    $matchFound = FALSE;
                    $keysTake = FALSE;
                    $row2Keys = array();
                        //If Result set is empty fill with null values 
                        if(count($result) == 0 ){
                            $fieldList = $reportDefinitionObject->getSubReportFields($subReportName);
                            $result = array(array_merge(array($id2=>$id1),$fieldList));
                        }
                       
                        
                        foreach ($result as $row2) {
                            if ($row1[$id1] == $row2[$id2]) {
                                $midResult[] = array_merge($row1, $row2);
                                $matchFound = TRUE;
                            }

                            if (!$keysTake) {
                                $row2Keys = array_keys($row2);
                                // remove id key
                                if(($idkey = array_search($id2, $row2Keys)) !== false) {
                                    unset($row2Keys[$idkey]);
                                }
                                $keysTake = TRUE;
                            }
                        }
                    if (!$matchFound) {
                        $midResult[] = array_merge($row1, array_fill_keys($row2Keys, null));
                    }
                }
                $mergedArray = $midResult;
            }
        } else if ($joinByCount == 1) {
            $mergedArray = $resultsSetArray;
        }
//var_dump($mergedArray);
        $reportDefinitionObject->resetJoinBy();
        return $mergedArray;
    }

    /**
     * Validates join by id attribute.
     * @param string[][] $resultSetArray
     * @param ReportDefinition $reportDefinitionObject
     * @param SimpleXMLElement $join
     * @throws ReportException
     */
    private function validateJoinById($resultSetArray, $reportDefinitionObject, $join) {
        $numberJoinBy = 0;
        $reportDefinitionObject->resetJoinBy();
        $joinBy = $reportDefinitionObject->getCurrentJoinBy($join);
        if (is_null($joinBy)) {
            throw new ReportException("Invalid XML Definition!!! ( At least two Join Bys should be defined )");
        }
        $numberJoinBy++;
        while (!is_null($joinBy)) {
            $subReportName = $reportDefinitionObject->getJoinBySubReport($joinBy);
            if (is_null($subReportName) || empty($subReportName)) {
                throw new ReportException("Invalid XML Definition!!! ( Join By Attribute 'sub_report' should be defined )");
            }

            $joinById = $reportDefinitionObject->getJoinById($joinBy);

            if (is_null($joinById) || empty($joinById)) {
                throw new ReportException("Invalid XML Definition!!! ( Join By Attribute 'id' should be defined )");
            }

            if (count($resultSetArray[$subReportName]) > 0 && !array_key_exists($joinById, $resultSetArray[$subReportName][0])) {
                $message = "The id field - " . $joinById . ' - is not a field in the report "' . $subReportName . '"';
                throw new ReportException($message);
            }
            $joinBy = $reportDefinitionObject->getNextJoinBy($join);
            $numberJoinBy++;
            if ((is_null($joinBy)) && ($numberJoinBy <= 2)) {
                throw new ReportException("Invalid XML Definition!!! ( At least two Join Bys should be defined )");
            }
        }
        $reportDefinitionObject->resetJoinBy();
    }

    /**
     * Merge results of multiple subreports. Set of sub reports result is given.
     * When merging this method uses join conditions defined in report definition, 
     * @param string[][] $resultsSetArray
     * @param ReportDefinition $reportDefinitionObject
     * @return string[]
     * @throws ReportException
     */
    private function mergeWithMultipleJoins($resultsSetArray, $reportDefinitionObject) {
        $result = array();
        $reportDefinitionObject->resetJoin();
        $join = $reportDefinitionObject->getCurrentJoin();
        if (is_null($join) || empty($join)) {
            throw new ReportException("Invalid XML Definition!!! 
                    ( The report should contain at least one join element, 
                    if there are more than one sub reports defined )");
        }

        $foundAJoinWithoutAsAttribute = false;

        $numOfJoins = $reportDefinitionObject->getJoinCount();

        for ($i = 0; $i < $numOfJoins; $i++) {

            $joinAs = $reportDefinitionObject->getJoinAs($join);

            if ($foundAJoinWithoutAsAttribute) {
                throw new ReportException("Invalid 'join' definition! refer to ReadMe file..");
            }

            if ($joinAs == "") {
                $foundAJoinWithoutAsAttribute = true;
            }

            $result = $this->mergeResultsUsingJoinBy($resultsSetArray, $reportDefinitionObject, $join);

            $resultsSetArray[$joinAs] = $result;
            $join = $reportDefinitionObject->getNextJoin();
        }

        $reportDefinitionObject->resetJoin();
        return $result;
    }

    /**
     * Traverse through the XML and builds headers that are defined to be 
     * displayed.
     * @param integer $reportId
     * @return string[][]
     * @throws ReportException
     */
    public function buildTableHeader($reportId) {

        $reportDefinitionObject = $this->getReportDefinition($reportId);
        $headerArray = array();

        $subReport = $reportDefinitionObject->getCurrentSubReport();
        if (is_null($subReport)) {
            throw new ReportException("There is no sub report defined in the XML");
        }

        while (!is_null($subReport)) {

            $reportDefinitionObject->resetDisplayGroup();
            $displayGroups = $reportDefinitionObject->getDisplayGroups($subReport);
            if (is_null($displayGroups)) {
                throw new ReportException("Invalid XML Definition!!! ( Display Groups should be defined )");
            }

            $displayGroup = $reportDefinitionObject->getCurrentDisplayGroup($displayGroups);
            if (is_null($displayGroup)) {
                throw new ReportException("Invalid XML Definition!!! ( At least one display group should be defined )");
            }

            while (!is_null($displayGroup)) {

                $display = $reportDefinitionObject->getDisplayGroupDisplayAttr($displayGroup);

                if ($display == "true") {
                    $groupHeader = $reportDefinitionObject->getGroupHeader($displayGroup);
                    if (is_null($groupHeader) || empty($groupHeader)) {
                        throw new ReportException("Invalid XML Definition!!! ( Group header should be defined )");
                    }

                    $reportDefinitionObject->resetField();
                    $fields = $reportDefinitionObject->getFields($displayGroup);
                    if (is_null($fields)) {
                        throw new ReportException("Invalid XML Definition!!! ( Display Fields should be defined )");
                    }

                    $field = $reportDefinitionObject->getCurrentField($fields);
                    if (is_null($field)) {
                        throw new ReportException("Invalid XML Definition!!! ( At least one field should be defined )");
                    }

                    while (!is_null($field)) {
                        $display = $reportDefinitionObject->getFieldDisplayAttr($field);
                        if (is_null($display) || empty($display)) {
                            throw new ReportException('Invalid XML Definition!!! ( Field attribute "display" should be defined )');
                        }

                        if ($display == "true") {
                            $displayFieldName = $reportDefinitionObject->getFieldDisplayName($field);
                            if (is_null($displayFieldName) || empty($displayFieldName)) {
                                throw new ReportException("Invalid XML Definition!!! ( Field Display Name should be defined )");
                            }

                            $headerArray[$groupHeader][] = $displayFieldName;
                        }
                        $field = $reportDefinitionObject->getNextField($fields);
                    }
                }

                $displayGroup = $reportDefinitionObject->getNextDisplayGroup($displayGroups);
            }

            $subReport = $reportDefinitionObject->getNextSubReport();
        }

        return $headerArray;
    }

    /**
     * Gets report name of a report given report id of that report.
     * 
     * @param integer $reportId
     * @return string
     */
    public function getReportName($reportId) {
        $dao = new ReportDefinitionDao();
        $report = $dao->getReport($reportId);
        $reportName = $report->getName();
        return $reportName;
    }
    
    /**
     * Replace the parameter holder with the value.
     * @param string $header
     * @param string[][] $formParam
     * @return string
     */
    public function replaceHeaderParam($header, $formParam){
        $pattern = '/\$P\{(.*?)\}/';

        $callback = function( $matches ) use ( $formParam ) {
                    $name = $matches[1];
                    
                    $valStr = is_array($formParam[$name]) ? implode(",", $formParam[$name]) : $formParam[$name];
                                     
                    return $valStr;
                };

        $str = preg_replace_callback($pattern, $callback, $header);

        return $str;
    }

}

