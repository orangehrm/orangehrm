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
require_once ROOT_PATH."/lib/confs/Conf.php";

class BeaconDatapointDao extends BaseDao {

    /**
     * returns all the datapoints in the datapoint table
     * 
     * @return Doctrine_Collection Datapoint 
     * @throws DaoException
     */
    public function getAllDatapoints() {
        try {

            $query = Doctrine_Query::create()
                    ->from('DataPoint');

            return $query->execute();

            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }
    
    /**
     * return a doctrine collection containing a single record which is the 
     * datapoint type identified by the name string
     * 
     * @param string $name
     * @return Doctrine_Collection DatapointType
     * @throws DaoException
     */
    public function getDatapointTypeByName($name) {
        try {

            $query = Doctrine_Query::create()
                    ->from('DataPointType')
                    ->where('name = ?', $name);

            return $query->execute();

            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }
    
    /**
     * 
     * @param string $name
     * @return Doctrine_Collection DataPoint
     * @throws DaoException
     */
    public function getDatapointByName($name) {
        try {
            $query = Doctrine_Query::create()
                    ->from('DataPoint')
                    ->where('name = ?', $name);

            return $query->fetchOne();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    public function deleteDatapointByName($name) {
        try {

            $query = Doctrine_Query::create()
                    ->delete()
                    ->from('DataPoint')
                    ->where('name = ?', $name);

            return $query->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }
    
    public function getTableNames() {
        try {
            $tableList = array();
            $conf = new Conf();
            $dbName = $conf->dbname;
            $q = "show tables in `" . $dbName."`";
            $pdo = Doctrine_Manager::connection()->getDbh();
            $sth = $pdo->prepare($q);
            if ($sth->execute()) {
                $tableList = $sth->fetchAll(PDO::FETCH_COLUMN);
            }
            return $tableList;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

}
