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
 * Please refer http://www.orangehrm.com/Files/OrangeHRM_Commercial_License.pdf for the license which includes terms and conditions on using this software.
 *
 */
//<datapoint type = "count">
//<settings>
//        <name>user_count</name>
//    </settings>
//    <parameters>
//        <table>ohrm_user</table>
//        <alias></alias>
//        <join>
//        <type></type>
//          <table></table>
//          <alias></alias>
//          <on><left></left>
//          <right></right></on>
//          </join>
//        <where>
//        <connector>
//        <column></column>
//        <comparator></comparator>
//        <value> </value>
//        </where>
//        <groupby>
//        
//        </groupby>
//    </parameters>
//</datapoint>
class countDataPointProcessor extends AbstractBaseProcessor {

    public function sanitize($definition) {
        try {
            $datapoint = new SimpleXMLElement($definition);
            $isValid = true;
            $dataPointService = new BeaconDatapointService();
            if (!$dataPointService->checkTableNameExists($datapoint->parameters->table)) {
                $isValid = false;
            }

            return $isValid;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    public function process($definition) {
        if (!isset($definition)) {
            return null;
        }
        $result = null;
        try {


            $datapoint = new SimpleXMLElement($definition);
            if ($datapoint['type'] == 'count' && $this->sanitize($definition)) {

                try {
                    if (empty($datapoint->parameters->distinct)) {
                        $query = 'SELECT COUNT(*) FROM ' . $datapoint->parameters->table;
                    } else {
                        $query = "SELECT COUNT( DISTINCT `";
                        foreach ($datapoint->parameters->distinct as $distinct) {
                            $query.= $distinct . "` ,";
                        }
                        $query = substr($query, 0, -1); //remove last unnecessary comma
                        $query .= ") FROM " . $datapoint->parameters->table;
                    }

                    $query.= " ".$datapoint->parameters->alias . "";

                    if (count($datapoint->parameters->join) > 0) {
                        foreach ($datapoint->parameters->join as $joinClause) {
                            $query .= " " .$joinClause->type . " JOIN ";
                            $query .= $joinClause->table . " " . $joinClause->alias;
                            $query .= " on ".$joinClause->on->left . " = " . $joinClause->on->right;
                        }
                    }

                    if (count($datapoint->parameters->where) > 0) {
                        $query .= ' WHERE ';
                    }
                    $whereFilter = '';
                    foreach ($datapoint->parameters->where as $whereClause) {
                        $whereQuery = $whereClause->column . " " . $whereClause->operation . " ";

                        if ($whereClause->value . "" != null || $whereClause->value . "" != "") {
                            $whereQuery.= is_numeric($whereClause->value . "") ? $whereClause->value . "" : "'" . $whereClause->value . "'";
                        }
                        if ($whereClause->connector . "" == null || $whereClause->connector . "" == "") {
                            $whereFilter = $whereQuery . ' ' . $whereFilter;
                        } else {
                            $whereFilter .= " " . $whereClause->connector . "  " . $whereQuery;
                        }
                    }
                    $query = $query . $whereFilter;

                    if (!empty($datapoint->parameters->groupby)) {
                        $query .= " group by ".$datapoint->parameters->groupby;
                    }
                    
                    $count = $this->executeQuery($query);
                    // @codeCoverageIgnoreStart
                } catch (Exception $e) {
                    throw new DaoException($e->getMessage(), $e->getCode(), $e);
                }
                // @codeCoverageIgnoreEnd

                $name = $datapoint->settings->name;

                $result = $count[0];
            }
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
        return $result;
    }

    public function executeQuery($query) {
        
            $pdo = Doctrine_Manager::connection()->getDbh();
            $query = $pdo->prepare($query);
            $query->execute();
            $count = $query->fetch();
        
            return $count;
    }

}
