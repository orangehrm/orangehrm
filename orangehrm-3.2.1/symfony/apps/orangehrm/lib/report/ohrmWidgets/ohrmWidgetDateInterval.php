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
class ohrmWidgetDateInterval extends ohrmWidgetDateRange {
        /**
     * This method generates the where clause part.
     * @param string $fieldNames
     * @param string $value
     * @return string
     */
    public function generateWhereClausePart($fieldNames, $dateRanges) {

        $fromDate = "1970-01-01";
        $toDate = date("Y-m-d");

        $fieldArray = explode(",", $fieldNames);
        $field1 = $fieldArray[0];
        $field2 = $fieldArray[1];

        if (($dateRanges["from"] != "YYYY-MM-DD") && ($dateRanges["to"] != "YYYY-MM-DD")) {
            $fromDate = $dateRanges["from"];
            $toDate = $dateRanges["to"];
        } else if (($dateRanges["from"] == "YYYY-MM-DD") && ($dateRanges["to"] != "YYYY-MM-DD")) {
            $toDate = $dateRanges["to"];
        } else if (($dateRanges["from"] != "YYYY-MM-DD") && ($dateRanges["to"] == "YYYY-MM-DD")) {
            $fromDate = $dateRanges["from"];
        }

//        Case 1
        $sqlPartForField1 = "( " . $field1. " " . $this->getWhereClauseCondition() . " '" . $fromDate . "' AND '" . $toDate . "' )";
        $sqlPartForField2 = "( " . $field2. " " . $this->getWhereClauseCondition() . " '" . $fromDate . "' AND '" . $toDate . "' )";

        $sqlForCase1 = " ( " . $sqlPartForField1 . " AND " . $sqlPartForField2 . " ) ";

//        Case 2
        $sqlPartForField1 = " ( " . $field1 . " > '" . $fromDate . "' AND " . $field1 . " < '" . $toDate . "' ) " ;
        $sqlPartForField2 = " ( ".$field2 . " > '" . $toDate . "' ) ";

        $sqlForCase2 = " ( " .$sqlPartForField1 . " AND " . $sqlPartForField2 . " ) ";

//        Case 3
        $sqlPartForField1 = " ( " . $field1 . " < '" . $fromDate . "' ) ";
        $sqlPartForField2 = " ( " . $field2 . " > '" . $fromDate . "' AND " . $field2 . " < '" . $toDate . "' ) " ;

        $sqlForCase3 = " ( " .$sqlPartForField1 . " AND " . $sqlPartForField2 . " ) ";

        $sql = " ( " . $sqlForCase1 . " OR " . $sqlForCase2 . " OR " . $sqlForCase3 . " ) ";
        return $sql;
    }
}

