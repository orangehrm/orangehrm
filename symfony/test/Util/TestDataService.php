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

namespace OrangeHRM\Tests\Util;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\MappingException;
use Exception;
use OrangeHRM\Entity\UniqueId;
use OrangeHRM\ORM\Doctrine;
use PDO;
use Symfony\Component\Yaml\Yaml;

class TestDataService
{
    /** Encrypted fields in the format 
     *        array('Model1' => array(field1, field2), 
     *              'Model2' => array(field1, field2))        
     */
    private static $encryptedModels = []; //array('EmployeeSalary' => array('amount'));
    
    private static $dbConnection;
    private static $data;
    private static $tableNames;
    private static $lastFixture = null;
    private static $insertQueryCache = null;
    
    public static function populate($fixture) {
        $pathToFixtures = realpath($fixture);
        if (!$pathToFixtures) {
            throw new Exception(sprintf("Couldn't find fixture file in %s", $fixture));
        }
        self::_populateUsingPdoTransaction($pathToFixtures);
        //self::_populateUsingDoctrineObjects($fixture);
    }

    private static function _populateUsingDoctrineObjects($fixture) {

        self::_setData($fixture);

        self::_disableConstraints();

        self::_truncateTables();

        foreach (self::$data as $tableName => $tableData) {

            $count = 0;

            foreach ($tableData as $key => $dataRow) {

                $rowObject = new $tableName;
                $rowObject->fromArray($dataRow);
                $rowObject->save();

                $count++;
            }

            if ($count > 0) {
                self::adjustUniqueId($tableName, $count, true);
            }
        }

        self::_enableConstraints();
    }

    private static function _populateUsingPdoTransaction($fixture) {

        // Don't reload already loaded fixtures
        $useCache = true;
        if (self::$lastFixture != $fixture) {

            self::_setData($fixture);
            self::$lastFixture = $fixture;
            $useCache = false;
            self::$insertQueryCache = NULL;
        }

        self::_disableConstraints();

        self::_truncateTables();

        $pdo = self::_getDbConnection();
        $query = "";
        try {

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->beginTransaction();

            if ($useCache) {
                foreach (self::$insertQueryCache as $query) {
                    $pdo->exec($query);
                }
            } else {
                self::$insertQueryCache = array();

                foreach (self::$data as $tableName => $tableData) {

                    $queryArray = self::_generatePdoInsertQueryArray($tableName, $tableData);
                    self::$insertQueryCache = array_merge(self::$insertQueryCache, $queryArray);

                    foreach ($queryArray as $query) {
                        $pdo->exec($query);
                    }
                }
            }

            $pdo->commit();
        } catch (Exception $e) {

            $pdo->rollBack();
            echo __FILE__ . ':' . __LINE__ . "\n Transaction failed: " . $e->getMessage() .
            "\nQuery: [" . $query . "]\n" . "Fixture: " . $fixture . "\n\n";
        }

        self::_enableConstraints();
    }

    private static function _generatePdoInsertQueryArray($tableAlias, $tableData) {

        return self::_generateMultipleInsertQueryArray($tableAlias, $tableData);

        /* Multiple inserts have to be used since some fixtures contains different no
         * of columns for same data set */
    }

    public static function _generateMultipleInsertQueryArray($tableAlias, $tableData) {

        $tableName = self::_getTableName($tableAlias);
        $queryArray = array();

        if (!empty($tableData)) {
            foreach ($tableData as $item) {

                $columnString = self::_generateInsetQueryColumnString($item, $tableAlias);
                $queryArray[] = "INSERT INTO `$tableName` $columnString VALUES ('" . implode("', '", $item) . "')";
            }
        }

        $queryArray[] = "UPDATE `hs_hr_unique_id` SET `last_id` = " . count($tableData) . " WHERE `table_name` = '$tableName'";

        return $queryArray;
    }

    private static function _generateInsetQueryColumnString($dataArray, $tableAlias) {

        $columnString = "(";

        $count = count($dataArray);
        $i = 1;

        foreach ($dataArray as $key => $value) {

            $columnName = self::_getClassMetadata($tableAlias)->getColumnName($key);

            /* Had to remove backtick (`) since hs_hr_config's "key" column contains them */
            if ($i < $count) {
                $columnString .= "$columnName, ";
            } else {
                $columnString .= "$columnName";
            }

            $i++;
        }

        $columnString .= ")";

        return $columnString;
    }

    /**
     * @param string $tableAlias
     * @return string
     */
    private static function _getTableName(string $tableAlias): string
    {
        return self::_getClassMetadata($tableAlias)->getTableName();
    }

    /**
     * @param string $tableAlias
     * @return ClassMetadata
     */
    private static function _getClassMetadata(string $tableAlias): ClassMetadata
    {
        $entityName = self::getFQEntityName($tableAlias);
        return Doctrine::getEntityManager()->getClassMetadata($entityName);
    }

    public static function adjustUniqueId($tableName, $count, $isAlias = false) {

        if ($isAlias) {
            $tableName = self::_getTableName($tableName);
        }

        $q = Doctrine::getEntityManager()->createQueryBuilder();
        $q->from($tableName,'ui');
        $q->where('ui.table_name = :table_name');
        $q->setParameter('table_name',$tableName);

        $uniqueIdObject = $q->getQuery()->getOneOrNullResult();

        if ($uniqueIdObject instanceof UniqueId) {

            $uniqueIdObject->setLastId($count);
            Doctrine::getEntityManager()->persist($uniqueIdObject);
            Doctrine::getEntityManager()->flush();
        }
    }

    private static function _setData($fixture) {

        self::$data = Yaml::parseFile($fixture);

        self::_encryptFieldsInFixture();

        self::_setTableNames();
    }

    /**
     * If configured to encrypt data, encrypt fields in fixture.
     */
    private static function _encryptFieldsInFixture() {

        foreach (self::$encryptedModels as $model => $fields) {
            if (isset(self::$data[$model]) && \KeyHandler::keyExists()) {
                foreach (self::$data[$model] as $id => $row) {

                    foreach ($fields as $field) {
                        self::$data[$model][$id][$field] = \Cryptographer::encrypt($row[$field]);
                    }
                }
            }
        }
    }

    /**
     * @param string $className
     * @return string fully-qualified class name without a leading backslash
     */
    private static function getFQEntityName(string $className): string
    {
        if (class_exists($className)) {
            return $className;
        }
        return "OrangeHRM\Entity\\" . $className;
    }

    private static function _setTableNames(): void
    {
        foreach (self::$data as $key => $value) {
            try {
                self::$tableNames[] = self::_getTableName($key);
            } catch (MappingException $e) {
                echo __FILE__ . ':' . __LINE__ . ") Skipping unknown table alias: " . $key .
                    "\n" . "Fixture: " . self::$lastFixture . "\n\n";
            }
        }
    }

    private static function _disableConstraints() {

        $pdo = self::_getDbConnection();
        $pdo->exec("SET FOREIGN_KEY_CHECKS=0;");
    }

    private static function _enableConstraints() {
        $pdo = self::_getDbConnection();
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1;");
    }

    /**
     * @param array|null $tableNames
     * @throws MappingException
     */
    private static function _truncateTables(?array $tableNames = null): void
    {
        if (is_null($tableNames)) {
            $tableNames = self::$tableNames;
        }

        if (count($tableNames) > 0) {
            $pdo = self::_getDbConnection();
            self::_disableConstraints();
            $query = '';

            try {
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->beginTransaction();

                foreach ($tableNames as $tableName) {
                    $query = 'DELETE FROM ' . $tableName;
                    $pdo->query($query);
                    $query = 'ALTER TABLE `' . $tableName . '` AUTO_INCREMENT = 1';
                    $pdo->query($query);
                }

                $query = "UPDATE hs_hr_unique_id SET last_id = 0 WHERE table_name in ('" .
                    implode("','", $tableNames) . "')";
                $pdo->exec($query);

                $pdo->commit();
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
            } catch (Exception $e) {
                $pdo->rollBack();
                echo __FILE__ . ':' . __LINE__ . "\n Transaction failed: " . $e->getMessage() .
                    "\nQuery: [" . $query . "]\n" . "Fixture: " . self::$lastFixture . "\n\n";
            }

            // Clear table cache
            if (is_array(self::$data)) {
                foreach (self::$data as $alias => $values) {
                    Doctrine::getEntityManager()->clear(self::getFQEntityName($alias));
                }
            }

            self::_enableConstraints();
        }
    }

    /**
     *
     * @return PDO
     */
    private static function _getDbConnection() {

        if (empty(self::$dbConnection)) {

            self::$dbConnection = Doctrine::getEntityManager()->getConnection()->getWrappedConnection();

            return self::$dbConnection;
        } else {

            return self::$dbConnection;
        }
    }

    public static function truncateTables($aliasArray) {

        foreach ($aliasArray as $alias) {
            try {
                self::$tableNames[] = self::_getTableName($alias);
            } catch (MappingException $e) {
                echo __FILE__ . ':' . __LINE__ . ") Skipping unknown table alias: " . $alias . "\n";
            }
        }

        self::_truncateTables();
    }

    /**
     * @param array $aliasArray
     */
    public static function truncateSpecificTables(array $aliasArray): void
    {
        $tableNames = [];

        foreach ($aliasArray as $alias) {
            try {
                $tableNames[] = self::_getTableName($alias);
                Doctrine::getEntityManager()->clear(self::getFQEntityName($alias));
            } catch (MappingException $e) {
                echo __FILE__ . ':' . __LINE__ . ") Skipping unknown table alias: " . $alias . "\n";
            }
        }

        self::_truncateTables($tableNames);
    }

    public static function fetchLastInsertedRecords($alias, $count) {

        $entityName = self::getFQEntityName($alias);
        $wholeCount = Doctrine::getEntityManager()->getRepository($entityName)->count([]);
        $offset = $wholeCount - $count;

        $q = Doctrine::getEntityManager()->getRepository($entityName)->createQueryBuilder('a');
        $q->setFirstResult($offset);
        $q->setMaxResults($count);

        return $q->getQuery()->execute();
    }

    public static function fetchLastInsertedRecord($alias, $orderBy) {
        $entityName = self::getFQEntityName($alias);
        $q = Doctrine::getEntityManager()->getRepository($entityName)->createQueryBuilder('a');
        $q->setMaxResults(1);
        if (substr( $orderBy, 0, 2 ) !== "a.") {
            $orderBy = 'a.' .$orderBy;
        }
        $q->orderBy($orderBy,'DESC');

        return $q->getQuery()->getOneOrNullResult();
    }

    public static function fetchObject($alias, $primaryKey) {

        $entityName = self::getFQEntityName($alias);
        Doctrine::getEntityManager()->clear($entityName);
        return Doctrine::getEntityManager()->find($entityName,$primaryKey);
    }

    public static function loadObjectList($alias, $fixture, $key) {
        $data = Yaml::parseFile($fixture);

        return self::loadObjectListFromArray($alias, $data[$key]);
    }

    /**
     * @param string $alias
     * @param array|object[] $data
     * @return array|object[]
     */
    public static function loadObjectListFromArray(string $alias, array $data): array
    {
        $objectList = [];
        $classMetadata = self::_getClassMetadata($alias);

        foreach ($data as $row) {
            $entityName = self::getFQEntityName($alias);
            $object = new $entityName();

            foreach ($row as $attribute => $value) {
                $setMethodName = "set" . ucfirst($attribute);

                $fieldName = self::getFieldForColumn($classMetadata, $attribute);
                if ($fieldName) {
                    $associationMapping = self::getAssociationMapping($classMetadata, $fieldName);
                } else {
                    $associationMapping = self::getAssociationMapping($classMetadata, $attribute);
                }

                if ($associationMapping) {
                    $value = Doctrine::getEntityManager()->getReference($associationMapping['targetEntity'], $value);
                }

                if (!method_exists($object, $setMethodName)) {
                    if ($attribute) {
                        $setMethodName = "set" . ucfirst($fieldName);
                    }
                }
                $object->$setMethodName($value);
            }

            $objectList[] = $object;
        }

        return $objectList;
    }

    /**
     * @param ClassMetadata $classMetadata
     * @param string $columnName
     * @return string|null
     */
    public static function getFieldForColumn(ClassMetadata $classMetadata, string $columnName): ?string
    {
        try {
            return $classMetadata->getFieldForColumn($columnName);
        } catch (\Doctrine\ORM\Mapping\MappingException $e) {
            return null;
        }
    }

    /**
     * @param ClassMetadata $classMetadata
     * @param string $fieldName
     * @return array|null
     */
    public static function getAssociationMapping(ClassMetadata $classMetadata, string $fieldName): ?array
    {
        try {
            return $classMetadata->getAssociationMapping($fieldName);
        } catch (\Doctrine\ORM\Mapping\MappingException $e) {
            return null;
        }
    }

    public static function getRecords($query) {
        return self::_getDbConnection()->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

}

